<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class AskAiController extends Controller
{
    /**
     * 会話画面表示
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Message::class); // 必要に応じて Policy 実装

        $userId = Auth::id();

        // 会話を取得 or 作成（タイトルは固定）
        $conv = Conversation::firstOrCreate(
            ['user_id' => $userId, 'title' => '彩さんとの相談'],
            [
                'system_prompt'     => null,
                'last_summary'      => null,
                'last_activity_at'  => now(),
            ]
        );

        // 直近 50 件まで（古い→新しい）
        $history = Message::where('conversation_id', $conv->id)
            ->orderBy('created_at')
            ->limit(50)
            ->get(['role', 'content']);

        // 最初の挨拶（200字要約を意識した軽いトーン）
        $greeting = $this->buildGreeting($conv);

        return view('ask.ai.index', [
            'history'  => $history,
            'greeting' => $greeting,
        ]);
    }

    /**
     * 質問受付（JSON 返却）
     */
    public function post(Request $request)
    {
        $userId = Auth::id();

        // 入力バリデーション & 正規化
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
        ]);
        $question = trim($validated['question']);

        // 会話を取得 or 作成
        $conv = Conversation::firstOrCreate(
            ['user_id' => $userId, 'title' => '彩さんとの相談'],
            [
                'system_prompt'     => null,
                'last_summary'      => null,
                'last_activity_at'  => now(),
            ]
        );

        // ユーザー質問を保存
        $userMsg = Message::create([
            'conversation_id' => $conv->id,
            'role'            => 'user',
            'content'         => $question,
        ]);

        // モデル呼び出し（OpenAI か、フォールバックのダミー応答）
        try {
            $answer = $this->generateAnswer($conv, $question);
        } catch (\Throwable $e) {
            Log::error('AI 呼び出し失敗: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $answer = '（現在混み合っているため、後でもう一度お試しください）';
        }

        // アシスタント回答を保存
        $asstMsg = Message::create([
            'conversation_id' => $conv->id,
            'role'            => 'assistant',
            'content'         => $answer,
        ]);

        // 会話メタ更新
        $conv->forceFill([
            'last_activity_at'            => now(),
            'last_summarized_message_id'  => $asstMsg->id,
        ])->save();

        return response()->json(['answer' => $answer]);
    }

    /**
     * 最初の挨拶テキスト生成（200字目安）
     */
    private function buildGreeting(Conversation $conv): string
    {
        if (!empty($conv->last_summary)) {
            return (string) Str::limit($conv->last_summary, 200, '…');
        }

        $base = "こんにちは、彩です。気軽に質問してください。要約や文章修正、Laravelの不具合調査、SQL最適化、学習計画づくりまでお手伝いします。"
              . "やりたいこと（目的）と今の状況（環境やエラー文など）を一言添えていただけると最短で解決に近づけます。";
        return (string) Str::limit($base, 200, '…');
    }

    /**
     * 回答生成（OpenAI を優先し、未設定時はフォールバック）
     */
    private function generateAnswer(Conversation $conv, string $question): string
    {
        $apiKey = (string) config('services.openai.key', env('OPENAI_API_KEY', ''));
        $model  = (string) config('services.openai.chat_model', env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'));
        $timeoutSec = (int) config('services.openai.timeout', 25);

        // OpenAI キー未設定ならフォールバック
        if ($apiKey === '') {
            return $this->fallbackAnswer($question);
        }

        // 会話履歴を直近 10 件だけ送る（漏洩/過剰送信防止）
        $recent = Message::where('conversation_id', $conv->id)
            ->orderByDesc('id')
            ->limit(10)
            ->get(['role', 'content'])
            ->reverse()
            ->values();

        $messages = [];

        // システムプロンプト（任意）
        $sys = $conv->system_prompt ?: "あなたは日本語で丁寧かつ簡潔に回答するアシスタントです。技術的な質問には最新のPHP/Laravel 11の文法で安全第一の提案を返し、"
            ."コードは必要最小限を示し、入力値検証とセキュリティ考慮（XSS/CSRF/SQLi/情報漏洩）を忘れず、曖昧な点は仮定を明示してください。";
        $messages[] = ['role' => 'system', 'content' => $sys];

        // 既存履歴
        foreach ($recent as $m) {
            $role = $m->role === 'user' ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => (string) $m->content];
        }

        // 今回の質問
        $messages[] = ['role' => 'user', 'content' => $question];

        // OpenAI Chat Completions 呼び出し
        $resp = Http::withToken($apiKey)
            ->timeout($timeoutSec)
            ->acceptJson()
            ->asJson()
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $model,
                'messages'    => $messages,
                'temperature' => 0.2,
                'max_tokens'  => 800,
            ]);

        if (!$resp->ok()) {
            $status = $resp->status();
            $body   = $resp->body();
            Log::warning("OpenAI API error: status={$status} body={$body}");
            return $this->fallbackAnswer($question);
        }

        $data = $resp->json();
        $answer = (string) ($data['choices'][0]['message']['content'] ?? '');
        $answer = trim($answer);

        if ($answer === '') {
            return $this->fallbackAnswer($question);
        }

        // 念のためサイズ制限
        return Str::limit($answer, 4000, '…');
    }

    /**
     * フォールバックの単純回答（API未設定/障害時）
     */
    private function fallbackAnswer(string $question): string
    {
        // ごく簡易に、質問を復唱＋ガイドを返す
        $q = Str::limit(preg_replace('/\s+/', ' ', $question), 120, '…');
        return "質問を受け取りました：{$q}\n\n"
            ."現在AIサービスの設定が未完了のため、簡易回答で対応しています。具体的なエラー文・期待動作・再現手順を教えていただければ、"
            ."修正すべきコードの最小差分で提案します。";
    }
}

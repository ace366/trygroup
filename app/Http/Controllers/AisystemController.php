<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\ConversationSummarizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AisystemController extends Controller
{
    public function index(Request $request, ConversationSummarizer $summarizer)
    {
        $userId = Auth::id();

        // 既存 or 新規作成（ユーザーにつき1本のメイン会話想定）
        $conv = Conversation::firstOrCreate(
            ['user_id' => $userId, 'title' => '彩さんとの相談'],
            ['last_activity_at' => now(), 'system_prompt' => null]
        );

        // 未要約 or 新規メッセージが増えていたら再要約（“遅延要約”）
        $latestMsgId = Message::where('conversation_id', $conv->id)->max('id');
        if (!$conv->last_summary || $conv->last_summarized_message_id !== $latestMsgId) {
            $result = $summarizer->summarize($conv->id);
            $conv->update([
                'last_summary' => $result['summary'],
                'last_summarized_message_id' => $result['last_id'],
            ]);
        }

        $greeting = "前回はこんなお話をしましたね：\n{$conv->last_summary}\n\n今日はどんなご相談ですか？";

        // 初期表示は履歴（一覧）と挨拶を渡すだけ
        $history = Message::where('conversation_id', $conv->id)->orderBy('id')->get();

        return view('ask-ai', compact('history','greeting','conv'));
    }

    public function ask(Request $request)
    {
        $request->validate([
            'question' => ['required','string','max:2000'],
        ]);

        $userId = Auth::id();
        $conv = Conversation::where('user_id',$userId)->latest('last_activity_at')->firstOrFail();
        $question = trim($request->input('question'));

        // APIには「人格」＋「要約」＋「今回の質問」だけ渡す（トーク節約）
        $persona = $conv->system_prompt ?: 'あなたは優しい女性の先生「彩さん」です。'
                 .'小中学生にもわかる言葉で、前向きに、根拠を添えて説明します。'
                 .'わからないときは無理に推測せず、確認質問をしてください。';

        $messages = [
            ['role'=>'system','content'=> $persona],
            ['role'=>'system','content'=> "前回までの相談内容の要約：\n{$conv->last_summary}"],
            ['role'=>'user','content'=> $question],
        ];

        $res = Http::withHeaders([
            'Authorization' => 'Bearer '.env('OPENAI_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => $messages,
            'max_tokens' => 700,
            'temperature' => 0.5,
        ]);
if (!$res->successful()) {
    // ★詳細をログへ
    \Log::error('OpenAI API error', [
        'status' => $res->status(),
        'body'   => $res->body(),
    ]);

    // よくあるHTTPステータスでメッセージを分岐
    $hint = match ($res->status()) {
        401 => 'APIキーが不正です（.env/OPENAI_API_KEY を確認）',
        429 => 'レート制限です（時間を空けるか上限を下げてください）',
        400, 404 => 'パラメータかエンドポイントの指定ミスの可能性',
        500, 502, 503 => 'OpenAI側の一時的障害。少し待って再試行してください。',
        default => '通信に失敗しました',
    };

    return response()->json([
        'answer' => "（回答エラー: {$hint}）",
    ], 200);
}

// 正常時のみパース
$answer = trim(data_get($res->json(), 'choices.0.message.content', ''));

if ($answer === '') {
    \Log::warning('OpenAI API empty content', ['json' => $res->json()]);
    return response()->json([
        'answer' => '（回答が空でした。プロンプトかモデル指定を見直してください）',
    ]);
}
        $answer = trim($res->json('choices.0.message.content') ?? '（回答の取得に失敗しました）');

        // フル履歴はDB保存（節約はAPI側のみ）
        Message::create(['conversation_id'=>$conv->id,'role'=>'user','content'=>$question]);
        Message::create(['conversation_id'=>$conv->id,'role'=>'assistant','content'=>$answer]);

        $conv->update(['last_activity_at'=>now()]);

        // 要約は次回 index 表示時に“遅延要約”で更新される
        return response()->json(['answer'=>$answer]);
    }
}

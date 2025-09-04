<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Http;

class ConversationSummarizer
{
    /**
     * 会話全文から日本語200字要約を生成
     * @return array{summary:string,last_id:int|null}
     */
    public function summarize(int $conversationId): array
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('id')->get();

        if ($messages->isEmpty()) {
            return ['summary' => '（まだ会話はありません）', 'last_id' => null];
        }

        // フルログを role 付きで結合
        $plain = $messages->map(fn($m) => "【{$m->role}】{$m->content}")->join("\n");

        $prompt = "以下は先生（assistant）と生徒（user）の会話ログです。"
                . "内容を日本語で200字程度に簡潔に要約してください。箇条書き禁止。"
                . "専門用語は平易に言い換えてください。\n\n".$plain;

        $res = Http::withHeaders([
            'Authorization' => 'Bearer '.env('OPENAI_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'あなたは優秀な要約アシスタントです。'],
                ['role' => 'user',   'content' => $prompt],
            ],
            'max_tokens' => 320,
            'temperature' => 0.2,
        ]);

        $summary = trim($res->json('choices.0.message.content') ?? '');
        // 念のため全角400バイト付近で丸める
        $summary = mb_strimwidth($summary, 0, 400, '');

        return ['summary' => $summary, 'last_id' => $messages->last()->id];
    }
}

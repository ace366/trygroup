<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LineWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // LINEからのデータを取得
        $data = $request->all();
        Log::info('LINE Webhook Data:', $data);

        // イベントを処理
        foreach ($data['events'] as $event) {
            if ($event['type'] === 'message') {
                $replyToken = $event['replyToken'];
                $messageText = $event['message']['text'];
                
                // LINEにメッセージを返信
                $this->replyMessage($replyToken, "受け取ったメッセージ: " . $messageText);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function replyMessage($replyToken, $message)
    {
        $accessToken = env('LINE_CHANNEL_ACCESS_TOKEN');

        $client = new \GuzzleHttp\Client();
        $client->post('https://api.line.me/v2/bot/message/reply', [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'replyToken' => $replyToken,
                'messages' => [
                    ['type' => 'text', 'text' => $message]
                ],
            ],
        ]);
    }
}

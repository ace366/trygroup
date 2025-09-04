<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LineWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // LINE����Υǡ��������
        $data = $request->all();
        Log::info('LINE Webhook Data:', $data);

        // ���٥�Ȥ����
        foreach ($data['events'] as $event) {
            if ($event['type'] === 'message') {
                $replyToken = $event['replyToken'];
                $messageText = $event['message']['text'];
                
                // LINE�˥�å��������ֿ�
                $this->replyMessage($replyToken, "������ä���å�����: " . $messageText);
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

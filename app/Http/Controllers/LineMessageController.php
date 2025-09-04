<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LineMessageController extends Controller
{
    public function showForm()
    {
        return view('line.message');
    }

    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $response = Http::withToken(env('LINE_CHANNEL_ACCESS_TOKEN'))
            ->post('https://api.line.me/v2/bot/message/push', [
                'to' => $request->user_id,
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => $request->message,
                    ]
                ],
            ]);

        if ($response->successful()) {
            return back()->with('success', '送信成功しました');
        } else {
            return back()->with('error', '送信失敗: ' . $response->body());
        }
    }
}

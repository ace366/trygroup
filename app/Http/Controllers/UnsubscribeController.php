<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnsubscribeController extends Controller
{
    // 確認ページ（リンク先）
    public function show(Request $request)
    {
        return view('unsubscribe.confirmed', [
            'email' => (string) $request->query('e', ''),
        ]);
    }

    // ワンクリック解除（RFC8058対応：GET/POST両対応）
    public function oneClick(Request $request)
    {
        $email = (string) ($request->query('e', '') ?: $request->input('e', ''));
        $token = (string) ($request->query('t', '') ?: $request->input('t', ''));

        if ($email === '' || $token === '') {
            return response('Bad Request', 400);
        }

        $secret = (string) config('app.key', '');
        // APP_KEY が base64: の場合でもそのまま HMAC のキーとして利用可
        $expected = hash_hmac('sha256', $email.'|unsubscribe', $secret);

        if (!hash_equals($expected, $token)) {
            return response('Unauthorized', 401);
        }

        // 解除登録（重複は無視）
        DB::table('mail_unsubscribes')->updateOrInsert(
            ['email' => mb_strtolower($email)],
            ['reason' => 'one-click', 'created_at' => now(), 'updated_at' => now()]
        );

        // 解除完了を返す（Gmailの自動アクセス想定でシンプルに）
        return response('Unsubscribed', 200);
    }
}

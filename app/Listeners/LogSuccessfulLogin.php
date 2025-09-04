<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // ✅ ログイン回数をカウント
        $user->increment('login_count');

        // ✅ ログイン成功ログ
        Log::info("User {$user->id} ({$user->email}) has logged in. Login count: {$user->login_count}");

        // ✅ 指定のページにリダイレクト
        session(['url.intended' => route('welcome')]);
    }
}

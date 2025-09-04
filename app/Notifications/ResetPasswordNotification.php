<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('【よりE土曜塾】パスワード再設定のご案内')
            ->line('以下のリンクをクリックしてパスワードを再設定してください。')
            ->action('パスワードを再設定する', url(config('app.url') . route('password.reset', $this->token, false)))
            ->line('このリンクは60分後に無効になります。')
            ->line('よりE土曜塾'); // ← フッターの「Regards, ～」を好きに変えられる！
    }
}

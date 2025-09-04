<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $bodyText;
    public string $toEmail;
    public ?int $userId;
    public int $batchId;

    public function __construct(string $subject, string $body, string $toEmail, ?int $userId, int $batchId)
    {
        $this->subjectLine = $subject;
        $this->bodyText    = $body;
        $this->toEmail     = $toEmail;
        $this->userId      = $userId;
        $this->batchId     = $batchId;
    }

    public function build()
    {
        // From を確実に決定（mail.from が無ければ smtp.username を使用）
        $fromAddress = (string) (config('mail.from.address') ?: config('mail.mailers.smtp.username', ''));
        $fromName    = (string) (config('mail.from.name') ?: config('app.name', 'App'));

        // 解除URL（RFC8058 One-Click と表示用ページ）
        $base = rtrim((string) config('app.url'), '/');
        $token = hash_hmac('sha256', $this->toEmail.'|unsubscribe', (string) config('app.key', ''));
        $unsubscribeOneClick = $base . '/unsubscribe/one-click?e=' . rawurlencode($this->toEmail) . '&t=' . $token;
        $unsubscribePageUrl  = $base . '/unsubscribe?e=' . rawurlencode($this->toEmail);

        // HTML本文を生成（自動リンク対応）
        $html = $this->renderHtml($this->bodyText, $unsubscribePageUrl);

        return $this->from($fromAddress, $fromName)
            ->subject($this->subjectLine)
            ->priority(1) // 高優先度ヘッダ
            ->withSymfonyMessage(function (\Symfony\Component\Mime\Email $message) use ($unsubscribeOneClick) {
                $headers = $message->getHeaders();

                // Date ヘッダ（JST）
                if ($headers->has('Date')) {
                    $headers->remove('Date');
                }
                $headers->addDateHeader('Date', new \DateTimeImmutable('now', new \DateTimeZone(config('app.timezone', 'Asia/Tokyo'))));

                // リスト系ヘッダ（Gmail要件対応）
                $fromForList = config('mail.from.address') ?: 'noreply@localhost';
                $headers->addTextHeader('List-Unsubscribe', "<{$unsubscribeOneClick}>, <mailto:{$fromForList}?subject=unsubscribe>");
                $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
                $headers->addTextHeader('List-ID', 'YoriE Saturday School <mails.r7yorii.top-ace-picard.com>');

                // X-Mailer
                if ($headers->has('X-Mailer')) {
                    $headers->remove('X-Mailer');
                }
                $headers->addTextHeader('X-Mailer', 'YoriE Laravel Mailer');
            })
            // マルチパート（text/plain と text/html）
            ->text('emails.bulk_plain', [
                'subjectLine' => $this->subjectLine,
                'bodyText'    => $this->bodyText,
            ])
            ->html($html);
    }

    private function renderHtml(string $plain, string $unsubscribePageUrl): string
    {
        $safe = e($plain);

        // URL自動リンク（http/https, www.*）とメールアドレス自動リンク
        // 1) http/https
        $safe = preg_replace('~(?<!")\bhttps?://[^\s<]+~u', '<a href="$0" rel="noopener noreferrer" target="_blank">$0</a>', $safe);
        // 2) www. から始まるURL（プロトコル補完してリンク化）
        $safe = preg_replace('~(?<!://)(?<!@)\bwww\.[^\s<]+~u', '<a href="http://$0" rel="noopener noreferrer" target="_blank">$0</a>', $safe);
        // 3) メールアドレス
        $safe = preg_replace('~\b[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}\b~iu', '<a href="mailto:$0">$0</a>', $safe);

        $safe = nl2br($safe, false);
        $subject = e($this->subjectLine);

        return <<<HTML
<!doctype html>
<html lang="ja">
<head><meta charset="utf-8"><title>{$subject}</title></head>
<body>
<div style="font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;line-height:1.7;color:#111;word-break:break-word;overflow-wrap:anywhere;">
{$safe}
<hr style="border:none;border-top:1px solid #e5e7eb;margin:16px 0;">
<div style="font-size:12px;color:#6b7280;">
このメールは「よりE土曜塾」からの一括ご案内です。<br>
配信停止：<a href="{$unsubscribePageUrl}" target="_blank" rel="nofollow noopener">こちら</a>
</div>
</div>
</body>
</html>
HTML;
    }
}

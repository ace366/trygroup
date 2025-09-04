<?php

namespace App\Jobs;

use App\Mail\BulkNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendBulkMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $recipientEmail;
    public string $subject;
    public string $body;
    public int $batchId;
    public ?int $userId;

    public $timeout = 120;

    public function __construct(string $recipientEmail, string $subject, string $body, int $batchId, ?int $userId = null)
    {
        $this->recipientEmail = $recipientEmail;
        $this->subject = $subject;
        $this->body = $body;
        $this->batchId = $batchId;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        Mail::to($this->recipientEmail)->send(new BulkNotificationMail(
            subject: $this->subject,
            body: $this->body
        ));

        DB::table('bulk_mail_recipients')
            ->where('batch_id', $this->batchId)
            ->where('email', $this->recipientEmail)
            ->update([
                'status' => 'sent',
                'sent_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function failed(\Throwable $e): void
    {
        DB::table('bulk_mail_recipients')
            ->where('batch_id', $this->batchId)
            ->where('email', $this->recipientEmail)
            ->update([
                'status' => 'failed',
                'error_message' => mb_substr($e->getMessage(), 0, 1000),
                'updated_at' => now(),
            ]);
    }
}

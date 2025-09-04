<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // バッチ（1回の送信操作ごと）
        Schema::create('bulk_mail_batches', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 150);
            $table->text('body_encrypted'); // Crypt::encryptString(body)
            $table->text('filters_json')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 受信者ログ
        Schema::create('bulk_mail_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email', 320);
            $table->enum('status', ['queued','sent','failed'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['batch_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_mail_recipients');
        Schema::dropIfExists('bulk_mail_batches');
    }
};

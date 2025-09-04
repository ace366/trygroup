<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('conversations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->onDelete('cascade');
            $t->string('title')->nullable();               // Ǥ��: ����̾�ʤ�
            $t->text('system_prompt')->nullable();         // Ǥ��: �̤���οͳʸ���
            $t->text('last_summary')->nullable();          // �ǿ�200������
            $t->unsignedBigInteger('last_summarized_message_id')->nullable();
            $t->timestamp('last_activity_at')->nullable();
            $t->timestamps();
            $t->index(['user_id','last_activity_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('conversations');
    }
};

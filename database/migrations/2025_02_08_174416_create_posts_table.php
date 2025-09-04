<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // 投稿者のID
            $table->string('title'); // 投稿のタイトル
            $table->text('content'); // 投稿の内容
            $table->timestamps();

            // 外部キー制約（ユーザーが削除されたら投稿も削除される）
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('posts');
    }
};

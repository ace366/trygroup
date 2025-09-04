<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('last_name'); // 姓
            $table->string('first_name'); // 名
            $table->string('last_name_kana'); // かな（姓）
            $table->string('first_name_kana'); // かな（名）
            $table->string('school'); // 中学校名
            $table->string('grade'); // 学年
            $table->string('class'); // 組
            $table->string('phone'); // 電話番号
            $table->string('lesson_type'); // 受講形式
            $table->string('eiken'); // 英検参加
            $table->string('email')->unique();
            $table->text('other')->nullable(); // その他（4行テキスト）
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name', 'first_name', 'last_name_kana', 'first_name_kana',
                'school', 'grade', 'class', 'phone', 'lesson_type', 'eiken', 'other'
            ]);
        });
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('ユーザーID');
            $table->string('class_name')->comment('参加したクラス');
            $table->timestamp('joined_at')->useCurrent()->comment('参加日時');
        });
    }

    public function down(): void {
        Schema::dropIfExists('attendance_logs');
    }
};

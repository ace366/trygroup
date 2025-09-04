<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('physical_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('ユーザーID');
            $table->string('venue_name')->comment('会場名');
            $table->timestamp('attended_at')->useCurrent()->comment('出席日時');
        });
    }

    public function down(): void {
        Schema::dropIfExists('physical_attendance_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shared_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_name'); // 表示名
            $table->string('file_path'); // 物理パス（public ディスク相対）
            $table->enum('type', ['file', 'folder'])->default('file');
            $table->foreignId('parent_id')->nullable()->constrained('shared_files')->cascadeOnDelete();
            $table->boolean('is_protected')->default(false);
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_files');
    }
};

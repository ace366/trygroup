<?php
// database/migrations/2025_09_01_100100_create_clients_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_code', 8)->unique(); // 自動採番 (ゼロ埋め)
            $table->string('client_name', 100);         // 受託元名
            $table->foreignId('base_id')
                  ->constrained('client_bases')         // 拠点マスタ参照
                  ->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();                      // 誤削除防止
            $table->index(['client_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

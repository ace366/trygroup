<?php
// database/migrations/2025_09_01_100000_create_client_bases_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_bases', function (Blueprint $table) {
            $table->id();
            $table->string('base_code', 8)->unique();   // 例: 0003
            $table->string('base_name', 100);           // 例: 関東
            $table->timestamps();
            $table->softDeletes();                      // 誤削除防止
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_bases');
    }
};

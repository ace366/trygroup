<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);              // 事業名
            $table->text('description')->nullable();  // 説明
            $table->foreignId('fiscal_year_id')       // 会計年度ID
                  ->constrained('fiscal_years')
                  ->cascadeOnDelete();
            $table->date('contract_start')->nullable(); // 契約開始
            $table->date('contract_end')->nullable();   // 契約終了
            $table->enum('type', ['venue','individual']); // 区分
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 既存テーブルに fiscal_year_id が無い場合のみ追加
        if (!Schema::hasColumn('projects', 'fiscal_year_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->unsignedBigInteger('fiscal_year_id')->after('description');
                // SQLite は ALTER での外部キー追加をサポートしないため index のみにする
                $table->index('fiscal_year_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('projects', 'fiscal_year_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropIndex(['fiscal_year_id']);
                $table->dropColumn('fiscal_year_id');
            });
        }
    }
};

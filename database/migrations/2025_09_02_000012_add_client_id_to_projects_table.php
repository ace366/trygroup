<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('projects', 'client_id')) {
            Schema::table('projects', function (Blueprint $table) {
                // ★ SQLite対策：NOT NULL では追加できないため nullable で追加
                $table->unsignedBigInteger('client_id')->nullable()->after('fiscal_year_id');
                $table->index('client_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('projects', 'client_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropIndex(['client_id']);
                $table->dropColumn('client_id');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guidances', function (Blueprint $table) {
            $table->boolean('homework_flag')->default(false)->after('homework'); // 宿題フラグ
        });
    }

    public function down(): void
    {
        Schema::table('guidances', function (Blueprint $table) {
            $table->dropColumn('homework_flag');
        });
    }
};

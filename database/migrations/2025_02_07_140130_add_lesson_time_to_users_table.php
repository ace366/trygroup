<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'lesson_time')) {
                $table->string('lesson_time')->nullable()->after('lesson_type')->comment('受講時間（午前/午後）');
            }
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'lesson_time')) {
                $table->dropColumn('lesson_time');
            }
        });
    }
};

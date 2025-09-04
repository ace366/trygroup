<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_name_kana')) {
                $table->string('last_name_kana')->nullable();
            }
            if (!Schema::hasColumn('users', 'first_name_kana')) {
                $table->string('first_name_kana')->nullable();
            }
            if (!Schema::hasColumn('users', 'school')) {
                $table->string('school')->nullable();
            }
            if (!Schema::hasColumn('users', 'grade')) {
                $table->string('grade')->nullable();
            }
            if (!Schema::hasColumn('users', 'class')) {
                $table->string('class')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'lesson_type')) {
                $table->string('lesson_type')->nullable();
            }
            if (!Schema::hasColumn('users', 'eiken')) {
                $table->string('eiken')->nullable();
            }
            if (!Schema::hasColumn('users', 'other')) {
                $table->text('other')->nullable();
            }
        });
        
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name', 'first_name', 'last_name_kana', 'first_name_kana',
                'school', 'grade', 'class', 'phone', 'lesson_type', 'eiken', 'other'
            ]);
        });
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'login_count')) { // ✅ すでに存在しない場合のみ追加
                $table->integer('login_count')->default(0)->after('role')->comment('ログイン回数');
            }
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'login_count')) {
                $table->dropColumn('login_count');
            }
        });
    }
};

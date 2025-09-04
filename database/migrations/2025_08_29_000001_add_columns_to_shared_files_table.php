<?php
// database/migrations/2025_08_29_000001_add_columns_to_shared_files_table.php
// 「shared_files」に 'type' が無くて落ちているため、必要列を後付けします（SQLite対応）

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shared_files', function (Blueprint $table) {
            // SQLite でも安全に後付けできるよう、存在チェックして個別追加
            if (!Schema::hasColumn('shared_files', 'type')) {
                // enum は SQLite だと厳しいため string で代替（'file' / 'folder'）
                $table->string('type', 10)->default('file');
            }
            if (!Schema::hasColumn('shared_files', 'parent_id')) {
                // 外部キー制約は後付けしない（SQLite配慮）。必要なら将来テーブル再作成で対応
                $table->unsignedBigInteger('parent_id')->nullable()->index();
            }
            if (!Schema::hasColumn('shared_files', 'is_protected')) {
                $table->boolean('is_protected')->default(false);
            }
            if (!Schema::hasColumn('shared_files', 'password')) {
                $table->string('password')->nullable();
            }
        });

        // 既存レコードの 'type' を補完
        if (Schema::hasColumn('shared_files', 'type')) {
            DB::table('shared_files')->whereNull('type')->update(['type' => 'file']);
        }
    }

    public function down(): void
    {
        // SQLite は dropColumn が難しいため、MySQL/PostgreSQL のときのみ実行
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('shared_files', function (Blueprint $table) {
                if (Schema::hasColumn('shared_files', 'password'))    $table->dropColumn('password');
                if (Schema::hasColumn('shared_files', 'is_protected')) $table->dropColumn('is_protected');
                if (Schema::hasColumn('shared_files', 'parent_id'))    $table->dropColumn('parent_id');
                if (Schema::hasColumn('shared_files', 'type'))         $table->dropColumn('type');
            });
        }
    }
};

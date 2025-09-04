<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // 事業名
            $table->string('name')->index();

            // 外部コード（スクショの「外部コード」に相当）
            $table->string('external_code', 64)->nullable()->index();

            // 契約期間
            $table->date('contract_start')->index();
            $table->date('contract_end')->index();

            // 受託元（相手先名）
            $table->string('contractor_name')->nullable()->index();

            // 公開状況：true=公開中 / false=非公開
            $table->boolean('is_published')->default(false)->index();

            // 事業タイプ：venue（会場型） / individual（個別対応型）
            $table->string('project_type', 16)->index(); // 'venue' or 'individual'

            // 年度（期間情報は fiscal_years に保持）
            $table->foreignId('fiscal_year_id')
                  ->constrained('fiscal_years')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->timestamps();

            // 期間検索高速化
            $table->index(['contract_start', 'contract_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

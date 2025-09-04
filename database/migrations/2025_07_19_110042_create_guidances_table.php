<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guidances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('users')->onDelete('cascade'); // 生徒（role = user）
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // 講師（role = teacher）

            $table->date('registered_at')->default(DB::raw('CURRENT_DATE')); // 登録日（デフォルト本日）

            $table->enum('course_type', ['土曜塾', '英検対策']);
            $table->enum('time_zone', ['午前', '午後']);

            // グループ（コースによって異なる）
            $table->string('group');

            $table->enum('subject', ['国語', '数学', '英語']);
            $table->string('unit')->nullable(); // 単元
            $table->text('content')->nullable(); // 指導内容

            $table->enum('school_name', ['寄居中学校', '城南中学校', '男衾中学校']);
            
            $table->tinyInteger('understanding_level')->unsigned()->nullable(); // 1〜5
            $table->tinyInteger('concentration_level')->unsigned()->nullable(); // 1〜5

            $table->text('attitude')->nullable();     // 授業態度/雰囲気（4行程度）
            $table->text('homework')->nullable();     // 宿題内容

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guidances');
    }
};

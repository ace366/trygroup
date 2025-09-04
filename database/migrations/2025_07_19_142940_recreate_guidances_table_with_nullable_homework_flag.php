<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // �Ť��ơ��֥���͡���
        Schema::rename('guidances', 'guidances_old');

        // ���ơ��֥����
        Schema::create('guidances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->date('registered_at')->default(DB::raw('CURRENT_DATE'));
            $table->string('course_type');
            $table->string('time_zone');
            $table->string('group');
            $table->string('subject');
            $table->string('unit')->nullable();
            $table->text('content')->nullable();
            $table->string('school_name');
            $table->tinyInteger('understanding_level')->nullable();
            $table->tinyInteger('concentration_level')->nullable();
            $table->text('attitude')->nullable();
            $table->text('homework')->nullable();
            $table->boolean('homework_flag')->nullable(); // �� NULL���Ƥ��ѹ�
            $table->timestamps();
        });

        // �ǡ����ܹԡʵ�ơ��֥뤫�鿷�ơ��֥�ء�
        DB::statement('
            INSERT INTO guidances (
                id, student_id, teacher_id, registered_at, course_type, time_zone, "group", subject,
                unit, content, school_name, understanding_level, concentration_level,
                attitude, homework, homework_flag, created_at, updated_at
            )
            SELECT
                id, student_id, teacher_id, registered_at, course_type, time_zone, "group", subject,
                unit, content, school_name, understanding_level, concentration_level,
                attitude, homework, NULL, created_at, updated_at
            FROM guidances_old
        ');

        // �Ť��ơ��֥���
        Schema::drop('guidances_old');
    }

    public function down(): void
    {
        Schema::dropIfExists('guidances');
    }
};

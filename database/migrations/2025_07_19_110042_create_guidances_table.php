<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guidances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('users')->onDelete('cascade'); // ���̡�role = user��
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // �ֻա�role = teacher��

            $table->date('registered_at')->default(DB::raw('CURRENT_DATE')); // ��Ͽ���ʥǥե����������

            $table->enum('course_type', ['���˽�', '�Ѹ��к�']);
            $table->enum('time_zone', ['����', '���']);

            // ���롼�סʥ������ˤ�äưۤʤ��
            $table->string('group');

            $table->enum('subject', ['���', '����', '�Ѹ�']);
            $table->string('unit')->nullable(); // ñ��
            $table->text('content')->nullable(); // ��Ƴ����

            $table->enum('school_name', ['�����ع�', '������ع�', '������ع�']);
            
            $table->tinyInteger('understanding_level')->unsigned()->nullable(); // 1��5
            $table->tinyInteger('concentration_level')->unsigned()->nullable(); // 1��5

            $table->text('attitude')->nullable();     // ��������/ʷ�ϵ���4�����١�
            $table->text('homework')->nullable();     // ��������

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guidances');
    }
};

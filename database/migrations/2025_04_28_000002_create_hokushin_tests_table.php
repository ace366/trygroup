<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hokushin_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('grade');
            $table->tinyInteger('exam_number');
            $table->integer('japanese');
            $table->integer('math');
            $table->integer('english');
            $table->integer('science');
            $table->integer('social');
            $table->integer('three_subjects_total');
            $table->integer('five_subjects_total');
            $table->decimal('japanese_deviation', 5, 2)->nullable();
            $table->decimal('math_deviation', 5, 2)->nullable();
            $table->decimal('english_deviation', 5, 2)->nullable();
            $table->decimal('science_deviation', 5, 2)->nullable();
            $table->decimal('social_deviation', 5, 2)->nullable();
            $table->decimal('three_subjects_deviation', 5, 2)->nullable();
            $table->decimal('five_subjects_deviation', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hokushin_tests');
    }
};

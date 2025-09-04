<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regular_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('grade');
            $table->tinyInteger('semester');
            $table->string('test_type', 20);
            $table->integer('japanese');
            $table->integer('math');
            $table->integer('english');
            $table->integer('science');
            $table->integer('social');
            $table->integer('three_subjects_total');
            $table->integer('five_subjects_total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regular_tests');
    }
};

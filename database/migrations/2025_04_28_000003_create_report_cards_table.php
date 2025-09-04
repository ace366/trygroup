<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('grade');
            $table->tinyInteger('semester');
            $table->tinyInteger('japanese');
            $table->tinyInteger('math');
            $table->tinyInteger('english');
            $table->tinyInteger('science');
            $table->tinyInteger('social');
            $table->tinyInteger('pe'); // 体育
            $table->tinyInteger('music');
            $table->tinyInteger('home_economics'); // 家庭科
            $table->tinyInteger('technology'); // 技術
            $table->integer('three_subjects_total');
            $table->integer('five_subjects_total');
            $table->integer('nine_subjects_total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'grade',
        'exam_number',
        'japanese',
        'math',
        'english',
        'science',
        'social',
        'three_subjects_total',
        'five_subjects_total',
        'japanese_deviation',
        'math_deviation',
        'english_deviation',
        'science_deviation',
        'social_deviation',
        'three_subjects_deviation',
        'five_subjects_deviation',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

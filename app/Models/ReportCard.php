<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'grade',
        'semester',
        'japanese',
        'math',
        'english',
        'science',
        'social',
        'pe',
        'music',
        'home_economics',
        'technology',
        'three_subjects_total',
        'five_subjects_total',
        'nine_subjects_total',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

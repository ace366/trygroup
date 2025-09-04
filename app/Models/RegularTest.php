<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegularTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'grade',
        'semester',
        'test_type',
        'japanese',
        'math',
        'english',
        'science',
        'social',
        'three_subjects_total',
        'five_subjects_total',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

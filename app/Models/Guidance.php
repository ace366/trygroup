<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guidance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'registered_at',
        'course_type',
        'time_zone',
        'group',
        'subject',
        'unit',
        'content',
        'school_name',
        'understanding_level',
        'concentration_level',
        'attitude',
        'homework',
        'homework_flag',
    ];

    // ���̡�users�ơ��֥��role = 'user'��
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // �ֻա�users�ơ��֥��role = 'teacher'��
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}

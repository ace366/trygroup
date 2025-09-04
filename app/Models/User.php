<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'last_name',
        'first_name',
        'last_name_kana',
        'first_name_kana',
        'school',
        'grade',
        'class',
        'phone',
        'lesson_type',
        'lesson_time',
        'eiken',
        'email',
        'other',
        'password',
        'role',
        'login_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function regularTests()
    {
        return $this->hasMany(\App\Models\RegularTest::class, 'student_id');
    }
    public function hokushinTests()
    {
        return $this->hasMany(HokushinTest::class, 'student_id');
    }
    public function mockTests()
    {
        return $this->hasMany(\App\Models\MockTest::class, 'student_id');
    }
}

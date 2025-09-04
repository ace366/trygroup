<?php

// app/Models/LineUser.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineUser extends Model
{
    protected $fillable = ['line_user_id', 'last_message'];
}
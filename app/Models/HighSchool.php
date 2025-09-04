<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HighSchool extends Model
{
    protected $fillable = [
        'id',
        'name',
        'department',
        'deviation_value',
        'school_report_ratio',
        'selection_criteria_url',
        'has_school_selection_exam',
        'created_at',
        'updated_at',
        'type',
    ];

    public $timestamps = false;
}


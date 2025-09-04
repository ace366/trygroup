<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

class SharedFile extends Model
{
    protected $fillable = [
        'user_id', 'file_name', 'file_path', 'type', 'parent_id', 'is_protected', 'password',
    ];

    protected $casts = [
        'is_protected' => 'bool',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}

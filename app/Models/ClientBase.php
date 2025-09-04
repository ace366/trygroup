<?php
// app/Models/ClientBase.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientBase extends Model
{
    use SoftDeletes;

    protected $fillable = ['base_code', 'base_name'];

    public function clients()
    {
        return $this->hasMany(Client::class, 'base_id');
    }
}

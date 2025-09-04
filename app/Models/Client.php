<?php
// app/Models/Client.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = ['client_code', 'client_name', 'base_id'];

    public function base()
    {
        return $this->belongsTo(ClientBase::class, 'base_id');
    }
}

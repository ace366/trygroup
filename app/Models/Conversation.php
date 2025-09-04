<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'user_id','title','system_prompt','last_summary',
        'last_summarized_message_id','last_activity_at'
    ];
    protected $casts = ['last_activity_at' => 'datetime'];

    public function messages(): HasMany {
        return $this->hasMany(Message::class);
    }
}

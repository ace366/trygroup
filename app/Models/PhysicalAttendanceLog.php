<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalAttendanceLog extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'venue_name', 'attended_at'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

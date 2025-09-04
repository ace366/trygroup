<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhysicalAttendance;
use Illuminate\Support\Facades\Auth;

class PhysicalAttendanceController extends Controller
{
    public function logEntry()
    {
        // 出席を記録
        PhysicalAttendance::create([
            'user_id' => Auth::id(),
            'attendance_time' => now(),
        ]);

        return back()->with('success', '出席が記録されました');
    }
}

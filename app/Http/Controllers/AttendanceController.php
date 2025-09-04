<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Schedule;

class AttendanceController extends Controller
{
    // ✅ 出席簿一覧を表示する
    public function index(Request $request)
    {
        $query = Attendance::join('users', 'attendances.user_id', '=', 'users.id')
            ->where('users.role', 'user')
            ->selectRaw('
                users.id as user_id,
                users.last_name, users.first_name,
                users.last_name_kana, users.first_name_kana,
                users.school, users.grade, users.eiken,
                attendances.attendance_type, attendances.class,
                MAX(attendances.attendance_time) as attendance_time
            ')
            ->groupBy(
                'users.id', 'users.last_name', 'users.first_name',
                'users.last_name_kana', 'users.first_name_kana',
                'users.school', 'users.grade', 'users.eiken',
                'attendances.attendance_type', 'attendances.class'
            );

        // ✅ 14日以内 or 指定日
        if ($request->filled('date')) {
            $query->whereDate('attendances.attendance_time', $request->date);
        } else {
            $query->where('attendances.attendance_time', '>=', now()->subDays(14));
        }

        if ($request->filled('attendance_type')) {
            $query->where('attendances.attendance_type', $request->attendance_type);
        }

        $attendances = $query->orderBy('attendance_time', 'desc')->get();

        $attendances->transform(function ($attendance) {
            $attendance->attendance_time = Carbon::parse($attendance->attendance_time);
            return $attendance;
        });

        return view('attendance.index', compact('attendances'));
    }

    // ✅ Zoom 入室時の出席記録
    public function logAttendance(Request $request)
    {
        $user = Auth::user();

        // 出席登録
        Attendance::create([
            'user_id' => $user->id,
            'class' => $request->class,
            'attendance_type' => $request->attendance_type,
            'attendance_time' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'redirect_url' => $request->zoom_url
        ]);
    }

    // ✅ QRコードスキャン画面を表示
    public function scan()
    {
        return view('attendance.scan');
    }

    // ✅ 出席登録（QRコードスキャン）
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_type' => 'required|in:online,physical'
        ]);

        $user = User::find($request->user_id);

        Attendance::create([
            'user_id' => $user->id,
            'attendance_type' => $request->attendance_type,
            'attendance_time' => now(),
            'class' => $request->class,
        ]);

        return response()->json([
            'success' => true,
            'user_name' => $user->last_name
        ]);
    }

    // ✅ 出席を記録 + Zoom リダイレクト（JSONレスポンス）
    public function logEntry(Request $request, $class)
    {
        Attendance::create([
            'user_id' => Auth::id(),
            'class' => $request->class,
            'attendance_type' => $request->attendance_type ?? 'online',
            'attendance_time' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'redirect_url' => $request->zoom_url
        ]);
    }

    // ✅ 出席分析（オンライン + 対面午前 + 対面午後）
    public function analysis()
    {
        $scheduleDates = Schedule::orderBy('date')->pluck('date')->toArray();

        $dates = [];
        $onlineCounts = [];
        $physicalMorningCounts = [];
        $physicalAfternoonCounts = [];

        foreach ($scheduleDates as $date) {
            $dates[] = $date;

            // オンライン
            $onlineCounts[] = Attendance::whereDate('attendance_time', $date)
                ->where('attendance_type', 'online')
                ->distinct('user_id')
                ->count('user_id');

            // 対面（午前）
            $physicalMorningCounts[] = Attendance::whereDate('attendance_time', $date)
                ->where('attendance_type', 'physical')
                ->whereTime('attendance_time', '<', '12:00:00')
                ->distinct('user_id')
                ->count('user_id');

            // 対面（午後）
            $physicalAfternoonCounts[] = Attendance::whereDate('attendance_time', $date)
                ->where('attendance_type', 'physical')
                ->whereTime('attendance_time', '>=', '12:00:00')
                ->distinct('user_id')
                ->count('user_id');
        }

        return view('attendance.analysis', compact(
            'dates',
            'onlineCounts',
            'physicalMorningCounts',
            'physicalAfternoonCounts'
        ));
    }
}

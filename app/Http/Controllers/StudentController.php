<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
class StudentController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'teacher'])) {
            abort(403, '権限がありません');
        }

        $filter = $request->input('filter', 'today'); 
        $grade = $request->input('grade');
        $school = $request->input('school');

        // 出席者ID（今日）
        $todayIds = Attendance::whereDate('attendance_time', Carbon::today())
            ->pluck('user_id')->unique();

        // 生徒一覧取得
        $studentsQuery = User::with(['regularTests', 'hokushinTests'])
            ->select(['id', 'last_name', 'first_name', 'school', 'grade', 'class', 'memo'])
            ->withCount([
               'regularTests as has_regular' => function ($q) {
                  $q->whereNotNull('japanese'); // 任意条件（点数が入ってるもののみ）
               },
               'hokushinTests as has_hokushin' => function ($q) {
                  $q->whereNotNull('japanese');
               }
            ])
            ->where('role', 'user')
            ->orderBy('grade', 'desc') // ✅ 学年で降順に並べる
            ->orderByRaw('(has_regular + has_hokushin) desc');
        if ($filter === 'today') {
            $studentsQuery->whereIn('id', $todayIds);
        }

        if ($filter === 'scored') {
            // 一旦すべて取得して後でフィルタ
            $students = $studentsQuery->get()->filter(function ($student) {
                return $student->regularTests->isNotEmpty() || $student->hokushinTests->isNotEmpty();
            })->values();
        } else {
            $students = $studentsQuery->get();
        }

        // フィルタ適用（grade / school） ※表示中のデータに対して絞り込み
        if ($request->filled('grade')) {
            $students = $students->where('grade', $grade)->values();
        }
        if ($request->filled('school')) {
            $students = $students->where('school', $school)->values();
        }

        // 学校選択肢（プルダウン用）
        $schoolOptions = User::where('role', 'user')
            ->whereNotNull('school')
            ->distinct()
            ->pluck('school')->sort()->values();

        return view('students.index', compact('students', 'schoolOptions'));
    }
    public function dashboard($studentId)
    {
        $student = User::findOrFail($studentId);

        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '自分以外の成績にはアクセスできません');
        }

        return view('students.dashboard', compact('student'));
    }
    public function updateMemo(Request $request, User $student)
    {
        if (!in_array(auth()->user()->role, ['admin', 'teacher'])) {
            abort(403);
        }

        $request->validate([
            'memo' => 'nullable|string|max:1000',
        ]);

        $student->memo = $request->memo;
        $student->save();

        return response()->json(['message' => 'メモを保存しました']);
    }
}

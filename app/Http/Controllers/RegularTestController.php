<?php

namespace App\Http\Controllers;

use App\Models\RegularTest;
use App\Models\User;
use Illuminate\Http\Request;

class RegularTestController extends Controller
{
    public function index($studentId)
    {
        $student = User::findOrFail($studentId);
        // 🔥 userの場合は「自分の成績」以外見れない
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $tests = RegularTest::where('student_id', $studentId)
            ->orderBy('grade')     // 学年
            ->orderBy('semester')  // 学期
            ->orderBy('test_type') // テスト種別（中間・期末）
            ->get();
        return view('regular_tests.index', compact('student', 'tests'));
    }

    public function create($studentId)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        return view('regular_tests.create', compact('student'));
    }

    public function store(Request $request, $studentId)
    {
        $student = User::findOrFail($studentId);

        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $validated = $request->validate([
            'grade' => 'required|integer|min:1|max:3',
            'semester' => 'required|integer|min:1|max:3',
            'test_type' => 'required|string|max:20',
            'japanese' => 'required|integer|min:0|max:100',
            'math' => 'required|integer|min:0|max:100',
            'english' => 'required|integer|min:0|max:100',
            'science' => 'required|integer|min:0|max:100',
            'social' => 'required|integer|min:0|max:100',
        ]);

        $validated['student_id'] = $studentId;
        $validated['three_subjects_total'] = $validated['japanese'] + $validated['math'] + $validated['english'];
        $validated['five_subjects_total'] = $validated['three_subjects_total'] + $validated['science'] + $validated['social'];

        RegularTest::create($validated);

        return redirect()->route('regular-tests.index', $studentId)->with('success', '定期テストを登録しました');
    }

    public function edit($studentId, $id)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $test = RegularTest::where('student_id', $studentId)->findOrFail($id);

        return view('regular_tests.edit', compact('student', 'test'));
    }

    public function update(Request $request, $studentId, $id)
    {
        $student = User::findOrFail($studentId);

        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $validated = $request->validate([
            'grade' => 'required|integer|min:1|max:3',
            'semester' => 'required|integer|min:1|max:2',
            'test_type' => 'required|string|max:20',
            'japanese' => 'required|integer|min:0|max:100',
            'math' => 'required|integer|min:0|max:100',
            'english' => 'required|integer|min:0|max:100',
            'science' => 'required|integer|min:0|max:100',
            'social' => 'required|integer|min:0|max:100',
        ]);

        $test = RegularTest::where('student_id', $studentId)->findOrFail($id);

        $validated['three_subjects_total'] = $validated['japanese'] + $validated['math'] + $validated['english'];
        $validated['five_subjects_total'] = $validated['three_subjects_total'] + $validated['science'] + $validated['social'];

        $test->update($validated);

        return redirect()->route('regular-tests.index', $studentId)->with('success', '定期テストを更新しました');
    }

    public function destroy($studentId, $id)
    {
        $student = User::findOrFail($studentId);

        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $test = RegularTest::where('student_id', $studentId)->findOrFail($id);
        $test->delete();

        return redirect()->route('regular-tests.index', $studentId)->with('success', '定期テストを削除しました');
    }
}

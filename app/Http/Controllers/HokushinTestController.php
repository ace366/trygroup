<?php

namespace App\Http\Controllers;

use App\Models\HokushinTest;
use App\Models\User;
use Illuminate\Http\Request;

class HokushinTestController extends Controller
{
    public function index($studentId)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $tests = HokushinTest::where('student_id', $studentId)->orderBy('grade')->orderBy('exam_number')->get();

        return view('hokushin_tests.index', compact('student', 'tests'));
    }

    public function create($studentId)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        return view('hokushin_tests.create', compact('student'));
    }

    public function store(Request $request, $studentId)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $validated = $request->validate([
            'grade' => 'required|integer|min:1|max:3',
            'exam_number' => 'required|integer|min:1|max:8',
            'japanese' => 'required|integer|min:0|max:100',
            'math' => 'required|integer|min:0|max:100',
            'english' => 'required|integer|min:0|max:100',
            'science' => 'required|integer|min:0|max:100',
            'social' => 'required|integer|min:0|max:100',
            'japanese_deviation' => 'nullable|numeric|between:20,80',
            'math_deviation' => 'nullable|numeric|between:20,80',
            'english_deviation' => 'nullable|numeric|between:20,80',
            'science_deviation' => 'nullable|numeric|between:20,80',
            'social_deviation' => 'nullable|numeric|between:20,80',
        ]);

        $validated['student_id'] = $studentId;

        // 3教科合計
        $validated['three_subjects_total'] = $validated['japanese'] + $validated['math'] + $validated['english'];
        $validated['five_subjects_total'] = $validated['three_subjects_total'] + $validated['science'] + $validated['social'];

        // 3教科偏差値平均（北辰のみ偏差値あり）
        if (isset($validated['japanese_deviation'], $validated['math_deviation'], $validated['english_deviation'])) {
            $validated['three_subjects_deviation'] = round(
                ($validated['japanese_deviation'] + $validated['math_deviation'] + $validated['english_deviation']) / 3,
                2
            );
        }

        if (isset($validated['japanese_deviation'], $validated['math_deviation'], $validated['english_deviation'], $validated['science_deviation'], $validated['social_deviation'])) {
            $validated['five_subjects_deviation'] = round(
                ($validated['japanese_deviation'] + $validated['math_deviation'] + $validated['english_deviation'] + $validated['science_deviation'] + $validated['social_deviation']) / 5,
                2
            );
        }

        HokushinTest::create($validated);

        return redirect()->route('hokushin-tests.index', $studentId)->with('success', '北辰テストを登録しました');
    }

    public function edit($studentId, $id)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $test = HokushinTest::where('student_id', $studentId)->findOrFail($id);

        return view('hokushin_tests.edit', compact('student', 'test'));
    }

    public function update(Request $request, $studentId, $id)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $validated = $request->validate([
            'grade' => 'required|integer|min:1|max:3',
            'exam_number' => 'required|integer|min:1|max:8',
            'japanese' => 'required|integer|min:0|max:100',
            'math' => 'required|integer|min:0|max:100',
            'english' => 'required|integer|min:0|max:100',
            'science' => 'required|integer|min:0|max:100',
            'social' => 'required|integer|min:0|max:100',
            'japanese_deviation' => 'nullable|numeric|between:20,80',
            'math_deviation' => 'nullable|numeric|between:20,80',
            'english_deviation' => 'nullable|numeric|between:20,80',
            'science_deviation' => 'nullable|numeric|between:20,80',
            'social_deviation' => 'nullable|numeric|between:20,80',
        ]);

        $test = HokushinTest::where('student_id', $studentId)->findOrFail($id);

        $validated['three_subjects_total'] = $validated['japanese'] + $validated['math'] + $validated['english'];
        $validated['five_subjects_total'] = $validated['three_subjects_total'] + $validated['science'] + $validated['social'];

        if (isset($validated['japanese_deviation'], $validated['math_deviation'], $validated['english_deviation'])) {
            $validated['three_subjects_deviation'] = round(
                ($validated['japanese_deviation'] + $validated['math_deviation'] + $validated['english_deviation']) / 3,
                2
            );
        }

        if (isset($validated['japanese_deviation'], $validated['math_deviation'], $validated['english_deviation'], $validated['science_deviation'], $validated['social_deviation'])) {
            $validated['five_subjects_deviation'] = round(
                ($validated['japanese_deviation'] + $validated['math_deviation'] + $validated['english_deviation'] + $validated['science_deviation'] + $validated['social_deviation']) / 5,
                2
            );
        }

        $test->update($validated);

        return redirect()->route('hokushin-tests.index', $studentId)->with('success', '北辰テストを更新しました');
    }

    public function destroy($studentId, $id)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $test = HokushinTest::where('student_id', $studentId)->findOrFail($id);
        $test->delete();

        return redirect()->route('hokushin-tests.index', $studentId)->with('success', '北辰テストを削除しました');
    }
}

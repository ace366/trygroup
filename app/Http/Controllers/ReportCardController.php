<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function index($studentId)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $reports = ReportCard::where('student_id', $studentId)->orderBy('grade')->orderBy('semester')->get();

        return view('report_cards.index', compact('student', 'reports'));
    }

    public function create($studentId)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        return view('report_cards.create', compact('student'));
    }

    public function store(Request $request, $studentId)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $validated = $request->validate([
            'grade' => 'required|integer|min:1|max:3',
            'semester' => 'required|integer|min:1|max:4',
            'japanese' => 'required|integer|min:1|max:5',
            'math' => 'required|integer|min:1|max:5',
            'english' => 'required|integer|min:1|max:5',
            'science' => 'required|integer|min:1|max:5',
            'social' => 'required|integer|min:1|max:5',
            'pe' => 'required|integer|min:1|max:5',
            'music' => 'required|integer|min:1|max:5',
            'home_economics' => 'required|integer|min:1|max:5',
            'technology' => 'required|integer|min:1|max:5',
        ]);

        $validated['student_id'] = $studentId;

        // 合計計算
        $validated['three_subjects_total'] = $validated['japanese'] + $validated['math'] + $validated['english'];
        $validated['five_subjects_total'] = $validated['three_subjects_total'] + $validated['science'] + $validated['social'];
        $validated['nine_subjects_total'] = $validated['five_subjects_total'] + $validated['pe'] + $validated['music'] + $validated['home_economics'] + $validated['technology'];

        ReportCard::create($validated);

        return redirect()->route('report-cards.index', $studentId)->with('success', '内申点を登録しました');
    }

    public function edit($studentId, $id)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $report = ReportCard::where('student_id', $studentId)->findOrFail($id);

        return view('report_cards.edit', compact('student', 'report'));
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
            'japanese' => 'required|integer|min:1|max:5',
            'math' => 'required|integer|min:1|max:5',
            'english' => 'required|integer|min:1|max:5',
            'science' => 'required|integer|min:1|max:5',
            'social' => 'required|integer|min:1|max:5',
            'pe' => 'required|integer|min:1|max:5',
            'music' => 'required|integer|min:1|max:5',
            'home_economics' => 'required|integer|min:1|max:5',
            'technology' => 'required|integer|min:1|max:5',
        ]);

        $report = ReportCard::where('student_id', $studentId)->findOrFail($id);

        $validated['three_subjects_total'] = $validated['japanese'] + $validated['math'] + $validated['english'];
        $validated['five_subjects_total'] = $validated['three_subjects_total'] + $validated['science'] + $validated['social'];
        $validated['nine_subjects_total'] = $validated['five_subjects_total'] + $validated['pe'] + $validated['music'] + $validated['home_economics'] + $validated['technology'];

        $report->update($validated);

        return redirect()->route('report-cards.index', $studentId)->with('success', '内申点を更新しました');
    }

    public function destroy($studentId, $id)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $report = ReportCard::where('student_id', $studentId)->findOrFail($id);
        $report->delete();

        return redirect()->route('report-cards.index', $studentId)->with('success', '内申点を削除しました');
    }
}

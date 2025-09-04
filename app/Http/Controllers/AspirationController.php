<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Models\User;
use Illuminate\Http\Request;

class AspirationController extends Controller
{
    public function index($studentId)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $aspiration = Aspiration::where('student_id', $studentId)->first();

        return view('aspirations.index', compact('student', 'aspiration'));
    }

    public function create($studentId)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        return view('aspirations.create', compact('student'));
    }

    public function store(Request $request, $studentId)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $validated = $request->validate([
            'first_choice' => 'required|string|max:255',
            'second_choice' => 'nullable|string|max:255',
            'third_choice' => 'nullable|string|max:255',
            'fourth_choice' => 'nullable|string|max:255',
        ]);

        $validated['student_id'] = $studentId;

        Aspiration::create($validated);

        return redirect()->route('aspirations.index', $studentId)->with('success', '志望校を登録しました');
    }

    public function edit($studentId, $id)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $aspiration = Aspiration::where('student_id', $studentId)->findOrFail($id);

        return view('aspirations.edit', compact('student', 'aspiration'));
    }

    public function update(Request $request, $studentId, $id)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $validated = $request->validate([
            'first_choice' => 'required|string|max:255',
            'second_choice' => 'nullable|string|max:255',
            'third_choice' => 'nullable|string|max:255',
            'fourth_choice' => 'nullable|string|max:255',
        ]);

        $aspiration = Aspiration::where('student_id', $studentId)->findOrFail($id);
        $aspiration->update($validated);

        return redirect()->route('aspirations.index', $studentId)->with('success', '志望校を更新しました');
    }

    public function destroy($studentId, $id)
    {
        $student = User::findOrFail($studentId);
        if (auth()->user()->role === 'user' && auth()->id() != $studentId) {
            abort(403, '他の生徒の成績にはアクセスできません');
        }
        $aspiration = Aspiration::where('student_id', $studentId)->findOrFail($id);
        $aspiration->delete();

        return redirect()->route('aspirations.index', $studentId)->with('success', '志望校を削除しました');
    }
}

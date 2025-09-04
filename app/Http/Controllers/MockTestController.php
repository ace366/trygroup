<?php

namespace App\Http\Controllers;

use App\Models\MockTest;
use App\Models\User;
use Illuminate\Http\Request;


class MockTestController extends Controller
{
    public function index()
    {
        $mockTests = MockTest::with('student')->latest()->paginate(10);
        return view('mock_tests.index', compact('mockTests'));
    }

    public function create(User $student)
    {
        return view('mock_tests.create', compact('student'));
    }

    public function store(Request $request, User $student)
    {
        $deviationRules = [
            'japanese_deviation' => 'nullable|numeric',
            'math_deviation' => 'nullable|numeric',
            'english_deviation' => 'nullable|numeric',
            'science_deviation' => 'nullable|numeric',
            'social_deviation' => 'nullable|numeric',
            'three_subjects_deviation' => 'nullable|numeric',
            'five_subjects_deviation' => 'nullable|numeric',
        ];

        $data = $request->validate(array_merge([
            'grade' => 'required|integer',
            'exam_number' => 'required|integer',
            'japanese' => 'required|integer',
            'math' => 'required|integer',
            'english' => 'required|integer',
            'science' => 'required|integer',
            'social' => 'required|integer',
            'three_subjects_total' => 'required|integer',
            'five_subjects_total' => 'required|integer',
        ], $deviationRules));

        $data['student_id'] = $student->id;

        MockTest::create($data);
        return redirect()->route('students.mock_tests.index', $student->id)->with('success', 'ÅĞÏ¿´°Î»');
    }


    public function edit(MockTest $mockTest)
    {
        $students = User::where('role', 'user')->get();
        return view('mock_tests.edit', compact('mockTest', 'students'));
    }

    public function update(Request $request, MockTest $mockTest)
    {
        $deviationRules = [
            'japanese_deviation' => 'nullable|numeric',
            'math_deviation' => 'nullable|numeric',
            'english_deviation' => 'nullable|numeric',
            'science_deviation' => 'nullable|numeric',
            'social_deviation' => 'nullable|numeric',
            'three_subjects_deviation' => 'nullable|numeric',
            'five_subjects_deviation' => 'nullable|numeric',
        ];

        $data = $request->validate(array_merge([
            'student_id' => 'required|exists:users,id',
            'grade' => 'required|integer',
            'exam_number' => 'required|integer',
            'japanese' => 'required|integer',
            'math' => 'required|integer',
            'english' => 'required|integer',
            'science' => 'required|integer',
            'social' => 'required|integer',
            'three_subjects_total' => 'required|integer',
            'five_subjects_total' => 'required|integer',
        ], $deviationRules));

        $mockTest->update($data);

        return redirect()->route('students.mock_tests.index', $data['student_id'])->with('success', '¹¹¿·´°Î»');
    }
    public function destroy(MockTest $mockTest)
    {
        $studentId = $mockTest->student_id;

        $mockTest->delete();

        return redirect()->route('students.mock_tests.index', $studentId)->with('success', 'ºï½ü´°Î»');
    }
    public function indexByStudent(User $student)
    {
        $mockTests = $student->mockTests()->latest()->get();

        return view('mock_tests.index_by_student', compact('student', 'mockTests'));
    }


}

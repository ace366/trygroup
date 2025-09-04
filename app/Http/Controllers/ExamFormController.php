<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Aspiration;
use App\Models\HokushinTest;
use App\Models\ReportCard;

class ExamFormController extends Controller
{
    /**
     * 入力フォームの表示
     */
    public function showForm()
    {
        $user = Auth::user();

        // 志望校データ（aspirations）から最新
        $aspiration = Aspiration::where('student_id', $user->id)
            ->latest()
            ->first();

        // 北辰テストの最新データ
        $hokushin = HokushinTest::where('student_id', $user->id)
            ->latest('exam_number')
            ->first();

        // 通知表（report_cards）から学年ごとに最新（9科合計）
        $naishin = [];
        for ($grade = 1; $grade <= 3; $grade++) {
            $rc = ReportCard::where('student_id', $user->id)
                ->where('grade', $grade)
                ->latest('semester')
                ->first();
            $naishin[$grade] = $rc ? $rc->nine_subjects_total : null;
        }

        // Bladeへ渡す初期値
        $preFill = [
            'school'     => $aspiration->school_name ?? '',
            'department' => $aspiration->department ?? '',
            'japanese'   => $hokushin->japanese ?? '',
            'math'       => $hokushin->math ?? '',
            'english'    => $hokushin->english ?? '',
            'science'    => $hokushin->science ?? '',
            'social'     => $hokushin->social ?? '',
            'naishin_1'  => $naishin[1] ?? '',
            'naishin_2'  => $naishin[2] ?? '',
            'naishin_3'  => $naishin[3] ?? '',
        ];

        return view('exam_form', compact('preFill'));
    }
}

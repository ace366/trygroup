<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guidance;
use App\Models\User;

use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;


class GuidanceController extends Controller
{
    // app/Http/Controllers/GuidanceController.php（use の下あたりに追加）
    public function availableDates(Request $request)
    {
        $monthStr = $request->query('month'); // 形式: YYYY-MM
        if (!preg_match('/^\d{4}-\d{2}$/', (string)$monthStr)) {
            $month = Carbon::now('Asia/Tokyo')->startOfMonth();
        } else {
            $month = Carbon::createFromFormat('Y-m', $monthStr, 'Asia/Tokyo')->startOfMonth();
        }
        $start = $month->copy();
        $end   = $month->copy()->endOfMonth();

        $rows = Guidance::whereBetween('registered_at', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('DATE(registered_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        return response()->json([
            'month' => $month->format('Y-m'),
            'dates' => $rows->map(fn($r) => ['date' => $r->d, 'count' => (int)$r->c])->values(),
        ]);
    }
    // フォーム表示
    public function create(Request $request)
    {
        $teachers = User::where('role', 'teacher')->orderBy('last_name_kana')->get();

        if (!$request->filled('student_id')) {
            abort(404, '生徒IDが指定されていません。');
        }

        $student = User::where('id', $request->student_id)
                    ->where('role', 'user')
                    ->firstOrFail();

        return view('guidances.create', compact('teachers', 'student'));
    }

    // 登録処理
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'registered_at' => 'required|date',
            'course_type' => 'required|in:土曜塾,英検対策',
            'time_zone' => 'required|in:午前,午後',
            'group' => 'required|string|max:20',
            'subject' => 'required|string|max:50',
            'unit' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'school_name' => 'required|in:寄居中学校,城南中学校,男衾中学校',
            'understanding_level' => 'nullable|integer|min:1|max:5',
            'concentration_level' => 'nullable|integer|min:1|max:5',
            'attitude' => 'nullable|string',
            'homework' => 'nullable|string',
            'homework_flag' => 'nullable|boolean',
        ]);

        $guidance = Guidance::create([
            ...$validated,
            'teacher_id' => auth()->id(),
        ]);
        return redirect()
            ->route('guidances.history', ['student_id' => $guidance->student_id])
            ->with('success', '記録を登録しました。');
    }

    // 生徒別履歴表示
    public function history($student_id)
    {
        $student = User::findOrFail($student_id);
        $guidances = Guidance::where('student_id', $student_id)
                             ->orderByDesc('registered_at')
                             ->get();

        return view('guidances.history', compact('student', 'guidances'));
    }
    public function updateHomeworkFlag(Request $request, Guidance $guidance)
    {
        $request->validate([
            'homework_flag' => 'required|boolean',
        ]);

        $guidance->update(['homework_flag' => $request->homework_flag]);

        return back()->with('success', '宿題フラグを更新しました');
    }

    // 編集画面表示
    public function edit(Guidance $guidance)
    {
        $user = auth()->user();
        // admin または 登録した講師のみ許可
        if (!($user->role === 'admin' || $guidance->teacher_id === $user->id)) {
            abort(403, 'この記録を編集する権限がありません。');
        }

        $student = User::findOrFail($guidance->student_id);
        if ($student->role !== 'user') {
            abort(403, '対象が生徒ではありません。');
        }

        return view('guidances.edit', compact('guidance', 'student'));
    }
    
    public function update(Request $request, Guidance $guidance)
    {
        $user = auth()->user();
        if (!($user->role === 'admin' || $guidance->teacher_id === $user->id)) {
            abort(403, 'この記録を更新する権限がありません。');
        }

        $validated = $request->validate([
            'registered_at' => 'required|date',
            'course_type' => 'required|in:土曜塾,英検対策',
            'time_zone' => 'required|in:午前,午後',
            'group' => 'required|string|max:20',
            'subject' => 'required|string|max:50',
            'unit' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'understanding_level' => 'nullable|integer|min:1|max:5',
            'concentration_level' => 'nullable|integer|min:1|max:5',
            'attitude' => 'nullable|string',
            'homework' => 'nullable|string',
        ]);

        $guidance->update($validated);

        return redirect()
            ->route('guidances.history', $guidance->student_id)
            ->with('success', '記録を更新しました。');
    }
    // ▼ 置換：report() を Dompdf 直呼び出しに変更（wrapper不在でも動作）
    public function report($student_id)
    {
        $student = User::findOrFail($student_id);

        $guidances = Guidance::where('student_id', $student_id)
            ->orderBy('registered_at')
            ->get();

        // 講師氏名（teacher_id → 姓 名）を付与
        $teacherMap = User::whereIn('id', $guidances->pluck('teacher_id')->filter()->unique())
            ->get()
            ->mapWithKeys(fn($u) => [$u->id => trim(($u->last_name ?? '') . ' ' . ($u->first_name ?? ''))]);

        $guidances->transform(function ($g) use ($teacherMap) {
            $g->teacher_name = $teacherMap[$g->teacher_id] ?? '-';
            return $g;
        });

        $html = view('guidances.pdf', compact('student', 'guidances'))->render();

        $options = new Options();
        $options->set('defaultFont', 'ipag');
        $options->set('dpi', 96);
        $options->set('isRemoteEnabled', false);     // 外部アクセス無効（安全）
        $options->setChroot(base_path());            // file:// をアプリ配下に限定
        $options->set('fontDir', storage_path('fonts'));
        $options->set('fontCache', storage_path('fonts'));

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        $filename = "指導報告書_{$student->last_name}{$student->first_name}.pdf";

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }


    // app/Http/Controllers/GuidanceController.php
    // （置換）reportToday メソッド
    // ▼ 置換：reportToday() でも同様に講師名を付与してから一括PDF生成
    public function reportToday(Request $request)
    {
        $jstDate = $request->input('date', Carbon::now('Asia/Tokyo')->toDateString());

        $guidances = Guidance::whereDate('registered_at', $jstDate)
            ->orderBy('student_id')
            ->orderBy('registered_at')
            ->get();

        if ($guidances->isEmpty()) {
            // （空時PDFは既存処理のまま）
            $html = <<<HTML
    <!DOCTYPE html><html lang="ja"><head><meta charset="UTF-8"><title>指導報告書一括（{$jstDate}）</title>
    <style>
    @font-face{font-family:'ipag';font-style:normal;font-weight:400;src:url("file://{{ storage_path('fonts/ipag.ttf') }}") format('truetype');}
    body{font-family:'ipag',sans-serif;font-size:13px;line-height:1.7;margin:40mm 15mm;}
    h1{font-size:18px;margin-bottom:10px;}
    p{margin:0;}
    </style></head><body>
    <h1>指導報告書一括</h1>
    <p>{$jstDate} の指導記録はありません。</p>
    </body></html>
    HTML;

            $options = new Options();
            $options->set('defaultFont', 'ipag');
            $options->set('dpi', 96);
            $options->set('isRemoteEnabled', false);
            $options->setChroot(base_path());
            $options->set('fontDir', storage_path('fonts'));
            $options->set('fontCache', storage_path('fonts'));

            $dompdf = new Dompdf($options);
            $dompdf->setPaper('a4', 'portrait');
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->render();

            $filename = "よりE土曜塾‗指導報告書_{$jstDate}.pdf";
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        // ▼ 講師氏名マッピング → 各レコードに teacher_name を付与
        $teacherMap = User::whereIn('id', $guidances->pluck('teacher_id')->filter()->unique())
            ->get()
            ->mapWithKeys(fn($u) => [$u->id => trim(($u->last_name ?? '') . ' ' . ($u->first_name ?? ''))]);

        $guidances->transform(function ($g) use ($teacherMap) {
            $g->teacher_name = $teacherMap[$g->teacher_id] ?? '-';
            return $g;
        });

        $groups = $guidances->groupBy('student_id');
        $studentIds = $groups->keys()->all();
        $students = User::whereIn('id', $studentIds)->get()->keyBy('id');

        $html = view('guidances.pdf_bulk', [
            'date'     => $jstDate,
            'groups'   => $groups,
            'students' => $students,
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'ipag');
        $options->set('dpi', 96);
        $options->set('isRemoteEnabled', false);
        $options->setChroot(base_path());
        $options->set('fontDir', storage_path('fonts'));
        $options->set('fontCache', storage_path('fonts'));

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        $filename = "よりE土曜塾‗指導報告書__{$jstDate}.pdf";
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
    // ▼ 追加：削除処理
    public function destroy(Request $request, Guidance $guidance)
    {
        $user = $request->user();
        // admin または 登録した講師のみ削除可
        if (!($user->role === 'admin' || $guidance->teacher_id === $user->id)) {
            abort(403, '削除権限がありません。');
        }

        $studentId = $guidance->student_id;
        $guidance->delete();

        return redirect()
            ->route('guidances.history', ['student_id' => $studentId])
            ->with('success', '記録を削除しました。');
    }
}


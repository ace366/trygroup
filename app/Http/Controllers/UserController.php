<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth; // これをファイルの上に追加！

class UserController extends Controller
{
    /**
     * ユーザー一覧を表示
     */
    public function index(Request $request)
    {
        $query = User::query();



        // フィルタリング処理
        if ($request->filled('school')) {
            $query->where('school', $request->input('school'));
        }
        if ($request->filled('grade')) {
            $query->where('grade', $request->input('grade'));
        }
        if ($request->filled('eiken')) {
            $query->where('eiken', $request->input('eiken'));
        }
        if ($request->filled('lesson_time')) {
            $query->where('lesson_time', $request->input('lesson_time'));
        }
        if ($request->filled('lesson_type')) {
            $lessonType = $request->input('lesson_type');
            if ($lessonType === 'オンライン・オンデマンド') {
                $query->where(function ($q) {
                    $q->where('lesson_type', 'オンライン')
                    ->orWhere('lesson_type', 'オンデマンド');
                });
            } else {
                $query->where('lesson_type', $lessonType);
            }
        }
        if ($request->filled('phone')) {
            $query->where('phone', $request->input('phone'));
        }
        if ($request->filled('email')) {
            $query->where('email', $request->input('email'));
        }
        // ▼ ソート許可カラムを限定
        $sortable = [
            'id', 'last_name_kana', 'school', 'grade', 'eiken',
            'lesson_time', 'lesson_type', 'phone', 'email',
            'created_at', 'updated_at',
        ];

        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');

        // ▼ 不正な値が入った場合はデフォルトにフォールバック
        if (!in_array($sort, $sortable)) {
            $sort = 'id';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $query->orderBy($sort, $direction);

        $users = $query->get();

        return view('users.index', compact('users'));
    }

    /**
     * ユーザー一覧を CSV ファイルでエクスポート
     */
    public function exportCsv(Request $request)
    {
        $query = User::query();

        // フィルタリング処理
        if ($request->filled('school')) {
            $query->where('school', $request->input('school'));
        }
        if ($request->filled('grade')) {
            $query->where('grade', $request->input('grade'));
        }
        if ($request->filled('class')) {
            $query->where('class', $request->input('class'));
        }
        if ($request->filled('eiken')) {
            $query->where('eiken', $request->input('eiken'));
        }
        if ($request->filled('lesson_time')) {
            $query->where('lesson_time', $request->input('lesson_time'));
        }
        if ($request->filled('lesson_type')) {
            $query->where('lesson_type', $request->input('lesson_type'));
        }
        if ($request->filled('phone')) {
            $query->where('phone', $request->input('phone'));
        }
        if ($request->filled('email')) {
            $query->where('email', $request->input('email'));
        }
        if ($request->filled('other')) {
            $query->where('other', $request->input('other'));
        }
        if ($request->filled('created_at')) {
            $query->where('created_at', $request->input('created_at'));
        }
        if ($request->filled('updated_at')) {
            $query->where('updated_at', $request->input('updated_at'));
        }
        if ($request->filled('login_count')) {
            $query->where('login_count', $request->input('login_count'));
        }
        if ($request->filled('total_playtime')) {
            $query->where('total_playtime', $request->input('total_playtime'));
        }
        $users = $query->get();

        $csvData = "ID,名前,かな,学校,学年,組,英検,授業時間,タイプ,電話,メールアドレス,特記事項,登録日,更新日,ログイン回数,学習時間\n";

        // ▼ セルごとにCSV仕様のエスケープ処理
        function csv_escape($value) {
            $value = str_replace('"', '""', $value); // ダブルクォートを2つに
            return '"' . $value . '"';
        }

        foreach ($users as $user) {
            $csvData .=
                csv_escape($user->id) . ',' .
                csv_escape("{$user->last_name} {$user->first_name}") . ',' .
                csv_escape("{$user->last_name_kana} {$user->first_name_kana}") . ',' .
                csv_escape($user->school) . ',' .
                csv_escape($user->grade) . ',' .
                csv_escape($user->class) . ',' .
                csv_escape($user->eiken) . ',' .
                csv_escape($user->lesson_time) . ',' .
                csv_escape($user->lesson_type) . ',' .
                csv_escape($user->phone) . ',' .
                csv_escape($user->email) . ',' .
                csv_escape($user->other) . ',' . // ←改行もOK
                csv_escape($user->created_at) . ',' .
                csv_escape($user->updated_at) . ',' .
                csv_escape($user->login_count) . ',' .
                csv_escape($user->total_playtime) . "\n";
        }

        $fileName = 'r7yorii_' . now()->format('Ymd_His') . '.csv';

        return Response::make(mb_convert_encoding($csvData, "SJIS-WIN", "UTF-8"), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ]);
    }
    public function card(Request $request)
    {
        // フィルター条件があるなら受け取る（一覧と同じロジックにする）
        $query = User::query();
    
        if ($request->filled('school')) {
            $query->where('school', $request->school);
        }
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }
        if ($request->filled('eiken')) {
            $query->where('eiken', $request->eiken);
        }
        if ($request->filled('lesson_time')) {
            $query->where('lesson_time', $request->lesson_time);
        }
    
        $users = $query->get();
    
        return view('users.card', compact('users'));
    }
    public function myCard()
    {
        $user = Auth::user(); // ログイン中のユーザー情報を取得
        return view('users.my_card', compact('user'));
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'ユーザーを削除しました。');
    }
    public function updateRole(Request $request, User $user)
    {
        // 管理者以外は403
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        // バリデーション
        $validated = $request->validate([
            'role' => 'required|in:admin,teacher,user,editor',
        ]);

        // 権限更新
        $user->role = $validated['role'];
        $user->save();

        return back()->with('status', '権限を更新しました');
    }
}

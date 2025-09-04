<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Schedule; // Scheduleモデルをインポート
use Illuminate\Support\Facades\Auth; // ✅ 追加

class DashboardController extends Controller
{
    public function index()
    {
        // `videos` テーブルから学年・科目のデータを取得
        //$videos = Video::orderBy('grade')->orderBy('subject')->get();
        $videos = Video::paginate(4); // 1ページに4つの動画（2×2）
        // 学年リスト（重複削除）
        $grades = Video::distinct()->pluck('grade');
        
        $subjects = Video::distinct()->pluck('subject');

        $schedules = Schedule::all(); // スケジュールデータを取得

        $user = Auth::user(); // ✅ ここでログイン中のユーザーを取得
        // ビューにデータを渡す
        return view('welcome', [
            'videos' => $videos,
            'grades' => $grades,
            'subjects' => $subjects,
            'user' => Auth::user(), // ✅ ユーザー情報をビューに渡す
        ]);
    }
}

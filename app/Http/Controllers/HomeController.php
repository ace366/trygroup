<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Post;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // 🔵 まずログインしているかチェック
        if (auth()->check()) {
            if (auth()->user()->role === 'admin') {
                return redirect()->route('students.index');
            } elseif (auth()->user()->role === 'user') {
                return redirect()->route('students.dashboard', auth()->id());
            }
        }

        // 🔵 ここに来たら通常通りトップページ表示（ゲスト用）
        $today = Carbon::today()->toDateString();

        $schedules = Schedule::where('date', '>=', $today)
                             ->orderBy('date')
                             ->take(5)
                             ->get();

        $posts = Post::orderBy('created_at', 'desc')->take(5)->get();

        return view('home', compact('schedules', 'posts'));
    }
}

<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * ユーザーがログイン後の処理
     */
    protected function authenticated(Request $request, $user)
    {
        // ✅ ログイン回数をカウント
        $user->increment('login_count');

        // ✅ 指定のページにリダイレクト
        return redirect()->route('/');
    }
}

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $user->increment('login_count');
        return view('/');
    })->name('/');
});
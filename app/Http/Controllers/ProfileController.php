<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name_kana' => 'required|string|max:255',
            'first_name_kana' => 'required|string|max:255',
            'school' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'class' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'lesson_type' => 'required|string|max:255',
            'lesson_time' => 'required|string|max:255',
            'eiken' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'other' => 'nullable|string',
        ]);

        $user->update($request->only([
            'last_name', 'first_name', 'last_name_kana', 'first_name_kana',
            'school', 'grade', 'class', 'phone',
            'lesson_type', 'lesson_time', 'eiken',
            'email', 'other'
        ]));

        return back()->with('status', 'profile-updated');
    }
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:4', 'confirmed'], // ✅ 8 → 4 に変更
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'password-updated');
    }
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        // ユーザーの削除
        $user->delete();

        // セッションを無効化
        Auth::logout();

        // 削除後にリダイレクト
        return redirect('/')->with('status', 'アカウントが削除されました');
    }
}

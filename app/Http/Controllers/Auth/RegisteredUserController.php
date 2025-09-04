<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\RegisterRequest;

class RegisteredUserController extends Controller
{
    /**
     * 新規登録フォームの表示
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * 新規登録の処理
     */
    public function store(RegisterRequest $request)
    {
        // フルネームを生成（trim()で不要な空白を削除）
        $fullName = trim($request->last_name . ' ' . $request->first_name);
    
        // フルネームが空でないかチェック
        if (empty($fullName)) {
            return back()->withErrors(['name' => 'フルネームが空です']);
        }
    
        // ユーザー作成
        $user = User::create([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'last_name_kana' => $request->last_name_kana,
            'first_name_kana' => $request->first_name_kana,
            'school' => $request->school,
            'grade' => $request->grade,
            'class' => $request->class,
            'phone' => $request->phone,
            'lesson_type' => $request->lesson_type,
            'lesson_time' => $request->lesson_time, // ✅ 追加
            'name' => $fullName, // ✅ フルネームをセット
            'eiken' => $request->eiken,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'other' => $request->other,
        ]);
    
        event(new Registered($user));
    
        auth()->login($user);
    
        return redirect()->route('dashboard')->with('success', 'ユーザー登録が完了しました！');
        //return redirect()->route('welcome')->with('success', 'ユーザー登録が完了しました！');
    }
}    
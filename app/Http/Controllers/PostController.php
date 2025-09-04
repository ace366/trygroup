<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // お知らせ一覧表示
    public function index()
    {
        // $posts = Post::latest()->get();
        $posts = Post::latest()->paginate(10); // ✅ 10件ずつ表示
    
        foreach ($posts as $post) {
            // URL のみリンク化（他の部分はそのまま）
            $content = preg_replace_callback('/(https?:\/\/[^\s]+)/', function ($matches) {
                return '<a href="' . e($matches[1]) . '" class="text-blue-500 underline" target="_blank">' . e($matches[1]) . '</a>';
            }, e($post->content));
    
            // 改行を <br> に変換し、最後の <br> を削除
            $content = nl2br($content);
            $content = preg_replace('/<br\s*\/?>$/', '', $content); // ✅ 最後の <br> を削除
    
            // Blade に渡すために変更
            $post->content = $content;
        }
    
        return view('posts.index', compact('posts'));
    }
    // お知らせ新規作成フォーム
    public function create()
    {
        return view('posts.create');
    }
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'お知らせを削除しました。');
    }
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }
    // ✅ 投稿を更新する処理
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // 投稿の内容を更新
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('posts.index')->with('success', '投稿が更新されました。');
    }
    // お知らせ保存処理
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('posts.index')->with('success', 'お知らせを登録しました！');
    }
}

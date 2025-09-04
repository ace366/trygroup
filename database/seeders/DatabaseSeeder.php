<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post; // Postモデルをインポート
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ユーザーのシードデータを作成
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // デフォルトのパスワードを設定
        ]);

        // 投稿のシードデータを作成
        \App\Models\Post::factory(10)->create();
    }
}

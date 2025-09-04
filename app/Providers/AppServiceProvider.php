<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('ja');

        // Blade から getYouTubeID() を安全に呼べるように共有
        View::composer('*', function ($view) {
            $view->with('getYouTubeID', function ($url) {
                return function_exists('getYouTubeID') ? getYouTubeID($url) : null;
            });
        });

        // SQLite 使用時の初期化（外部キーON + 未作成テーブルの自動作成）
        if (config('database.default') === 'sqlite') {
            $dbFile = database_path('database.sqlite');
            if (!is_file($dbFile)) {
                @touch($dbFile);
            }

            DB::statement('PRAGMA foreign_keys = ON');

            if (!Schema::hasTable('conversations') || !Schema::hasTable('messages')) {
                Artisan::call('migrate', ['--force' => true]);
            }
        }
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Events\Login;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        // ✅ `role` を使って管理者を判定（未ログイン時の `null` を考慮）
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        Event::listen(Login::class, function ($event) {
            $user = $event->user;
            $user->increment('login_count');
        });
    }
}

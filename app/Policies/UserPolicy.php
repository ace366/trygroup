<?php
namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function isAdmin(User $user)
    {
        return $user->role === 'admin'; // ロールが 'admin' のユーザーのみ許可
    }
}

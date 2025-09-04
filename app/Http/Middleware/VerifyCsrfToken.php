<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * CSRF���ڤ��������URI
     *
     * @var array<int, string>
     */
    protected $except = [
        'line/webhook',
    ];
}

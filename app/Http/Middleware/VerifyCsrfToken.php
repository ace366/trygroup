<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * CSRF検証を除外するURI
     *
     * @var array<int, string>
     */
    protected $except = [
        'line/webhook',
    ];
}

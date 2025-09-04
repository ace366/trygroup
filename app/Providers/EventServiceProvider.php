<?php
use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;

protected $listen = [
    Login::class => [
        LogSuccessfulLogin::class,
    ],
];
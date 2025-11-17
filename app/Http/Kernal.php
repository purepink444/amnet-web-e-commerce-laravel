<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     */
    protected $middleware = [
        // ...
    ];

    /**
     * The application's route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            // ...
        ],

        'api' => [
            // ...
        ],
    ];

    /**
     * The application's route middleware.
     *
     * ⚠️ Laravel 11 ใช้ $middlewareAliases แทน $routeMiddleware
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // ✅ Custom Middleware - ใช้ตัวเดียวรับ parameter
        'role' => \App\Http\Middleware\RolesMiddleware::class,
    ];

    /**
     * สำหรับ Laravel 10 ใช้ $routeMiddleware แทน
     */
    // protected $routeMiddleware = [
    //     'auth' => \App\Http\Middleware\Authenticate::class,
    //     'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    //     'role' => \App\Http\Middleware\RolesMiddleware::class,
    // ];
}
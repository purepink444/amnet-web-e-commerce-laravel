<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ ลงทะเบียน middleware alias - รวมทั้งหมดในที่เดียว
        $middleware->alias([
            'role' => \App\Http\Middleware\RolesMiddleware::class,
            'overclock' => \App\Http\Middleware\OverclockMiddleware::class,
            'secure.auth' => \App\Http\Middleware\SecureAuth::class,
            'throttle.api' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1',
            'throttle.login' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':5,15',
            'throttle.password.reset' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':3,60',
        ]);

        // Security headers and performance monitoring for all requests
        $middleware->use([
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\PerformanceMonitor::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Database backup every day at 2 AM
        $schedule->command('db:backup')
                ->dailyAt('02:00')
                ->withoutOverlapping()
                ->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
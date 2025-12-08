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
        // ✅ ลงทะเบียน middleware alias
        $middleware->alias([
            'role' => \App\Http\Middleware\RolesMiddleware::class,
            'overclock' => \App\Http\Middleware\OverclockMiddleware::class,
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
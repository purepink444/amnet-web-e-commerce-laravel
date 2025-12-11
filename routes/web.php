<?php
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\HomeController;

// Public + Guest Routes
require __DIR__.'/pages.php';
require __DIR__.'/guest.php';
require __DIR__.'/client.php';

// Authenticated Routes
require __DIR__.'/account.php';
require __DIR__.'/admin.php';

// OVERCLOCK ROUTES - EXTREME PERFORMANCE MODE
require __DIR__.'/overclock.php';


// Checkout Routes (redirect to account checkout)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', function () {
        return redirect()->route('account.checkout.index');
    })->name('checkout');

    // Alias for checkout.index to maintain compatibility
    Route::get('/checkout/index', function () {
        return redirect()->route('account.checkout.index');
    })->name('checkout.index');
});

// Public Routes (Login/Register) with rate limiting
Route::middleware(['throttle:10,1'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::post('/check-username', [RegisterController::class, 'checkUsername'])->name('check.username');
    Route::post('/check-email', [RegisterController::class, 'checkEmail'])->name('check.email');
});


// Health Check Route (for load balancer)
Route::get('/health', function () {
    try {
        // Check database connection
        \DB::connection()->getPdo();

        // Check Redis connection if configured
        if (config('cache.default') === 'redis') {
            \Redis::ping();
        }

        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'services' => [
                'database' => 'ok',
                'cache' => config('cache.default') === 'redis' ? 'ok' : 'file',
                'storage' => is_writable(storage_path()) ? 'ok' : 'error'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'error' => $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
});

// Fallback
Route::fallback(fn() => response()->view('errors.404', [], 404));

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    RegisterController,
    LoginController,
    AdminProductController,
    ClientProductController,
    DashboardController,
};
use App\Http\Controllers\Account\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('home'))->name('home');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Authentication
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->name('login.post');
    });
    
    // Registration
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'create')->name('register');
        Route::post('/register', 'store')->name('register.store');
    });
    
    // AJAX Validation Endpoints
    Route::post('/check-username', [RegisterController::class, 'checkUsername'])->name('check.username');
    Route::post('/check-email', [RegisterController::class, 'checkEmail'])->name('check.email');
});

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/

Route::prefix('product')->name('client.products.')->group(function () {
    Route::get('/', [ClientProductController::class, 'index'])->name('index');
    Route::get('/search', [ClientProductController::class, 'quickSearch'])->name('search');
    Route::get('/{id}', [ClientProductController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| Static Pages
|--------------------------------------------------------------------------
*/

Route::prefix('pages')->name('pages.')->group(function () {
    Route::view('/payment', 'pages.payment')->name('payment');
    Route::view('/contact', 'pages.contact')->name('contact');
    Route::view('/news', 'pages.news')->name('news');
    Route::view('/about', 'pages.about')->name('about');
    Route::view('/terms', 'pages.terms')->name('terms');
    Route::view('/privacy', 'pages.privacy')->name('privacy');
});

// Legacy redirects
Route::permanentRedirect('/payment', '/pages/payment');
Route::permanentRedirect('/contact', '/pages/contact');
Route::permanentRedirect('/news', '/pages/news');
Route::permanentRedirect('/profile', '/account/profile');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Admin Routes
    |----------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard refresh
        Route::post('/dashboard/refresh', [DashboardController::class, 'refreshCache'])->name('dashboard.refresh');

        // Product Management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [AdminProductController::class, 'index'])->name('index');
            Route::get('/create', [AdminProductController::class, 'create'])->name('create');
            Route::post('/', [AdminProductController::class, 'store'])->name('store');
            Route::get('/{id}', [AdminProductController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Customer Routes (Profile + Account Pages)
    |----------------------------------------------------------------------
    */
    // ปิด middleware 'customer' ชั่วคราวเพื่อดูหน้าโปรไฟล์
    Route::prefix('account')->name('account.')->group(function () {
    // Route::middleware('customer')->prefix('account')->name('account.')->group(function () {
        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Orders, Wishlist, Settings
        Route::view('/orders', 'account.orders')->name('orders');
        Route::view('/wishlist', 'account.wishlist')->name('wishlist');
        Route::view('/settings', 'account.settings')->name('settings');
    });
});

/*
|--------------------------------------------------------------------------
| API Routes (Optional)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    Route::get('/products/featured', [ClientProductController::class, 'getFeatured'])->name('products.featured');
    Route::middleware('auth')->group(function () {
        // Authenticated API routes
    });
});

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
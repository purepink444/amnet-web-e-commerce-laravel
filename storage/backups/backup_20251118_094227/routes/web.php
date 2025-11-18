<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    RegisterController,
    LoginController,
    AdminProductController,
    ClientProductController,
    DashboardController,
    CartController,
    CheckoutController
};
use App\Http\Controllers\Account\{
    ProfileController,
    OrderController,
    WishlistController,
    SettingsController
};
use App\Http\Controllers\Admin\{
    AdminOrderController,
    AdminUserController,
    AdminReportController
};

/*
|--------------------------------------------------------------------------
| Public Routes (ทุกคนเข้าได้)
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('home'))->name('home');

// Static Pages
Route::prefix('pages')->name('pages.')->group(function () {
    Route::view('/payment', 'pages.payment')->name('payment');
    Route::view('/contact', 'pages.contact')->name('contact');
    Route::view('/news', 'pages.news')->name('news');
    Route::view('/about', 'pages.about')->name('about');
    Route::view('/terms', 'pages.terms')->name('terms');
    Route::view('/privacy', 'pages.privacy')->name('privacy');
});

// Legacy URL Redirects
Route::permanentRedirect('/payment', '/pages/payment');
Route::permanentRedirect('/contact', '/pages/contact');
Route::permanentRedirect('/news', '/pages/news');
Route::permanentRedirect('/profile', '/account/profile');

/*
|--------------------------------------------------------------------------
| Guest Routes (เฉพาะคนที่ยังไม่ได้ Login)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->name('login.post');
    });
    
    // Register
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'create')->name('register');
        Route::post('/register', 'store')->name('register.store');
        Route::post('/check-username', 'checkUsername')->name('check.username');
        Route::post('/check-email', 'checkEmail')->name('check.email');
    });
});

/*
|--------------------------------------------------------------------------
| Product Routes (ไม่ต้อง Login ก็ดูได้)
|--------------------------------------------------------------------------
*/

Route::prefix('product')->name('client.products.')->group(function () {
    Route::get('/', [ClientProductController::class, 'index'])->name('index');
    Route::get('/search', [ClientProductController::class, 'quickSearch'])->name('search');
    Route::get('/{id}', [ClientProductController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| API Routes (สำหรับ AJAX/Frontend)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    Route::get('/products/featured', [ClientProductController::class, 'getFeatured'])
        ->name('products.featured');
    
    // Protected API Routes
    Route::middleware('auth')->group(function () {
        Route::post('/cart/add/{productId}', [CartController::class, 'addViaApi'])
            ->name('cart.add');
        Route::get('/cart/count', [CartController::class, 'count'])
            ->name('cart.count');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (ต้อง Login ก่อน)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Auto-redirect Dashboard ตาม Role
    Route::get('/dashboard', function () {
        $roleName = strtolower(auth()->user()->role?->role_name ?? 'member');
        
        return match($roleName) {
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('account.profile')
        };
    })->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | 🔴 ADMIN ONLY ROUTES
    |----------------------------------------------------------------------
    */
    Route::middleware(\App\Http\Middleware\RolesMiddleware::class . ':admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            
            // Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');
            Route::post('/dashboard/refresh', [DashboardController::class, 'refreshCache'])
                ->name('dashboard.refresh');

            // 📦 Product Management
            Route::resource('products', AdminProductController::class);

            // 🚚 Order Management
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [AdminOrderController::class, 'index'])->name('index');
                Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
                Route::patch('/{id}/status', [AdminOrderController::class, 'updateStatus'])
                    ->name('update-status');
                Route::delete('/{id}', [AdminOrderController::class, 'destroy'])->name('destroy');
            });

            // 👥 User Management
            Route::resource('users', AdminUserController::class);

            // 📊 Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [AdminReportController::class, 'index'])->name('index');
                Route::get('/sales', [AdminReportController::class, 'sales'])->name('sales');
                Route::get('/products', [AdminReportController::class, 'products'])->name('products');
                Route::get('/customers', [AdminReportController::class, 'customers'])->name('customers');
                Route::get('/export/{type}', [AdminReportController::class, 'export'])->name('export');
            });
        });

    /*
    |----------------------------------------------------------------------
    | 🟢 MEMBER ROUTES (Member + Admin เข้าได้)
    |----------------------------------------------------------------------
    */
    Route::middleware(\App\Http\Middleware\RolesMiddleware::class . ':member,admin')
        ->prefix('account')
        ->name('account.')
        ->group(function () {
            
            // 👤 Profile
            Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
                ->name('profile.avatar');

            // 📋 Orders
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [OrderController::class, 'index'])->name('index');
                Route::get('/{id}', [OrderController::class, 'show'])->name('show');
                Route::delete('/{id}', [OrderController::class, 'cancel'])->name('cancel');
                Route::post('/{id}/review', [OrderController::class, 'submitReview'])
                    ->name('review');
            });

            // ❤️ Wishlist
            Route::prefix('wishlist')->name('wishlist.')->group(function () {
                Route::get('/', [WishlistController::class, 'index'])->name('index');
                Route::post('/{productId}', [WishlistController::class, 'toggle'])->name('toggle');
                Route::delete('/clear', [WishlistController::class, 'clear'])->name('clear');
            });

            // ⚙️ Settings
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [SettingsController::class, 'index'])->name('index');
                Route::patch('/', [SettingsController::class, 'update'])->name('update');
                Route::patch('/password', [SettingsController::class, 'updatePassword'])
                    ->name('password');
                Route::delete('/account', [SettingsController::class, 'deleteAccount'])
                    ->name('delete');
            });

            // 🛒 Shopping Cart
            Route::prefix('cart')->name('cart.')->group(function () {
                Route::get('/', [CartController::class, 'index'])->name('index');
                Route::post('/add/{productId}', [CartController::class, 'add'])->name('add');
                Route::patch('/update/{itemId}', [CartController::class, 'update'])->name('update');
                Route::delete('/remove/{itemId}', [CartController::class, 'remove'])->name('remove');
                Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
            });

            // 💳 Checkout
            Route::prefix('checkout')->name('checkout.')->group(function () {
                Route::get('/', [CheckoutController::class, 'index'])->name('index');
                Route::post('/process', [CheckoutController::class, 'process'])->name('process');
                Route::get('/success/{orderId}', [CheckoutController::class, 'success'])
                    ->name('success');
                Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
            });
        });
});

/*
|--------------------------------------------------------------------------
| Fallback - 404 Page
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
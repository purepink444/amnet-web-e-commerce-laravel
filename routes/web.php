<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    RegisterController,
    LoginController,
    AdminProductController,
    ClientProductController,
    DashboardController,
};
use App\Http\Controllers\Account\{
    ProfileController,
    OrderController,
    WishlistController,
    SettingsController
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('home'))->name('home');

/*
|--------------------------------------------------------------------------
| 🔍 Debug Routes (ลบทิ้งหลังแก้เสร็จ)
|--------------------------------------------------------------------------
*/
Route::get('/check-role', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not logged in'], 401);
    }
    
    $user = auth()->user();
    return response()->json([
        'authenticated' => true,
        'user_id' => $user->user_id,
        'username' => $user->username,
        'email' => $user->email,
        'role' => $user->role,
        'role_type' => gettype($user->role),
        'role_length' => strlen($user->role ?? ''),
        'role_trimmed' => trim($user->role ?? ''),
        'role_lower' => strtolower($user->role ?? ''),
        'role_bytes' => bin2hex($user->role ?? ''),
        'tests' => [
            'equals_member' => $user->role === 'member',
            'equals_admin' => $user->role === 'admin',
            'equals_customer' => $user->role === 'customer',
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Guest Routes (ผู้ที่ยังไม่ได้ Login)
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
        Route::post('/check-username', 'checkUsername')->name('check.username');
        Route::post('/check-email', 'checkEmail')->name('check.email');
    });
});

/*
|--------------------------------------------------------------------------
| Client Routes (ไม่ต้อง Login ก็เข้าได้)
|--------------------------------------------------------------------------
*/

Route::prefix('product')->name('client.products.')->group(function () {
    Route::get('/', [ClientProductController::class, 'index'])->name('index');
    Route::get('/search', [ClientProductController::class, 'quickSearch'])->name('search');
    Route::get('/{id}', [ClientProductController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| Static Pages (ไม่ต้อง Login ก็เข้าได้)
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
| Authenticated Routes (ต้อง Login ก่อน)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Logout (ทุกคนใช้ได้)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    /*
    |----------------------------------------------------------------------
    | 🔴 Admin Routes (เฉพาะ Admin เท่านั้น)
    |----------------------------------------------------------------------
    */
    Route::middleware(\App\Http\Middleware\RolesMiddleware::class . ':admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            
            // Dashboard สำหรับ Admin
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::post('/dashboard/refresh', [DashboardController::class, 'refreshCache'])->name('dashboard.refresh');

            // 📦 Product Management
            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/', [AdminProductController::class, 'index'])->name('index');
                Route::get('/create', [AdminProductController::class, 'create'])->name('create');
                Route::post('/', [AdminProductController::class, 'store'])->name('store');
                Route::get('/{id}', [AdminProductController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
                Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
                Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('destroy');
            });

            // 👥 User Management (TODO)
            // Route::resource('users', AdminUserController::class);

            // 🚚 Order Management (TODO)
            // Route::prefix('orders')->name('orders.')->group(function () {
            //     Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            //     Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
            //     Route::patch('/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
            // });

            // 👤 Admin Profile (ถ้าต้องการ)
            // Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
        });

    /*
    |----------------------------------------------------------------------
    | 🟢 Member/Customer Routes (เฉพาะ Member)
    |----------------------------------------------------------------------
    */
    Route::middleware(\App\Http\Middleware\RolesMiddleware::class . ':member')
        ->prefix('account')
        ->name('account.')
        ->group(function () {
            
            // 👤 Profile
            Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

            // 📋 Orders
            Route::get('/orders', [OrderController::class, 'index'])->name('orders');
            Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
            Route::delete('/orders/{id}', [OrderController::class, 'cancel'])->name('orders.cancel');

            // ❤️ Wishlist
            Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
            Route::post('/wishlist/{productId}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

            // ⚙️ Settings
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
            Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

            // 🛒 Cart & Checkout (TODO)
            // Route::get('/cart', [CartController::class, 'index'])->name('cart');
            // Route::post('/cart/{productId}', [CartController::class, 'add'])->name('cart.add');
            // Route::delete('/cart/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
            // Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
            // Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
        });

    /*
    |----------------------------------------------------------------------
    | 📊 Dashboard Redirect (Auto-redirect ตาม Role)
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        // member/customer ไปหน้า profile
        return redirect()->route('account.profile');
    })->name('dashboard');
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
| Fallback Route (404 Page)
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
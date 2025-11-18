<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    RegisterController,
    LoginController
};

use App\Http\Controllers\Admin\{
    DashboardController,
    AdminProductController,
    AdminOrderController,
    AdminUserController,
    AdminReportController,
    AdminCategoryController,
    AdminBrandController
};

use App\Http\Controllers\Client\{
    ClientProductController,
    CartController,
    CheckoutController
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

// Client Products
Route::prefix('product')->name('client.products.')->group(function () {
    Route::get('/', [ClientProductController::class, 'index'])->name('index');
    Route::get('/search', [ClientProductController::class, 'quickSearch'])->name('search');
    Route::get('/{id}', [ClientProductController::class, 'show'])->name('show');
});

// Static Pages
Route::prefix('pages')->name('pages.')->group(function () {
    Route::view('/payment', 'pages.payment')->name('payment');
    Route::view('/contact', 'pages.contact')->name('contact');
    Route::view('/news', 'pages.news')->name('news');
    Route::view('/about', 'pages.about')->name('about');
    Route::view('/terms', 'pages.terms')->name('terms');
    Route::view('/privacy', 'pages.privacy')->name('privacy');
});

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::post('/check-username', [RegisterController::class, 'checkUsername'])->name('check.username');
    Route::post('/check-email', [RegisterController::class, 'checkEmail'])->name('check.email');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard Auto-redirect
    Route::get('/dashboard', function () {
        $roleName = strtolower(auth()->user()->role?->role_name ?? 'member');
        return $roleName === 'admin' 
            ? redirect()->route('admin.dashboard')
            : redirect()->route('account.profile');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | 🔴 ADMIN ROUTES (ตรงตาม Views Structure)
    |--------------------------------------------------------------------------
    */
    Route::middleware(\App\Http\Middleware\RolesMiddleware::class . ':admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            /*
            |------------------------------------------------------------------
            | Dashboard (admin/dashboard.blade.php)
            |------------------------------------------------------------------
            */
            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');

            /*
            |------------------------------------------------------------------
            | Orders (admin/orders/)
            |------------------------------------------------------------------
            */
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [AdminOrderController::class, 'index'])
                    ->name('index');                    // → admin/orders/index.blade.php
                
                Route::get('/{id}', [AdminOrderController::class, 'show'])
                    ->name('show');                     // → admin/orders/show.blade.php
                
                Route::patch('/{id}/status', [AdminOrderController::class, 'updateStatus'])
                    ->name('update-status');
                
                Route::delete('/{id}', [AdminOrderController::class, 'destroy'])
                    ->name('destroy');
            });

            /*
            |------------------------------------------------------------------
            | Products (admin/products/)
            |------------------------------------------------------------------
            */
            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/', [AdminProductController::class, 'index'])
                    ->name('index');                    // → admin/products/index.blade.php
                
                Route::get('/create', [AdminProductController::class, 'create'])
                    ->name('create');                   // → admin/products/create.blade.php
                
                Route::post('/', [AdminProductController::class, 'store'])
                    ->name('store');
                
                Route::get('/{id}', [AdminProductController::class, 'show'])
                    ->name('show');                     // → admin/products/show.blade.php
                
                Route::get('/{id}/edit', [AdminProductController::class, 'edit'])
                    ->name('edit');                     // → admin/products/edit.blade.php
                
                Route::put('/{id}', [AdminProductController::class, 'update'])
                    ->name('update');
                
                Route::delete('/{id}', [AdminProductController::class, 'destroy'])
                    ->name('destroy');
            });

            /*
            |------------------------------------------------------------------
            | Product (admin/product/) - ไฟล์พิเศษ
            |------------------------------------------------------------------
            */
            Route::prefix('product')->name('product.')->group(function () {
                Route::get('/adm_product', [AdminProductController::class, 'admProduct'])
                    ->name('adm-product');              // → admin/product/adm_product.blade.php
                
                Route::get('/adm_profile', [AdminProductController::class, 'admProfile'])
                    ->name('adm-profile');              // → admin/product/adm_profile.blade.php
                
                Route::get('/{id}/edit', [AdminProductController::class, 'editSingle'])
                    ->name('edit-single');              // → admin/product/edit.blade.php
            });

            /*
            |------------------------------------------------------------------
            | Users (admin/users/)
            |------------------------------------------------------------------
            */
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [AdminUserController::class, 'index'])
                    ->name('index');                    // → admin/users/index.blade.php
                
                Route::get('/create', [AdminUserController::class, 'create'])
                    ->name('create');
                
                Route::post('/', [AdminUserController::class, 'store'])
                    ->name('store');
                
                Route::get('/{id}', [AdminUserController::class, 'show'])
                    ->name('show');
                
                Route::get('/{id}/edit', [AdminUserController::class, 'edit'])
                    ->name('edit');                     // → admin/users/edit.blade.php
                
                Route::put('/{id}', [AdminUserController::class, 'update'])
                    ->name('update');
                
                Route::delete('/{id}', [AdminUserController::class, 'destroy'])
                    ->name('destroy');
            });

            /*
            |------------------------------------------------------------------
            | Reports (admin/reports/)
            |------------------------------------------------------------------
            */
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [AdminReportController::class, 'index'])
                    ->name('index');                    // → admin/reports/index.blade.php
                
                Route::get('/sales', [AdminReportController::class, 'sales'])
                    ->name('sales');
                
                Route::get('/products', [AdminReportController::class, 'products'])
                    ->name('products');
                
                Route::get('/customers', [AdminReportController::class, 'customers'])
                    ->name('customers');
                
                Route::get('/export/{type}', [AdminReportController::class, 'export'])
                    ->name('export');
            });

            /*
            |------------------------------------------------------------------
            | Categories & Brands
            |------------------------------------------------------------------
            */
            Route::resource('categories', AdminCategoryController::class);
            Route::resource('brands', AdminBrandController::class);
        });

    /*
    |--------------------------------------------------------------------------
    | 🟢 MEMBER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(\App\Http\Middleware\RolesMiddleware::class . ':member,admin')
        ->prefix('account')
        ->name('account.')
        ->group(function () {

            // Profile
            Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

            // Orders
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [OrderController::class, 'index'])->name('index');
                Route::get('/{id}', [OrderController::class, 'show'])->name('show');
                Route::delete('/{id}', [OrderController::class, 'cancel'])->name('cancel');
            });

            // Wishlist
            Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
            Route::post('/wishlist/{productId}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

            // Settings
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
            Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

            // Cart
            Route::prefix('cart')->name('cart.')->group(function () {
                Route::get('/', [CartController::class, 'index'])->name('index');
                Route::post('/add/{productId}', [CartController::class, 'add'])->name('add');
                Route::patch('/update/{itemId}', [CartController::class, 'update'])->name('update');
                Route::delete('/remove/{itemId}', [CartController::class, 'remove'])->name('remove');
            });

            // Checkout
            Route::prefix('checkout')->name('checkout.')->group(function () {
                Route::get('/', [CheckoutController::class, 'index'])->name('index');
                Route::post('/process', [CheckoutController::class, 'process'])->name('process');
                Route::get('/success/{orderId}', [CheckoutController::class, 'success'])->name('success');
            });
        });
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('api')->name('api.')->middleware('throttle:60,1')->group(function () {
    Route::get('/products/featured', [ClientProductController::class, 'getFeatured'])
        ->name('products.featured');
    
    Route::middleware('auth')->group(function () {
        Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback (404)
|--------------------------------------------------------------------------
*/
Route::fallback(fn() => response()->view('errors.404', [], 404));
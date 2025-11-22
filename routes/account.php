<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Account\{ProfileController, OrderController, WishlistController, SettingsController};
use App\Http\Controllers\Client\{CartController, CheckoutController};

Route::middleware(['auth', \App\Http\Middleware\RolesMiddleware::class . ':member,admin'])
    ->prefix('account')
    ->name('account.')
    ->group(function () {

        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Logout
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

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
            Route::patch('/update', [CartController::class, 'update'])->name('update');
            Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
        });


        // Checkout
        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', [CheckoutController::class, 'index'])->name('index');
            Route::post('/process', [CheckoutController::class, 'process'])->name('process');
            Route::get('/success/{orderId}', [CheckoutController::class, 'success'])->name('success');
        });
});

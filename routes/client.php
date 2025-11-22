<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\{ClientProductController, CheckoutController};

Route::get('/', fn() => view('home'))->name('home');

// Client Products
Route::prefix('product')->name('client.products.')->group(function () {
    Route::get('/', [ClientProductController::class, 'index'])->name('index');
    Route::get('/search', [ClientProductController::class, 'quickSearch'])->name('search');
    Route::get('/{id}', [ClientProductController::class, 'show'])->name('show');
});

// Checkout
Route::middleware('auth')->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success/{orderId}', [CheckoutController::class, 'success'])->name('success');
    });
});


// API Routes (client related)
Route::prefix('api')->name('api.')->middleware('throttle:60,1')->group(function () {
    Route::get('/products/featured', [ClientProductController::class, 'getFeatured'])->name('products.featured');

    Route::middleware('auth')->group(function () {
        Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    });
});

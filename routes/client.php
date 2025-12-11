<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Client\{CartController, ClientProductController, CheckoutController, ReviewController, DiagnosticController, PaymentController};

Route::get('/', fn() => view('home'))->name('home');

// Client Products
Route::prefix('product')->name('client.products.')->group(function () {
    Route::get('/', [ClientProductController::class, 'index'])->name('index');
    Route::get('/search', [ClientProductController::class, 'quickSearch'])->name('search');
    Route::get('/{id}', [ClientProductController::class, 'show'])->name('show');
});

// Product Reviews
Route::prefix('product/{productId}/reviews')->name('products.reviews.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])->name('index');
    Route::get('/create', [ReviewController::class, 'create'])->name('create');
    Route::post('/', [ReviewController::class, 'store'])->name('store');
    Route::get('/{reviewId}', [ReviewController::class, 'show'])->name('show');
    Route::get('/{reviewId}/edit', [ReviewController::class, 'edit'])->name('edit');
    Route::put('/{reviewId}', [ReviewController::class, 'update'])->name('update');
    Route::delete('/{reviewId}', [ReviewController::class, 'destroy'])->name('destroy');
});

// Checkout
Route::middleware('auth')->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success/{orderId}', [CheckoutController::class, 'success'])->name('success');
    });
});

// Diagnostic
Route::prefix('diagnostic')->name('diagnostic.')->group(function () {
    Route::get('/', [DiagnosticController::class, 'index'])->name('index');
    Route::get('/system', [DiagnosticController::class, 'systemCheck'])->name('system');
    Route::get('/network', [DiagnosticController::class, 'networkCheck'])->name('network');
    Route::get('/product', [DiagnosticController::class, 'productCheck'])->name('product');
});


// Note: API routes have been moved to routes/api.php for proper separation
// All API endpoints are now available under /api/v1/ prefix

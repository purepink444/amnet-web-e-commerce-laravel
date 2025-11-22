<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\PaymentController;

Route::prefix('pages')->name('pages.')->group(function () {
    Route::view('/payment', 'pages.payment')->name('payment');
    Route::view('/contact', 'pages.contact')->name('contact');
    Route::view('/news', 'pages.news')->name('news');
    Route::view('/about', 'pages.about')->name('about');
    Route::view('/terms', 'pages.terms')->name('terms');
    Route::view('/privacy', 'pages.privacy')->name('privacy');
});

// Payment Routes
Route::middleware(['auth'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/order/{orderId}', [PaymentController::class, 'show'])->name('show');
    Route::post('/order/{orderId}/process', [PaymentController::class, 'process'])->name('process');
    Route::post('/verify', [PaymentController::class, 'verify'])->name('verify');
});

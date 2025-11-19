<?php
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;

// Public + Guest Routes
require __DIR__.'/pages.php';
require __DIR__.'/guest.php';
require __DIR__.'/client.php';

// Authenticated Routes
require __DIR__.'/account.php';
require __DIR__.'/admin.php';

// Public Routes (Login/Register)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Fallback
Route::fallback(fn() => response()->view('errors.404', [], 404));

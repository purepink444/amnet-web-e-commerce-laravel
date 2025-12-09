<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{ProductController, CartController, ClientProductController};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API v1 with proper versioning, rate limiting, and CORS
Route::prefix('v1')->name('api.v1.')->middleware(['api', 'throttle:api'])->group(function () {

    // Public endpoints (no authentication required)
    Route::get('products/featured', [ClientProductController::class, 'featured']);
    Route::get('products/search', [ClientProductController::class, 'search']);
    Route::get('products/{product}/related', [ClientProductController::class, 'related']);
    Route::get('products/{product}/reviews', [ClientProductController::class, 'reviews']);

    // Authenticated endpoints
    Route::middleware('auth:sanctum')->group(function () {
        // Admin products management
        Route::apiResource('products', ProductController::class);

        // Cart management
        Route::get('cart', [CartController::class, 'index']);
        Route::post('cart/items', [CartController::class, 'add']);
        Route::put('cart/items/{item}', [CartController::class, 'update']);
        Route::delete('cart/items/{item}', [CartController::class, 'remove']);
        Route::get('cart/count', [CartController::class, 'count']);
    });
});

// Legacy API (deprecated - will be removed in v3)
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::apiResource('products', ProductController::class)->names([
        'index' => 'legacy.products.index',
        'store' => 'legacy.products.store',
        'show' => 'legacy.products.show',
        'update' => 'legacy.products.update',
        'destroy' => 'legacy.products.destroy',
    ]);
});
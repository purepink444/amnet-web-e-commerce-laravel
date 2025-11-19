<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController,
    AdminProductController,
    AdminOrderController,
    AdminUserController,
    AdminReportController,
    AdminCategoryController,
    AdminBrandController
};

Route::middleware(['auth', \App\Http\Middleware\RolesMiddleware::class . ':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', AdminProductController::class);
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('brands', AdminBrandController::class);
        Route::resource('users', AdminUserController::class);

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
            Route::patch('/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{id}', [AdminOrderController::class, 'destroy'])->name('destroy');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [AdminReportController::class, 'index'])->name('index');
            Route::get('/sales', [AdminReportController::class, 'sales'])->name('sales');
            Route::get('/products', [AdminReportController::class, 'products'])->name('products');
            Route::get('/customers', [AdminReportController::class, 'customers'])->name('customers');
            Route::get('/export/{type}', [AdminReportController::class, 'export'])->name('export');
        });
});

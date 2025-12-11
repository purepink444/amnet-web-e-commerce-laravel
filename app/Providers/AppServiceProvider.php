<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CacheService;
use App\Services\DataStructureService;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\EloquentProductRepository;
use App\Services\Product\ProductService;
use App\Services\Product\AdminProductService;
use App\Services\Cache\ProductCacheService;
use App\Services\Cache\AdminCacheService;
use App\Services\Cache\AdvancedCacheService;
use App\Services\Logging\ApiLogger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);

        // Service bindings
        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });

        $this->app->singleton(DataStructureService::class, function ($app) {
            return new DataStructureService();
        });

        $this->app->singleton(ProductCacheService::class, function ($app) {
            return new ProductCacheService();
        });

        $this->app->singleton(ApiLogger::class, function ($app) {
            return new ApiLogger();
        });

        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(ProductRepositoryInterface::class),
                $app->make(ProductCacheService::class),
                $app->make(ApiLogger::class)
            );
        });

        // New refactored services
        $this->app->singleton(AdvancedCacheService::class, function ($app) {
            return new AdvancedCacheService();
        });

        $this->app->singleton(AdminCacheService::class, function ($app) {
            return new AdminCacheService(
                $app->make(AdvancedCacheService::class)
            );
        });

        $this->app->singleton(AdminProductService::class, function ($app) {
            return new AdminProductService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

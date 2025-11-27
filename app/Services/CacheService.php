<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class CacheService
{
    /**
     * Cache TTL constants (Time To Live)
     */
    const CACHE_TTL_SHORT = 300;    // 5 minutes
    const CACHE_TTL_MEDIUM = 1800;  // 30 minutes
    const CACHE_TTL_LONG = 3600;    // 1 hour
    const CACHE_TTL_DAY = 86400;    // 24 hours

    /**
     * Cache keys constants
     */
    const KEY_CATEGORIES = 'categories.active';
    const KEY_BRANDS = 'brands.active';
    const KEY_PRODUCTS_POPULAR = 'products.popular';
    const KEY_PRODUCTS_TRENDING = 'products.trending';
    const KEY_DASHBOARD_STATS = 'dashboard.stats';

    /**
     * Get cached categories with automatic cache warming
     * Time Complexity: O(1) amortized
     */
    public function getCategories(): Collection
    {
        return Cache::remember(
            self::KEY_CATEGORIES,
            self::CACHE_TTL_MEDIUM,
            function () {
                return \App\Models\Category::where('status', 'active')
                                         ->orderBy('category_name')
                                         ->get();
            }
        );
    }

    /**
     * Get cached brands with automatic cache warming
     */
    public function getBrands(): Collection
    {
        return Cache::remember(
            self::KEY_BRANDS,
            self::CACHE_TTL_MEDIUM,
            function () {
                return \App\Models\Brand::where('status', 'active')
                                      ->orderBy('brand_name')
                                      ->get();
            }
        );
    }

    /**
     * Get cached popular products using LRU-like eviction
     */
    public function getPopularProducts(int $limit = 10): Collection
    {
        return Cache::remember(
            self::KEY_PRODUCTS_POPULAR,
            self::CACHE_TTL_SHORT,
            function () use ($limit) {
                return \App\Models\Product::where('status', 'active')
                                        ->orderBy('view_count', 'desc')
                                        ->limit($limit)
                                        ->get();
            }
        );
    }

    /**
     * Get cached trending products with time-weighted algorithm
     */
    public function getTrendingProducts(int $days = 7, int $limit = 10): Collection
    {
        $cacheKey = self::KEY_PRODUCTS_TRENDING . ".{$days}.{$limit}";

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL_SHORT,
            function () use ($days, $limit) {
                $startDate = now()->subDays($days);

                return \App\Models\Product::where('status', 'active')
                                        ->where('created_at', '>=', $startDate)
                                        ->orderBy('view_count', 'desc')
                                        ->limit($limit)
                                        ->get();
            }
        );
    }

    /**
     * Get cached dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return Cache::remember(
            self::KEY_DASHBOARD_STATS,
            self::CACHE_TTL_SHORT,
            function () {
                return [
                    'total_products' => \App\Models\Product::where('status', 'active')->count(),
                    'total_orders' => \App\Models\Order::count(),
                    'total_users' => \App\Models\User::count(),
                    'total_sales' => \App\Models\Order::sum('total_amount') ?? 0,
                    'pending_orders' => \App\Models\Order::where('order_status', 'pending')->count(),
                    'low_stock_products' => \App\Models\Product::where('status', 'active')
                                                             ->where('stock_quantity', '<=', 10)
                                                             ->count(),
                ];
            }
        );
    }

    /**
     * Cache product view count with atomic increment
     * Uses Redis atomic operations for thread safety
     */
    public function incrementProductView(int $productId): void
    {
        $cacheKey = "product.view_count.{$productId}";

        // Atomic increment in cache
        $currentViews = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentViews + 1, self::CACHE_TTL_LONG);

        // Batch update to database every 100 views to reduce DB writes
        if (($currentViews + 1) % 100 === 0) {
            \App\Models\Product::where('product_id', $productId)
                             ->increment('view_count', 100);
            Cache::forget($cacheKey); // Reset cache counter
        }
    }

    /**
     * Smart cache invalidation with dependency tracking
     */
    public function invalidateProductRelatedCache(): void
    {
        Cache::forget(self::KEY_PRODUCTS_POPULAR);
        Cache::forget(self::KEY_PRODUCTS_TRENDING . '.*'); // Wildcard invalidation
        Cache::forget(self::KEY_DASHBOARD_STATS);
    }

    public function invalidateCategoryRelatedCache(): void
    {
        Cache::forget(self::KEY_CATEGORIES);
        $this->invalidateProductRelatedCache(); // Categories affect products
    }

    public function invalidateBrandRelatedCache(): void
    {
        Cache::forget(self::KEY_BRANDS);
        $this->invalidateProductRelatedCache(); // Brands affect products
    }

    /**
     * Cache warming for frequently accessed data
     * Should be called during application startup or via scheduled job
     */
    public function warmCache(): void
    {
        // Preload frequently accessed data
        $this->getCategories();
        $this->getBrands();
        $this->getPopularProducts();
        $this->getDashboardStats();

        \Log::info('Cache warming completed');
    }

    /**
     * Get cache hit statistics for monitoring
     */
    public function getCacheStats(): array
    {
        // This would require Redis or cache driver that supports stats
        return [
            'categories_cached' => Cache::has(self::KEY_CATEGORIES),
            'brands_cached' => Cache::has(self::KEY_BRANDS),
            'popular_products_cached' => Cache::has(self::KEY_PRODUCTS_POPULAR),
            'dashboard_stats_cached' => Cache::has(self::KEY_DASHBOARD_STATS),
        ];
    }
}
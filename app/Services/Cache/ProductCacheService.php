<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class ProductCacheService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const SHORT_CACHE_TTL = 900; // 15 minutes
    private const LONG_CACHE_TTL = 7200; // 2 hours

    /**
     * Cache with product tags for easy invalidation.
     */
    public function rememberWithTags(string $key, callable $callback, int $ttl = self::CACHE_TTL)
    {
        return Cache::tags(['products'])->remember($key, $ttl, $callback);
    }

    /**
     * Invalidate specific product cache.
     */
    public function invalidateProduct(int $productId): void
    {
        $keys = [
            "product_{$productId}",
            "product_{$productId}_full",
            "product_{$productId}_reviews",
            "product_{$productId}_images",
            "product_{$productId}_related",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Also clear from tagged cache
        Cache::tags(['products'])->forget("product_{$productId}_full");
        Cache::tags(['products'])->forget("product_{$productId}_reviews");
    }

    /**
     * Invalidate all product-related caches.
     */
    public function invalidateProductRelatedCache(): void
    {
        Cache::tags(['products'])->flush();
    }

    /**
     * Invalidate category-related caches.
     */
    public function invalidateCategoryRelatedCache(): void
    {
        Cache::tags(['categories'])->flush();
    }

    /**
     * Invalidate brand-related caches.
     */
    public function invalidateBrandRelatedCache(): void
    {
        Cache::tags(['brands'])->flush();
    }

    /**
     * Cache product search results.
     */
    public function cacheSearchResults(string $query, array $filters, $results, int $ttl = self::SHORT_CACHE_TTL): void
    {
        $key = 'search_' . md5($query . serialize($filters));
        Cache::tags(['products', 'search'])->put($key, $results, $ttl);
    }

    /**
     * Get cached search results.
     */
    public function getCachedSearchResults(string $query, array $filters)
    {
        $key = 'search_' . md5($query . serialize($filters));
        return Cache::tags(['products', 'search'])->get($key);
    }

    /**
     * Cache product statistics.
     */
    public function cacheProductStats(array $stats, int $ttl = self::SHORT_CACHE_TTL): void
    {
        Cache::tags(['products', 'stats'])->put('product_stats', $stats, $ttl);
    }

    /**
     * Get cached product statistics.
     */
    public function getCachedProductStats()
    {
        return Cache::tags(['products', 'stats'])->get('product_stats');
    }

    /**
     * Cache featured products.
     */
    public function cacheFeaturedProducts(Collection $products, int $ttl = self::LONG_CACHE_TTL): void
    {
        Cache::tags(['products', 'featured'])->put('featured_products', $products, $ttl);
    }

    /**
     * Get cached featured products.
     */
    public function getCachedFeaturedProducts()
    {
        return Cache::tags(['products', 'featured'])->get('featured_products');
    }

    /**
     * Cache trending products.
     */
    public function cacheTrendingProducts(Collection $products, int $days = 7, int $ttl = self::SHORT_CACHE_TTL): void
    {
        $key = "trending_products_{$days}";
        Cache::tags(['products', 'trending'])->put($key, $products, $ttl);
    }

    /**
     * Get cached trending products.
     */
    public function getCachedTrendingProducts(int $days = 7)
    {
        $key = "trending_products_{$days}";
        return Cache::tags(['products', 'trending'])->get($key);
    }

    /**
     * Warm up common caches.
     */
    public function warmUpCaches(): array
    {
        $results = [
            'success' => true,
            'warmed_caches' => [],
            'errors' => []
        ];

        try {
            // This would typically be called by a scheduled job
            // For now, just log that warming is needed
            $results['warmed_caches'][] = 'cache_warming_scheduled';
        } catch (\Exception $e) {
            $results['errors'][] = $e->getMessage();
            $results['success'] = false;
        }

        return $results;
    }

    /**
     * Get cache statistics.
     */
    public function getCacheStats(): array
    {
        return [
            'product_cache_hit_ratio' => $this->calculateHitRatio('products'),
            'search_cache_hit_ratio' => $this->calculateHitRatio('search'),
            'cache_memory_usage' => $this->getMemoryUsage(),
            'cache_keys_count' => $this->getKeysCount(),
        ];
    }

    /**
     * Calculate cache hit ratio (simplified).
     */
    private function calculateHitRatio(string $tag): float
    {
        // This is a simplified implementation
        // In a real-world scenario, you'd track hits/misses
        try {
            $keys = Cache::tags([$tag])->get('cache_stats', ['hits' => 0, 'misses' => 0]);
            $total = $keys['hits'] + $keys['misses'];
            return $total > 0 ? ($keys['hits'] / $total) * 100 : 0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Get cache memory usage (Redis specific).
     */
    private function getMemoryUsage(): ?int
    {
        try {
            // This would work with Redis
            // return Cache::store('redis')->getRedis()->info('memory')['used_memory'];
            return null; // Placeholder
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get approximate keys count.
     */
    private function getKeysCount(): int
    {
        // This is approximate and depends on cache driver
        return 0; // Placeholder
    }

    /**
     * Clear all caches (emergency use only).
     */
    public function clearAllCaches(): bool
    {
        try {
            Cache::flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clear expired caches.
     */
    public function clearExpiredCaches(): int
    {
        // This would typically be handled by the cache driver
        // For Redis, expired keys are automatically cleaned up
        return 0;
    }
}
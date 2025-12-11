<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AdvancedCacheService
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
     * Multi-level caching with Redis and local cache
     * Uses Redis for distributed caching with tags support
     * Falls back to local cache if Redis is unavailable
     */
    public function rememberWithTags(string $key, $tags, int $ttl, callable $callback)
    {
        $redisKey = $this->getRedisKey($key);
        $localKey = "local:{$key}";

        // Check if Redis is available
        $redisAvailable = $this->isRedisAvailable();

        if ($redisAvailable) {
            try {
                // Check Redis first (distributed cache)
                $cached = Redis::get($redisKey);
                if ($cached !== null) {
                    $this->logCacheHit($key, 'redis');
                    return unserialize($cached);
                }
            } catch (\Exception $e) {
                Log::warning('Redis unavailable during cache read, falling back to local cache', [
                    'key' => $key,
                    'error' => $e->getMessage()
                ]);
                $redisAvailable = false;
            }
        }

        // Check local cache (application cache)
        if (Cache::has($localKey)) {
            $data = Cache::get($localKey);
            $this->logCacheHit($key, 'local');

            // Try to store in Redis for other instances if available
            if ($redisAvailable) {
                try {
                    Redis::setex($redisKey, $ttl, serialize($data));
                } catch (\Exception $e) {
                    Log::warning('Failed to store in Redis during cache hit', [
                        'key' => $key,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return $data;
        }

        // Generate fresh data
        $this->logCacheMiss($key);
        $data = $callback();

        // Cache in local cache (always available)
        Cache::put($localKey, $data, min($ttl, 3600)); // Local cache max 1 hour

        // Try to cache in Redis if available
        if ($redisAvailable) {
            try {
                Redis::setex($redisKey, $ttl, serialize($data));

                // Cache tags for invalidation
                foreach ((array) $tags as $tag) {
                    Redis::sadd("tag:{$tag}", $redisKey);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to store in Redis during cache write', [
                    'key' => $key,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $data;
    }

    /**
     * Check if Redis is available and working
     */
    private function isRedisAvailable(): bool
    {
        try {
            // Try to ping Redis using full namespace
            \Illuminate\Support\Facades\Redis::ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Invalidate cache by tags (distributed invalidation)
     */
    public function invalidateTags(array $tags): void
    {
        if (!$this->isRedisAvailable()) {
            Log::warning('Cannot invalidate Redis tags - Redis unavailable');
            return;
        }

        foreach ($tags as $tag) {
            try {
                $tagKey = "tag:{$tag}";
                $keys = Redis::smembers($tagKey);

                if (!empty($keys)) {
                    // Delete all keys associated with this tag
                    Redis::del(array_merge([$tagKey], $keys));

                    Log::info('Cache tag invalidated', [
                        'tag' => $tag,
                        'keys_invalidated' => count($keys)
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to invalidate cache tag', [
                    'tag' => $tag,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Cache warming with priority levels
     */
    public function warmCachePriority(array $priorities = ['critical', 'high', 'medium']): void
    {
        $startTime = microtime(true);

        foreach ($priorities as $priority) {
            $this->warmCacheByPriority($priority);
        }

        $duration = round((microtime(true) - $startTime) * 1000, 2);
        Log::info('Cache warming completed', [
            'duration_ms' => $duration,
            'priorities' => $priorities
        ]);
    }

    /**
     * Distributed locking for cache operations
     * Prevents cache stampede and ensures data consistency
     */
    public function lock(string $key, int $ttl = 10): bool
    {
        if (!$this->isRedisAvailable()) {
            return false; // Cannot lock without Redis
        }

        try {
            $lockKey = "lock:{$key}";
            $result = Redis::set($lockKey, '1', 'EX', $ttl, 'NX');

            if ($result) {
                Log::debug('Cache lock acquired', ['key' => $key]);
            }

            return (bool) $result;
        } catch (\Exception $e) {
            Log::warning('Failed to acquire cache lock', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function unlock(string $key): void
    {
        if (!$this->isRedisAvailable()) {
            return;
        }

        try {
            $lockKey = "lock:{$key}";
            Redis::del($lockKey);
            Log::debug('Cache lock released', ['key' => $key]);
        } catch (\Exception $e) {
            Log::warning('Failed to release cache lock', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Atomic increment with distributed locking
     */
    public function incrementWithLock(string $key, int $increment = 1, int $ttl = 3600): int
    {
        if (!$this->isRedisAvailable()) {
            // Fallback to local cache increment
            $localKey = "local:incr:{$key}";
            $current = Cache::get($localKey, 0);
            $newValue = $current + $increment;
            Cache::put($localKey, $newValue, $ttl);
            return $newValue;
        }

        try {
            $lockKey = "lock:incr:{$key}";

            // Try to acquire lock
            if (!$this->lock($lockKey, 5)) {
                // If can't acquire lock, use Redis atomic increment
                return Redis::incrby($key, $increment);
            }

            $current = Redis::get($key) ?? 0;
            $newValue = $current + $increment;

            Redis::setex($key, $ttl, $newValue);
            return $newValue;
        } catch (\Exception $e) {
            Log::warning('Redis increment failed, using local cache', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            // Fallback to local cache
            $localKey = "local:incr:{$key}";
            $current = Cache::get($localKey, 0);
            $newValue = $current + $increment;
            Cache::put($localKey, $newValue, $ttl);
            return $newValue;
        } finally {
            $this->unlock($lockKey);
        }
    }

    /**
     * Smart cache invalidation with dependency tracking
     */
    public function invalidateProductRelatedCache(): void
    {
        $this->invalidateTags(['products']);
        Redis::del([
            self::KEY_PRODUCTS_POPULAR,
            self::KEY_PRODUCTS_TRENDING . ':*', // Pattern deletion
        ]);
        Redis::del(self::KEY_DASHBOARD_STATS);
    }

    public function invalidateCategoryRelatedCache(): void
    {
        $this->invalidateTags(['categories']);
        $this->invalidateProductRelatedCache(); // Categories affect products
    }

    public function invalidateBrandRelatedCache(): void
    {
        $this->invalidateTags(['brands']);
        $this->invalidateProductRelatedCache(); // Brands affect products
    }

    /**
     * Get cached categories with automatic cache warming
     */
    public function getCategories(): mixed
    {
        return $this->rememberWithTags(
            self::KEY_CATEGORIES,
            ['categories'],
            self::CACHE_TTL_MEDIUM,
            function () {
                return \App\Models\Category::where('is_active', true)
                                          ->orderBy('category_name')
                                          ->get();
            }
        );
    }

    /**
     * Get cached brands with automatic cache warming
     */
    public function getBrands(): mixed
    {
        return $this->rememberWithTags(
            self::KEY_BRANDS,
            ['brands'],
            self::CACHE_TTL_MEDIUM,
            function () {
                return \App\Models\Brand::where('is_active', true)
                                        ->orderBy('brand_name')
                                        ->get();
            }
        );
    }

    /**
     * Get cached popular products using LRU-like eviction
     */
    public function getPopularProducts(int $limit = 10): mixed
    {
        return $this->rememberWithTags(
            self::KEY_PRODUCTS_POPULAR,
            ['products'],
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
    public function getTrendingProducts(int $days = 7, int $limit = 10): mixed
    {
        $cacheKey = self::KEY_PRODUCTS_TRENDING . ".{$days}.{$limit}";

        return $this->rememberWithTags(
            $cacheKey,
            ['products'],
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
        return $this->rememberWithTags(
            self::KEY_DASHBOARD_STATS,
            ['products', 'orders', 'users'],
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
        $localKey = "local:view_count:{$productId}";

        if ($this->isRedisAvailable()) {
            try {
                // Atomic increment in Redis
                $currentViews = Redis::incr($cacheKey);

                // Batch update to database every 100 views to reduce DB writes
                if ($currentViews % 100 === 0) {
                    \App\Models\Product::where('product_id', $productId)
                                      ->increment('view_count', 100);

                    // Reset Redis counter after batch update
                    Redis::set($cacheKey, 0);
                }

                // Set expiry to ensure counter doesn't grow indefinitely
                Redis::expire($cacheKey, self::CACHE_TTL_LONG);
            } catch (\Exception $e) {
                Log::warning('Redis increment failed, using local cache', [
                    'product_id' => $productId,
                    'error' => $e->getMessage()
                ]);

                // Fallback to local increment
                $currentViews = Cache::get($localKey, 0) + 1;
                Cache::put($localKey, $currentViews, self::CACHE_TTL_LONG);

                // Batch update to database every 50 views (more frequent for local cache)
                if ($currentViews % 50 === 0) {
                    \App\Models\Product::where('product_id', $productId)
                                      ->increment('view_count', 50);
                    Cache::forget($localKey);
                }
            }
        } else {
            // Redis not available, use local cache only
            $currentViews = Cache::get($localKey, 0) + 1;
            Cache::put($localKey, $currentViews, self::CACHE_TTL_LONG);

            // Update database every 25 views (most frequent for no Redis)
            if ($currentViews % 25 === 0) {
                \App\Models\Product::where('product_id', $productId)
                                  ->increment('view_count', 25);
                Cache::forget($localKey);
            }
        }
    }

    /**
     * Get cache statistics for monitoring
     */
    public function getCacheStats(): array
    {
        try {
            $info = Redis::info();

            return [
                'redis_connected' => true,
                'used_memory' => $info['used_memory_human'] ?? 'Unknown',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'cache_hit_rate' => $this->calculateHitRate(),
                'categories_cached' => Redis::exists(self::KEY_CATEGORIES),
                'brands_cached' => Redis::exists(self::KEY_BRANDS),
                'popular_products_cached' => Redis::exists(self::KEY_PRODUCTS_POPULAR),
                'dashboard_stats_cached' => Redis::exists(self::KEY_DASHBOARD_STATS),
            ];
        } catch (\Exception $e) {
            return [
                'redis_connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Calculate cache hit rate (simplified version)
     */
    private function calculateHitRate(): float
    {
        try {
            $info = Redis::info();
            $hits = (int) ($info['keyspace_hits'] ?? 0);
            $misses = (int) ($info['keyspace_misses'] ?? 0);

            $total = $hits + $misses;
            return $total > 0 ? round(($hits / $total) * 100, 2) : 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Cache warming for frequently accessed data
     */
    private function warmCacheByPriority(string $priority): void
    {
        match($priority) {
            'critical' => $this->warmCriticalCache(),
            'high' => $this->warmHighPriorityCache(),
            'medium' => $this->warmMediumPriorityCache(),
            default => null,
        };
    }

    private function warmCriticalCache(): void
    {
        // Most frequently accessed data
        $this->getCategories();
        $this->getBrands();
        $this->getDashboardStats();
    }

    private function warmHighPriorityCache(): void
    {
        // Important but less critical data
        $this->getPopularProducts();
        $this->getTrendingProducts(7, 10);
    }

    private function warmMediumPriorityCache(): void
    {
        // Nice to have cached data
        $this->getTrendingProducts(30, 20);
    }

    private function getRedisKey(string $key): string
    {
        return config('cache.prefix', 'laravel-cache:') . $key;
    }

    private function logCacheHit(string $key, string $source): void
    {
        Log::debug('Cache hit', [
            'key' => $key,
            'source' => $source,
            'timestamp' => now()->toISOString()
        ]);
    }

    private function logCacheMiss(string $key): void
    {
        Log::debug('Cache miss', [
            'key' => $key,
            'timestamp' => now()->toISOString()
        ]);
    }
}
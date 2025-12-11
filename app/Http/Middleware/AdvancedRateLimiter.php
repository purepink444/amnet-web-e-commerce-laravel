<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdvancedRateLimiter
{
    /**
     * Handle an incoming request with advanced rate limiting
     *
     * Features:
     * - User-based and IP-based limiting
     * - Burst handling for occasional spikes
     * - Route-specific limits
     * - Distributed rate limiting with Redis
     * - Automatic cleanup of expired keys
     */
    public function handle(Request $request, Closure $next, string $limiter = 'api'): Response
    {
        $key = $this->getRateLimitKey($request, $limiter);
        $limits = $this->getLimits($limiter);

        // Check current usage
        $current = (int) Redis::get($key);

        // Check if within normal limit
        if ($current >= $limits['max_attempts']) {
            // Check burst allowance
            $burstKey = $key . ':burst';
            $burstCount = (int) Redis::get($burstKey);

            if ($burstCount >= $limits['burst_limit']) {
                // Rate limit exceeded
                return $this->buildRateLimitResponse($request, $limits, $key);
            }

            // Allow burst request
            Redis::incr($burstKey);
            Redis::expire($burstKey, $limits['burst_window']);

            $this->logBurstUsage($request, $burstKey, $burstCount + 1);
        }

        // Increment normal counter
        $newCount = Redis::incr($key);
        Redis::expire($key, $limits['decay_seconds']);

        $response = $next($request);

        // Add rate limit headers to response
        $response->headers->set('X-RateLimit-Limit', $limits['max_attempts']);
        $response->headers->set('X-RateLimit-Remaining', max(0, $limits['max_attempts'] - $newCount));
        $response->headers->set('X-RateLimit-Reset', time() + $limits['decay_seconds']);
        $response->headers->set('X-RateLimit-Burst-Limit', $limits['burst_limit']);
        $response->headers->set('X-RateLimit-Burst-Remaining', max(0, $limits['burst_limit'] - (int) Redis::get($key . ':burst')));

        return $response;
    }

    /**
     * Generate rate limit key based on user, IP, and route
     */
    private function getRateLimitKey(Request $request, string $limiter): string
    {
        // Use user ID if authenticated, otherwise IP address
        $identifier = auth()->id() ?? $request->ip();

        // Include route name for route-specific limiting
        $route = $request->route()?->getName() ?? $request->path();

        // Clean route name for Redis key
        $route = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $route);

        return "rate_limit:{$limiter}:{$identifier}:{$route}";
    }

    /**
     * Get rate limiting configuration for different limiter types
     */
    private function getLimits(string $limiter): array
    {
        return match($limiter) {
            'api' => [
                'max_attempts' => (int) env('RATE_LIMIT_API_MAX', 1000),
                'decay_seconds' => (int) env('RATE_LIMIT_API_DECAY', 3600), // 1 hour
                'burst_limit' => (int) env('RATE_LIMIT_API_BURST', 100),
                'burst_window' => (int) env('RATE_LIMIT_API_BURST_WINDOW', 60), // 1 minute
            ],
            'auth' => [
                'max_attempts' => (int) env('RATE_LIMIT_AUTH_MAX', 5),
                'decay_seconds' => (int) env('RATE_LIMIT_AUTH_DECAY', 900), // 15 minutes
                'burst_limit' => (int) env('RATE_LIMIT_AUTH_BURST', 2),
                'burst_window' => (int) env('RATE_LIMIT_AUTH_BURST_WINDOW', 300), // 5 minutes
            ],
            'admin' => [
                'max_attempts' => (int) env('RATE_LIMIT_ADMIN_MAX', 5000),
                'decay_seconds' => (int) env('RATE_LIMIT_ADMIN_DECAY', 3600),
                'burst_limit' => (int) env('RATE_LIMIT_ADMIN_BURST', 500),
                'burst_window' => (int) env('RATE_LIMIT_ADMIN_BURST_WINDOW', 60),
            ],
            'upload' => [
                'max_attempts' => (int) env('RATE_LIMIT_UPLOAD_MAX', 50),
                'decay_seconds' => (int) env('RATE_LIMIT_UPLOAD_DECAY', 3600),
                'burst_limit' => (int) env('RATE_LIMIT_UPLOAD_BURST', 10),
                'burst_window' => (int) env('RATE_LIMIT_UPLOAD_BURST_WINDOW', 300),
            ],
            default => [
                'max_attempts' => 100,
                'decay_seconds' => 60,
                'burst_limit' => 20,
                'burst_window' => 10,
            ],
        };
    }

    /**
     * Build rate limit exceeded response
     */
    private function buildRateLimitResponse(Request $request, array $limits, string $key): Response
    {
        $retryAfter = $limits['decay_seconds'];

        // Calculate reset time more accurately
        $resetTime = time() + $retryAfter;

        $this->logRateLimitExceeded($request, $key, $limits);

        return response()->json([
            'error' => 'Too Many Requests',
            'message' => 'Rate limit exceeded. Please try again later.',
            'retry_after' => $retryAfter,
            'retry_at' => date('c', $resetTime),
            'limit' => $limits['max_attempts'],
            'decay_seconds' => $limits['decay_seconds'],
        ], 429, [
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $limits['max_attempts'],
            'X-RateLimit-Reset' => $resetTime,
            'X-RateLimit-Burst-Limit' => $limits['burst_limit'],
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Log rate limit exceeded events
     */
    private function logRateLimitExceeded(Request $request, string $key, array $limits): void
    {
        Log::warning('Rate limit exceeded', [
            'key' => $key,
            'limiter' => $key,
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'limits' => $limits,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log burst usage for monitoring
     */
    private function logBurstUsage(Request $request, string $burstKey, int $burstCount): void
    {
        Log::info('Rate limit burst used', [
            'key' => $burstKey,
            'burst_count' => $burstCount,
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'url' => $request->fullUrl(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get current rate limit status for a key
     * Useful for debugging and monitoring
     */
    public function getRateLimitStatus(string $key): array
    {
        $current = (int) Redis::get($key);
        $burstCurrent = (int) Redis::get($key . ':burst');
        $ttl = Redis::ttl($key);
        $burstTtl = Redis::ttl($key . ':burst');

        return [
            'current' => $current,
            'burst_current' => $burstCurrent,
            'ttl' => $ttl,
            'burst_ttl' => $burstTtl,
            'key' => $key,
        ];
    }

    /**
     * Reset rate limit for a specific key
     * Useful for administrative purposes
     */
    public function resetRateLimit(string $key): bool
    {
        $deleted = Redis::del([$key, $key . ':burst']);

        Log::info('Rate limit reset', [
            'key' => $key,
            'keys_deleted' => $deleted,
            'timestamp' => now()->toISOString(),
        ]);

        return $deleted > 0;
    }

    /**
     * Get rate limit statistics for monitoring
     */
    public function getRateLimitStats(string $pattern = 'rate_limit:*'): array
    {
        try {
            $keys = Redis::keys($pattern);

            $stats = [
                'total_keys' => count($keys),
                'keys' => [],
                'summary' => [
                    'api_limiters' => 0,
                    'auth_limiters' => 0,
                    'admin_limiters' => 0,
                    'other_limiters' => 0,
                ]
            ];

            foreach (array_slice($keys, 0, 100) as $key) { // Limit to 100 keys for performance
                $parts = explode(':', $key);
                if (count($parts) >= 3) {
                    $limiter = $parts[1];
                    $stats['summary'][$this->categorizeLimiter($limiter)]++;

                    $stats['keys'][] = [
                        'key' => $key,
                        'limiter' => $limiter,
                        'current' => (int) Redis::get($key),
                        'ttl' => Redis::ttl($key),
                    ];
                }
            }

            return $stats;
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'total_keys' => 0,
            ];
        }
    }

    /**
     * Categorize limiter type for statistics
     */
    private function categorizeLimiter(string $limiter): string
    {
        return match($limiter) {
            'api' => 'api_limiters',
            'auth' => 'auth_limiters',
            'admin' => 'admin_limiters',
            default => 'other_limiters',
        };
    }
}
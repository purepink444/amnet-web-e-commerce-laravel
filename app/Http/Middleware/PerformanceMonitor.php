<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Logging\ApiLogger;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitor
{
    private ApiLogger $logger;

    public function __construct(ApiLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        $startCpu = getrusage();

        // Add request ID if not present
        if (!$request->hasHeader('X-Request-ID')) {
            $request->headers->set('X-Request-ID', uniqid('req_', true));
        }

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $endCpu = getrusage();

        $this->logPerformanceMetrics($request, $response, $startTime, $endTime, $startMemory, $endMemory, $startCpu, $endCpu);

        return $response;
    }

    /**
     * Log performance metrics.
     */
    private function logPerformanceMetrics(
        Request $request,
        Response $response,
        float $startTime,
        float $endTime,
        int $startMemory,
        int $endMemory,
        array $startCpu,
        array $endCpu
    ): void {
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;
        $peakMemory = memory_get_peak_usage();

        // Calculate CPU usage
        $cpuUserTime = ($endCpu['ru_utime.tv_sec'] - $startCpu['ru_utime.tv_sec']) * 1000000
                      + ($endCpu['ru_utime.tv_usec'] - $startCpu['ru_utime.tv_usec']);
        $cpuSystemTime = ($endCpu['ru_stime.tv_sec'] - $startCpu['ru_stime.tv_sec']) * 1000000
                        + ($endCpu['ru_stime.tv_usec'] - $startCpu['ru_stime.tv_usec']);
        $cpuTotalTime = $cpuUserTime + $cpuSystemTime;

        $metadata = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'response_size' => strlen($response->getContent()),
            'query_count' => $this->getQueryCount(),
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        $this->logger->logPerformance('http_request', $executionTime, array_merge($metadata, [
            'memory_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'peak_memory_mb' => round($peakMemory / 1024 / 1024, 2),
            'cpu_user_time_ms' => round($cpuUserTime / 1000, 2),
            'cpu_system_time_ms' => round($cpuSystemTime / 1000, 2),
            'cpu_total_time_ms' => round($cpuTotalTime / 1000, 2),
        ]));

        // Alert on performance issues
        $this->checkPerformanceThresholds($executionTime, $memoryUsage, $response->getStatusCode());
    }

    /**
     * Check performance thresholds and alert if needed.
     */
    private function checkPerformanceThresholds(float $executionTime, int $memoryUsage, int $statusCode): void
    {
        $alerts = [];

        // Slow request alert
        if ($executionTime > 2000) { // > 2 seconds
            $alerts[] = "Slow request: {$executionTime}ms";
        }

        // High memory usage alert
        $memoryMb = $memoryUsage / 1024 / 1024;
        if ($memoryMb > 50) { // > 50MB
            $alerts[] = "High memory usage: {$memoryMb}MB";
        }

        // Error response alert
        if ($statusCode >= 500) {
            $alerts[] = "Server error: HTTP {$statusCode}";
        }

        if (!empty($alerts)) {
            Log::warning('Performance Alert', [
                'alerts' => $alerts,
                'execution_time_ms' => round($executionTime, 2),
                'memory_usage_mb' => round($memoryMb, 2),
                'status_code' => $statusCode,
                'url' => request()->fullUrl(),
                'request_id' => request()->header('X-Request-ID'),
                'timestamp' => now()->toISOString(),
            ]);
        }
    }

    /**
     * Get database query count for this request.
     */
    private function getQueryCount(): int
    {
        $queryLog = \DB::getQueryLog();
        return count($queryLog);
    }
}
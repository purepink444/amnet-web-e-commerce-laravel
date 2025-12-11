<?php

namespace App\Services\Logging;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ApiLogger
{
    /**
     * Log API request details.
     */
    public function logRequest(Request $request, ?User $user = null): void
    {
        $context = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $user?->id,
            'request_id' => $request->header('X-Request-ID', uniqid('req_', true)),
            'timestamp' => now()->toISOString(),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
            'query_params' => $request->query(),
            'route' => $request->route() ? $request->route()->getName() : null,
        ];

        Log::info('API Request', $context);
    }

    /**
     * Log API response details.
     */
    public function logResponse(Request $request, $response, float $executionTime): void
    {
        $context = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'execution_time_ms' => round($executionTime, 2),
            'response_size' => strlen($response->getContent()),
            'request_id' => $request->header('X-Request-ID'),
            'timestamp' => now()->toISOString(),
        ];

        // Log based on response status
        if ($response->getStatusCode() >= 500) {
            Log::error('API Error Response', $context);
        } elseif ($response->getStatusCode() >= 400) {
            Log::warning('API Client Error Response', $context);
        } else {
            Log::info('API Success Response', $context);
        }
    }

    /**
     * Log application errors with context.
     */
    public function logError(\Throwable $exception, array $context = []): void
    {
        $errorContext = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'trace' => $exception->getTraceAsString(),
            'previous' => $exception->getPrevious()?->getMessage(),
            'request_id' => request()->header('X-Request-ID', 'unknown'),
            'user_id' => auth()->id(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            'context' => $context,
        ];

        // Log based on exception type
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            Log::warning('Validation Error', $errorContext);
        } elseif ($exception instanceof \Illuminate\Database\QueryException) {
            Log::error('Database Error', array_merge($errorContext, [
                'sql' => $exception->getSql(),
                'bindings' => $exception->getBindings(),
            ]));
        } elseif ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            Log::warning('Authentication Error', $errorContext);
        } elseif ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            Log::warning('Authorization Error', $errorContext);
        } else {
            Log::error('Application Error', $errorContext);
        }
    }

    /**
     * Log business logic events.
     */
    public function logBusinessEvent(string $event, array $data = []): void
    {
        $context = [
            'event' => $event,
            'user_id' => auth()->id(),
            'request_id' => request()->header('X-Request-ID'),
            'timestamp' => now()->toISOString(),
            'data' => $data,
        ];

        Log::info('Business Event: ' . $event, $context);
    }

    /**
     * Log performance metrics.
     */
    public function logPerformance(string $operation, float $executionTime, array $metadata = []): void
    {
        $context = [
            'operation' => $operation,
            'execution_time_ms' => round($executionTime, 2),
            'memory_usage_mb' => round(memory_get_usage() / 1024 / 1024, 2),
            'peak_memory_mb' => round(memory_get_peak_usage() / 1024 / 1024, 2),
            'request_id' => request()->header('X-Request-ID'),
            'timestamp' => now()->toISOString(),
            'metadata' => $metadata,
        ];

        // Alert on slow operations
        if ($executionTime > 1000) {
            Log::warning('Slow Operation Detected', $context);
        } else {
            Log::info('Performance Metric', $context);
        }
    }

    /**
     * Log security events.
     */
    public function logSecurityEvent(string $event, array $data = []): void
    {
        $context = [
            'event' => $event,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()->header('X-Request-ID'),
            'timestamp' => now()->toISOString(),
            'data' => $data,
        ];

        Log::warning('Security Event: ' . $event, $context);
    }

    /**
     * Sanitize sensitive headers.
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = ['authorization', 'cookie', 'x-api-key', 'x-auth-token'];

        $sanitized = [];
        foreach ($headers as $key => $value) {
            if (in_array(strtolower($key), $sensitiveHeaders)) {
                $sanitized[$key] = '[REDACTED]';
            } else {
                $sanitized[$key] = is_array($value) ? $value : (string)$value;
            }
        }

        return $sanitized;
    }
}
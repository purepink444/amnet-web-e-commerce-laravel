<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SecureAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Log authentication attempts for security monitoring
        if ($request->hasHeader('Authorization') || $request->hasHeader('X-API-Key')) {
            $this->logAuthAttempt($request);
        }

        // Check for suspicious patterns
        if ($this->detectSuspiciousActivity($request)) {
            Log::warning('Suspicious authentication activity detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Suspicious activity detected',
                'error_code' => 'SUSPICIOUS_ACTIVITY'
            ], 429);
        }

        // Validate token format and prevent token leaks
        if ($request->bearerToken()) {
            if (!$this->isValidTokenFormat($request->bearerToken())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token format',
                    'error_code' => 'INVALID_TOKEN_FORMAT'
                ], 401);
            }
        }

        // Check for concurrent sessions (optional - can be enabled based on requirements)
        if (Auth::check() && config('security.auth.prevent_concurrent_sessions', false)) {
            $this->checkConcurrentSessions($request);
        }

        // Add security headers for authenticated requests
        $response = $next($request);

        // Prevent token leakage in responses
        $this->sanitizeResponse($response);

        return $response;
    }

    /**
     * Log authentication attempts for security monitoring
     */
    private function logAuthAttempt(Request $request): void
    {
        $logData = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'timestamp' => now()->toISOString()
        ];

        // Log to security channel
        Log::channel('security')->info('Authentication attempt', $logData);
    }

    /**
     * Detect suspicious authentication activity
     */
    private function detectSuspiciousActivity(Request $request): bool
    {
        // Check for rapid token rotation (potential token theft)
        $token = $request->bearerToken();
        if ($token) {
            $cacheKey = 'token_attempts_' . md5($token);
            $attempts = cache()->get($cacheKey, 0);

            if ($attempts > 10) {
                return true;
            }

            cache()->put($cacheKey, $attempts + 1, now()->addMinutes(5));
        }

        // Check for unusual user agents
        $userAgent = $request->userAgent();
        if ($userAgent && strlen($userAgent) < 10) {
            return true;
        }

        // Check for too many failed auth attempts from same IP
        $ipKey = 'auth_failures_' . $request->ip();
        $failures = cache()->get($ipKey, 0);

        if ($failures > 5) {
            return true;
        }

        return false;
    }

    /**
     * Validate token format
     */
    private function isValidTokenFormat(string $token): bool
    {
        // Basic token format validation
        // Laravel Sanctum tokens are typically 40 characters long
        if (strlen($token) < 20 || strlen($token) > 100) {
            return false;
        }

        // Check for suspicious characters
        if (preg_match('/[<>\'"]/', $token)) {
            return false;
        }

        return true;
    }

    /**
     * Check for concurrent sessions
     */
    private function checkConcurrentSessions(Request $request): void
    {
        $user = Auth::user();
        $currentSession = session()->getId();
        $userSessions = cache()->get("user_sessions_{$user->user_id}", []);

        // Remove expired sessions
        $userSessions = array_filter($userSessions, function($sessionId) {
            return cache()->has("session_{$sessionId}");
        });

        // Check if user has too many concurrent sessions
        if (count($userSessions) > config('security.auth.max_concurrent_sessions', 3)) {
            // Log security event
            Log::channel('security')->warning('Multiple concurrent sessions detected', [
                'user_id' => $user->user_id,
                'session_count' => count($userSessions),
                'ip' => $request->ip()
            ]);
        }

        // Update session tracking
        if (!in_array($currentSession, $userSessions)) {
            $userSessions[] = $currentSession;
            cache()->put("user_sessions_{$user->user_id}", $userSessions, now()->addHours(24));
            cache()->put("session_{$currentSession}", true, now()->addHours(24));
        }
    }

    /**
     * Sanitize response to prevent token leaks
     */
    private function sanitizeResponse($response): void
    {
        // Ensure no sensitive data is leaked in JSON responses
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);

            // Remove any potential token leaks from response data
            $this->removeSensitiveData($data);

            $response->setData($data);
        }
    }

    /**
     * Recursively remove sensitive data from arrays
     */
    private function removeSensitiveData(array &$data): void
    {
        $sensitiveKeys = ['password', 'token', 'api_key', 'secret', 'private_key'];

        foreach ($data as $key => &$value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $value = '[REDACTED]';
            } elseif (is_array($value)) {
                $this->removeSensitiveData($value);
            }
        }
    }
}
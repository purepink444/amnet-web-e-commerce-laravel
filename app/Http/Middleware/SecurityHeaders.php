<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
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
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // HSTS (HTTP Strict Transport Security) - only for HTTPS
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Content Security Policy
        if (config('security.csp.enabled')) {
            $csp = $this->buildCSPHeader();
            $response->headers->set('Content-Security-Policy', $csp);
        }

        // Remove server information
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    /**
     * Build Content Security Policy header
     */
    private function buildCSPHeader(): string
    {
        $csp = config('security.csp');

        $directives = [
            "default-src {$csp['default-src']}",
            "script-src {$csp['script-src']}",
            "style-src {$csp['style-src']}",
            "font-src {$csp['font-src']}",
            "img-src {$csp['img-src']}",
            "connect-src {$csp['connect-src']}",
            "frame-ancestors {$csp['frame-ancestors']}",
            "object-src {$csp['object-src']}",
            "base-uri {$csp['base-uri']}",
            "form-action {$csp['form-action']}",
        ];

        return implode('; ', $directives);
    }
}
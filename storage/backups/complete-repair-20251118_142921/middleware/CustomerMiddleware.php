<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // ตรวจสอบว่า user logged in และ role เป็น customer
        if (Auth::check() && Auth::user()->role === 'customer') {
            return $next($request);
        }

        // ถ้าไม่ใช่ customer ให้ redirect หรือ abort 403
        abort(403, 'Access denied');
    }
}

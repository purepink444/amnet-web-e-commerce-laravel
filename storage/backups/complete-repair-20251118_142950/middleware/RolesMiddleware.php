<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolesMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (auth()->check() {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = auth()->user();
        $userRole = strtolower($user->role?->role_name ?? '' ;
        $allowedRoles = array_map('strtolower', $roles);

        if (in_array($userRole, $allowedRoles) {
            if ($userRole === 'admin' {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            }

            return redirect()->route('account.profile')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return $next($request);
    }
}

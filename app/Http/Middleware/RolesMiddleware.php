<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolesMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $user->load('role'); // โหลด relationship
        
        $userRoleName = $user->role?->role_name;
        $userRole = trim(strtolower($userRoleName ?? ''));
        $requiredRole = trim(strtolower($role));
        
        // ลบ dd() ออก แล้วใช้โค้ดนี้แทน
        if ($userRole !== $requiredRole) {
            \Log::warning('Access Denied', [
                'user_id' => $user->user_id,
                'user_role' => $userRole,
                'required_role' => $requiredRole,
                'url' => $request->url()
            ]);
            
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return $next($request);
    }
}
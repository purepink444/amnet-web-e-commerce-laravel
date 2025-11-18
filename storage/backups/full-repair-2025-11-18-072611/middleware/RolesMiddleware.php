<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$roles  รองรับหลาย role เช่น 'admin', 'member'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $user->load('role'); // โหลด relationship
        
        $userRoleName = $user->role?->role_name;
        $userRole = trim(strtolower($userRoleName ?? ''));
        
        // ✅ แปลง roles ที่ส่งมาทั้งหมดเป็น lowercase
        $allowedRoles = array_map(function($role) {
            return trim(strtolower($role));
        }, $roles);
        
        // ✅ เช็คว่า user role อยู่ในรายการที่อนุญาตหรือไม่
        if (!in_array($userRole, $allowedRoles)) {
            \Log::warning('Access Denied', [
                'user_id' => $user->user_id,
                'user_role' => $userRole,
                'allowed_roles' => $allowedRoles,
                'url' => $request->url()
            ]);
            
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return $next($request);
    }
}
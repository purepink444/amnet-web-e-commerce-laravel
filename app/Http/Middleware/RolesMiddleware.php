<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolesMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            // ผู้ใช้ยังไม่ล็อกอิน → redirect login
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = auth()->user();
        $user->load('role'); // Ensure role is loaded
        $userRole = strtolower($user->role?->role_name ?? '');
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            // ผู้ใช้ล็อกอินแล้ว แต่ role ไม่ถูกต้อง → redirect ไปหน้าเหมาะสม
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            }

            return redirect()->route('account.profile')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        // ถ้า role ตรงกับ allowedRoles → ผ่านไป
        return $next($request);
    }
}

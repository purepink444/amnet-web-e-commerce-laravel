<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // แสดงหน้า login
    public function showLoginForm()
    {
        if (auth()->check()) {
            $user = auth()->user();
            return $user->role_id == 1
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        return view('auth.login');
    }

    // ดำเนินการ login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate(); // ป้องกัน session fixation

            $user = auth()->user();
            $roleName = strtolower($user->role?->role_name ?? 'member');

            return $roleName === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        return back()->withErrors([
            'username' => 'ข้อมูลเข้าสู่ระบบไม่ถูกต้อง',
        ])->onlyInput('username');
    }

    // ออกจากระบบ
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'ออกจากระบบสำเร็จ');
    }
}

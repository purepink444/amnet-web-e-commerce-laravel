<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // แสดงฟอร์ม Login
    public function showLoginForm()
    {
        return view('pages.login'); // เปลี่ยนเป็น path view ของคุณ
    }

    // ทำการ Login
    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Redirect หลัง login สำเร็จเท่านั้น
        $role = strtolower(auth()->user()->role?->role_name ?? 'member');

        return match($role) {
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('account.profile'),
        };
    }

    return back()->withErrors([
        'email' => 'Email หรือรหัสผ่านไม่ถูกต้อง',
    ]);
}

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

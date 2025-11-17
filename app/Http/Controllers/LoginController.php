<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\Support\Facades\{Auth, RateLimiter, Log};
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Maximum login attempts allowed
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lockout duration in seconds (5 minutes)
     */
    private const DECAY_SECONDS = 300;

    /**
     * Handle user login
     */
    public function login(Request $request): RedirectResponse
    {
        // Check if too many login attempts
        $this->ensureIsNotRateLimited($request);

        // Validate credentials
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'username.max' => 'ชื่อผู้ใช้ต้องไม่เกิน 50 ตัวอักษร',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
        ]);

        // Attempt login
        if ($this->attemptLogin($request, $credentials)) {
            return $this->sendLoginResponse($request);
        }

        // Login failed - increment attempts
        RateLimiter::hit($this->throttleKey($request), self::DECAY_SECONDS);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application
     */
    private function attemptLogin(Request $request, array $credentials): bool
    {
        $remember = $request->boolean('remember');

        return Auth::attempt($credentials, $remember);
    }

    /**
     * Send successful login response
     */
    private function sendLoginResponse(Request $request): RedirectResponse
    {
        // Clear rate limiter
        RateLimiter::clear($this->throttleKey($request));

        // Regenerate session for security
        $request->session()->regenerate();

        // Log successful login
        Log::info('User logged in', [
            'user_id' => Auth::id(),
            'username' => Auth::user()->username,
            'ip' => $request->ip(),
        ]);

        // Redirect based on user role or intended page
        $redirectUrl = $this->getRedirectUrl();

        return redirect()->intended($redirectUrl)
            ->with('success', 'เข้าสู่ระบบสำเร็จ');
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl(): string
    {
        $user = Auth::user();

        // Customize redirect based on user role if needed
        return match ($user->role ?? null) {
            'admin' => '/dashboard',
            'customer' => '/',
            default => '/',
        };
    }

    /**
     * Send failed login response
     */
    private function sendFailedLoginResponse(Request $request): RedirectResponse
    {
        // Log failed login attempt
        Log::warning('Failed login attempt', [
            'username' => $request->username,
            'ip' => $request->ip(),
        ]);

        $attempts = RateLimiter::attempts($this->throttleKey($request));
        $remaining = self::MAX_ATTEMPTS - $attempts;

        $message = $remaining > 0
            ? "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ {$remaining} ครั้ง)"
            : 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';

        return back()
            ->withErrors(['login' => $message])
            ->withInput($request->only('username'))
            ->with('login_attempts', $attempts);
    }

    /**
     * Ensure the login request is not rate limited
     */
    private function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), self::MAX_ATTEMPTS)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        Log::warning('Login rate limit exceeded', [
            'username' => $request->username,
            'ip' => $request->ip(),
        ]);

        throw ValidationException::withMessages([
            'username' => [
                trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ])
            ],
        ])->status(429);
    }

    /**
     * Get the rate limiting throttle key for the request
     */
    private function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower($request->input('username')) . '|' . $request->ip()
        );
    }

    /**
     * Log the user out of the application
     */
    public function logout(Request $request): RedirectResponse
    {
        // Log logout action
        Log::info('User logged out', [
            'user_id' => Auth::id(),
            'username' => Auth::user()?->username,
        ]);

        // Logout user
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'ออกจากระบบสำเร็จ');
    }

    /**
     * Show login form (if needed)
     */
    public function showLoginForm()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            return redirect($this->getRedirectUrl());
        }

        return view('pages.login');
    }
}
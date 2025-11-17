<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;
use Illuminate\Support\Facades\{Hash, DB, Log, Auth};
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function create(): View
    {
        // Redirect if already logged in
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('pages.register');
    }

    /**
     * Handle user registration
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate input
        $validated = $this->validateRegistration($request);

        try {
            DB::beginTransaction();

            // Create user
            $user = $this->createUser($validated);

            // Send welcome email (optional)
            // $this->sendWelcomeEmail($user);

            DB::commit();

            // Log registration
            Log::info('New user registered', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            // Auto login option
            if (config('auth.auto_login_after_register', false)) {
                Auth::login($user);
                return redirect()->route('home')
                    ->with('success', 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จ!');
            }

            return redirect()->route('login')
                ->with('success', 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ')
                ->with('email', $user->email);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('User registration failed', [
                'error' => $e->getMessage(),
                'email' => $request->email,
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    }

    /**
     * Validate registration data
     */
    private function validateRegistration(Request $request): array
    {
        return $request->validate([
            // Required fields
            'firstname' => ['required', 'string', 'max:100', 'regex:/^[\p{Thai}a-zA-Z\s]+$/u'],
            'lastname' => ['required', 'string', 'max:100', 'regex:/^[\p{Thai}a-zA-Z\s]+$/u'],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
                'indisposable', // ป้องกัน temporary email (ต้องติดตั้ง package)
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],

            // Optional fields
            'username' => [
                'nullable',
                'string',
                'max:50',
                'unique:users,username',
                'alpha_dash',
                'regex:/^[a-zA-Z0-9_-]+$/',
            ],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]{9,10}$/', 'unique:users,phone'],
            
            // Address fields
            'address' => ['nullable', 'string', 'max:500'],
            'subdistrict' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'zipcode' => ['nullable', 'string', 'regex:/^[0-9]{5}$/'],

            // Terms acceptance
            'terms' => ['accepted'],
        ], [
            // Custom error messages
            'firstname.required' => 'กรุณากรอกชื่อ',
            'firstname.regex' => 'ชื่อต้องเป็นภาษาไทยหรืออังกฤษเท่านั้น',
            'lastname.required' => 'กรุณากรอกนามสกุล',
            'lastname.regex' => 'นามสกุลต้องเป็นภาษาไทยหรืออังกฤษเท่านั้น',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
            'username.unique' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว',
            'username.alpha_dash' => 'ชื่อผู้ใช้ต้องเป็นตัวอักษร ตัวเลข - หรือ _ เท่านั้น',
            'phone.regex' => 'เบอร์โทรศัพท์ไม่ถูกต้อง (ต้องเป็นตัวเลข 9-10 หลัก)',
            'phone.unique' => 'เบอร์โทรศัพท์นี้ถูกใช้งานแล้ว',
            'zipcode.regex' => 'รหัสไปรษณีย์ต้องเป็นตัวเลข 5 หลัก',
            'terms.accepted' => 'กรุณายอมรับข้อตกลงและเงื่อนไข',
        ]);
    }

    /**
     * Create new user
     */
    private function createUser(array $validated): User
    {
        // Generate username if not provided
        if (empty($validated['username'])) {
            $validated['username'] = $this->generateUsername($validated['email']);
        }

        // Sanitize input
        $validated['firstname'] = $this->sanitizeName($validated['firstname']);
        $validated['lastname'] = $this->sanitizeName($validated['lastname']);

        return User::create([
            'username' => $validated['username'],
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'subdistrict' => $validated['subdistrict'] ?? null,
            'district' => $validated['district'] ?? null,
            'province' => $validated['province'] ?? null,
            'zipcode' => $validated['zipcode'] ?? null,
            'role' => 'customer', // default role
            'status' => 'active',
            'email_verified_at' => null, // Require email verification
        ]);
    }

    /**
     * Generate unique username from email
     */
    private function generateUsername(string $email): string
    {
        $base = explode('@', $email)[0];
        $base = preg_replace('/[^a-zA-Z0-9_]/', '', $base);
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Sanitize name input
     */
    private function sanitizeName(string $name): string
    {
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    /**
     * Send welcome email (optional)
     */
    private function sendWelcomeEmail(User $user): void
    {
        // Implement email sending logic
        // Mail::to($user->email)->send(new WelcomeEmail($user));
    }

    /**
     * Check if username is available (AJAX endpoint)
     */
    public function checkUsername(Request $request)
    {
        $username = $request->input('username');
        
        if (empty($username)) {
            return response()->json(['available' => false]);
        }

        $exists = User::where('username', $username)->exists();
        
        return response()->json(['available' => !$exists]);
    }

    /**
     * Check if email is available (AJAX endpoint)
     */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['available' => false]);
        }

        $exists = User::where('email', $email)->exists();
        
        return response()->json(['available' => !$exists]);
    }
}
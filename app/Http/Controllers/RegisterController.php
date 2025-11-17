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
    public function create(): View|RedirectResponse
    {
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
        // Debug: Log registration attempt
        Log::info('=== REGISTRATION ATTEMPT ===', [
            'email' => $request->email,
            'username' => $request->username,
            'has_terms' => $request->has('terms'),
            'terms_value' => $request->terms,
            'ip' => $request->ip(),
        ]);

        try {
            $validated = $this->validateRegistration($request);
            
            Log::info('✅ Validation passed', ['email' => $validated['email']]);

            DB::beginTransaction();

            $user = $this->createUser($validated);
            
            Log::info('✅ User created successfully', [
                'user_id' => $user->user_id ?? $user->id,
                'email' => $user->email,
            ]);

            DB::commit();
            
            Log::info('✅ Transaction committed');

            // Auto login
            Auth::login($user);
            
            Log::info('✅ User logged in');
            Log::info('=== REGISTRATION COMPLETED ===');

            return redirect()->route('home')
                ->with('success', 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จ!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ VALIDATION FAILED', [
                'errors' => $e->errors(),
            ]);
            throw $e;
            
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ REGISTRATION ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'email' => $request->email ?? 'unknown',
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Validate registration data
     */
    private function validateRegistration(Request $request): array
    {
        return $request->validate([
            'prefix' => ['required', 'string', 'in:นาย,นาง,นางสาว'],
            'firstname' => ['required', 'string', 'max:100', 'regex:/^[\p{Thai}a-zA-Z\s]+$/u'],
            'lastname' => ['required', 'string', 'max:100', 'regex:/^[\p{Thai}a-zA-Z\s]+$/u'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
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
            'username' => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'phone' => ['required', 'string', 'regex:/^0[0-9]{9}$/', 'unique:users,phone'],
            'address' => ['required', 'string', 'max:500'],
            'subdistrict' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'zipcode' => ['required', 'string', 'regex:/^[0-9]{5}$/'],
            'terms' => ['required', 'accepted'],
        ], [
            'prefix.required' => 'กรุณาเลือกคำนำหน้า',
            'prefix.in' => 'คำนำหน้าไม่ถูกต้อง',
            'firstname.required' => 'กรุณากรอกชื่อ',
            'firstname.regex' => 'ชื่อต้องเป็นภาษาไทยหรืออังกฤษเท่านั้น',
            'lastname.required' => 'กรุณากรอกนามสกุล',
            'lastname.regex' => 'นามสกุลต้องเป็นภาษาไทยหรืออังกฤษเท่านั้น',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'username.unique' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว',
            'username.alpha_dash' => 'ชื่อผู้ใช้ต้องเป็นตัวอักษร ตัวเลข - หรือ _ เท่านั้น',
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'phone.regex' => 'เบอร์โทรศัพท์ไม่ถูกต้อง (ต้องขึ้นต้นด้วย 0 และมี 10 หลัก)',
            'phone.unique' => 'เบอร์โทรศัพท์นี้ถูกใช้งานแล้ว',
            'address.required' => 'กรุณากรอกที่อยู่',
            'subdistrict.required' => 'กรุณาเลือกตำบล',
            'district.required' => 'กรุณาเลือกอำเภอ',
            'province.required' => 'กรุณาเลือกจังหวัด',
            'zipcode.required' => 'กรุณากรอกรหัสไปรษณีย์',
            'zipcode.regex' => 'รหัสไปรษณีย์ต้องเป็นตัวเลข 5 หลัก',
            'terms.required' => 'กรุณายอมรับข้อตกลงและเงื่อนไข',
            'terms.accepted' => 'กรุณายอมรับข้อตกลงและเงื่อนไข',
        ]);
    }

    /**
     * Create new user
     */
    private function createUser(array $validated): User
    {
        $validated['firstname'] = $this->sanitizeName($validated['firstname']);
        $validated['lastname'] = $this->sanitizeName($validated['lastname']);

        return User::create([
            'username' => $validated['username'],
            'prefix' => $validated['prefix'],
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'subdistrict' => $validated['subdistrict'],
            'district' => $validated['district'],
            'province' => $validated['province'],
            'zipcode' => $validated['zipcode'],
        ]);
    }

    /**
     * Sanitize name
     */
    private function sanitizeName(string $name): string
    {
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    /**
     * AJAX check username availability
     */
    public function checkUsername(Request $request)
    {
        $request->validate(['username' => 'required|string|max:50']);
        
        $username = $request->input('username');
        $available = !User::where('username', $username)->exists();
        
        return response()->json([
            'available' => $available,
            'message' => $available ? 'ชื่อผู้ใช้นี้สามารถใช้งานได้' : 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว'
        ]);
    }

    /**
     * AJAX check email availability
     */
    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $email = $request->input('email');
        $available = !User::where('email', strtolower($email))->exists();
        
        return response()->json([
            'available' => $available,
            'message' => $available ? 'อีเมลนี้สามารถใช้งานได้' : 'อีเมลนี้ถูกใช้งานแล้ว'
        ]);
    }
}
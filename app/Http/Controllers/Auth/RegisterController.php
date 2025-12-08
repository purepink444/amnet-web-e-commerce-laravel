<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    // แสดงฟอร์มสมัครสมาชิก
    public function create()
    {
        return view('auth.register'); // ตรงกับไฟล์ Blade ของคุณ
    }

    // ประมวลผลสมัครสมาชิก - ส่ง OTP
    public function store(Request $request)
    {
        \Log::info('Registration attempt', $request->all());

        // Validate ข้อมูล
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username|regex:/^[A-Za-z0-9_-]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
            'prefix' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|digits:10',
            'address' => 'required|string',
            'province' => 'required|string',
            'district' => 'required|string',
            'subdistrict' => 'required|string',
            'zipcode' => 'required|digits:5',
            'terms' => 'accepted',
        ]);

        \Log::info('Validation passed');

        // แปลงรหัสจังหวัดและอำเภอเป็นชื่อ
        $provinceName = $this->getProvinceName($request->province);
        $districtName = $this->getDistrictName($request->district);

        // สร้าง verification token
        $verificationToken = Str::random(64);

        // เก็บข้อมูลการสมัครชั่วคราวใน cache
        $registrationData = [
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role_id' => 2,
            'member_data' => [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'district' => $districtName,
                'subdistrict' => $request->subdistrict,
                'province' => $provinceName,
                'postal_code' => $request->zipcode,
            ],
            'verification_token' => $verificationToken,
            'expires_at' => now()->addHours(24), // 24 ชั่วโมง
        ];

        // เก็บใน cache ด้วย key ที่เป็น token
        Cache::put('verification_' . $verificationToken, $registrationData, now()->addHours(24));

        // สร้าง URL สำหรับยืนยัน
        $verificationUrl = route('email.verify', ['token' => $verificationToken]);

        // ส่งอีเมลยืนยัน
        try {
            Mail::to($request->email)->send(new EmailVerification($verificationUrl, $request->email));
            \Log::info('Verification email sent', ['email' => $request->email]);
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'ไม่สามารถส่งอีเมลยืนยันได้ กรุณาลองใหม่อีกครั้ง']);
        }

        return redirect()->route('register.pending')->with('success', 'กรุณาตรวจสอบอีเมลของคุณและคลิกลิงก์เพื่อยืนยันการสมัครสมาชิก');
    }

    // แสดงหน้ารอการยืนยันอีเมล
    public function showPendingVerification()
    {
        return view('auth.pending-verification');
    }

    // ยืนยันอีเมลและสมัครสมาชิก
    public function verifyEmail(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('register')->withErrors(['error' => 'ลิงก์ยืนยันไม่ถูกต้อง']);
        }

        // ดึงข้อมูลการสมัครจาก cache
        $registrationData = Cache::get('verification_' . $token);
        if (!$registrationData) {
            return redirect()->route('register')->withErrors(['error' => 'ลิงก์ยืนยันหมดอายุหรือไม่ถูกต้อง กรุณาสมัครสมาชิกใหม่']);
        }

        // ตรวจสอบเวลาหมดอายุ
        if (now()->greaterThan($registrationData['expires_at'])) {
            Cache::forget('verification_' . $token);
            return redirect()->route('register')->withErrors(['error' => 'ลิงก์ยืนยันหมดอายุ กรุณาสมัครสมาชิกใหม่']);
        }

        try {
            // สร้างผู้ใช้ใหม่
            $user = User::create([
                'username' => $registrationData['username'],
                'email' => $registrationData['email'],
                'password' => $registrationData['password'],
                'phone' => $registrationData['phone'],
                'role_id' => $registrationData['role_id'],
            ]);

            \Log::info('User created', ['user_id' => $user->user_id]);
        } catch (\Exception $e) {
            \Log::error('User creation failed', ['error' => $e->getMessage()]);
            return redirect()->route('register')->withErrors(['error' => 'ไม่สามารถสร้างบัญชีผู้ใช้ได้ กรุณาลองใหม่อีกครั้ง']);
        }

        try {
            // สร้าง Member profile
            $member = Member::create([
                'user_id' => $user->user_id,
                'first_name' => $registrationData['member_data']['first_name'],
                'last_name' => $registrationData['member_data']['last_name'],
                'address' => $registrationData['member_data']['address'],
                'district' => $registrationData['member_data']['district'],
                'subdistrict' => $registrationData['member_data']['subdistrict'],
                'province' => $registrationData['member_data']['province'],
                'postal_code' => $registrationData['member_data']['postal_code'],
            ]);

            \Log::info('Member created', ['member_id' => $member->member_id]);
        } catch (\Exception $e) {
            \Log::error('Member creation failed', ['error' => $e->getMessage()]);
            // ลบผู้ใช้ถ้าสร้าง member ไม่สำเร็จ
            $user->delete();
            return redirect()->route('register')->withErrors(['error' => 'ไม่สามารถสร้างโปรไฟล์สมาชิกได้ กรุณาลองใหม่อีกครั้ง']);
        }

        // ลบข้อมูลจาก cache
        Cache::forget('verification_' . $token);

        // Login อัตโนมัติ
        auth()->login($user);

        \Log::info('User logged in after email verification');

        return redirect()->route('home')->with('success', 'ยืนยันอีเมลสำเร็จ! สมัครสมาชิกเรียบร้อย ยินดีต้อนรับ!');
    }

    /**
     * แปลงรหัสจังหวัดเป็นชื่อจังหวัด
     */
    private function getProvinceName($provinceCode)
    {
        $provinces = json_decode(file_get_contents(public_path('json/src/provinces.json')), true);
        $province = collect($provinces)->firstWhere('provinceCode', $provinceCode);
        return $province ? $province['provinceNameTh'] : $provinceCode;
    }

    /**
     * แปลงรหัสอำเภอเป็นชื่ออำเภอ
     */
    private function getDistrictName($districtCode)
    {
        $districts = json_decode(file_get_contents(public_path('json/src/districts.json')), true);
        $district = collect($districts)->firstWhere('districtCode', $districtCode);
        return $district ? $district['districtNameTh'] : $districtCode;
    }

    /**
     * ตรวจสอบความพร้อมของชื่อผู้ใช้
     */
    public function checkUsername(Request $request)
    {
        $username = $request->input('username');
        $exists = User::where('username', $username)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว' : 'ชื่อผู้ใช้สามารถใช้งานได้'
        ]);
    }

    /**
     * ตรวจสอบความพร้อมของอีเมล
     */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $exists = User::where('email', $email)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'อีเมลนี้ถูกใช้งานแล้ว' : 'อีเมลสามารถใช้งานได้'
        ]);
    }
}

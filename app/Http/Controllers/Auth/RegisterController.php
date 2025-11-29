<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // แสดงฟอร์มสมัครสมาชิก
    public function showRegistrationForm()
    {
        return view('auth.register'); // ตรงกับไฟล์ Blade ของคุณ
    }

    // ประมวลผลสมัครสมาชิก
    public function register(Request $request)
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

        try {
            // สร้างผู้ใช้ใหม่
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role_id' => 2, // สมมติเป็น member
            ]);

            \Log::info('User created', ['user_id' => $user->user_id]);
        } catch (\Exception $e) {
            \Log::error('User creation failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to create user']);
        }

        try {
            // สร้าง Member profile
            $member = Member::create([
                'user_id' => $user->user_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'district' => $districtName,
                'subdistrict' => $request->subdistrict,
                'province' => $provinceName,
                'postal_code' => $request->zipcode,
            ]);

            \Log::info('Member created', ['member_id' => $member->member_id]);
        } catch (\Exception $e) {
            \Log::error('Member creation failed', ['error' => $e->getMessage()]);
            // Optionally delete the user if member fails
            $user->delete();
            return back()->withErrors(['error' => 'Failed to create member profile']);
        }

        // Login อัตโนมัติ
        auth()->login($user);

        \Log::info('User logged in');

        return redirect()->route('home')->with('success', 'สมัครสมาชิกสำเร็จ');
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
}

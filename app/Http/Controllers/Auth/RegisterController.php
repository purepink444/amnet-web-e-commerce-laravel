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
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).+$/'
            ],
            'prefix' => 'required|string',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'phone' => 'required|digits:10',
            'address' => 'required|string',
            'province' => 'required|string',
            'district' => 'required|string',
            'subdistrict' => 'required|string',
            'zipcode' => 'required|digits:5',
            'terms' => 'accepted',
        ]);

        \Log::info('Validation passed');

        // สร้างผู้ใช้ใหม่
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role_id' => 2, // สมมติเป็น member
        ]);

        \Log::info('User created', ['user_id' => $user->user_id]);

        // สร้าง Member profile
        $member = Member::create([
            'user_id' => $user->user_id,
            'first_name' => $request->firstname,
            'last_name' => $request->lastname,
            'address' => $request->address,
            'district' => $request->district,
            'province' => $request->province,
            'postal_code' => $request->zipcode,
        ]);

        \Log::info('Member created', ['member_id' => $member->member_id]);

        // Login อัตโนมัติ
        auth()->login($user);

        \Log::info('User logged in');

        return redirect()->route('home')->with('success', 'สมัครสมาชิกสำเร็จ');
    }
}

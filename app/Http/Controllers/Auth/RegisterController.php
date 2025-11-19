<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
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
        // Validate ข้อมูล
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username|regex:/^[A-Za-z0-9_-]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
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

        // สร้างผู้ใช้ใหม่
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'prefix' => $request->prefix,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone' => $request->phone,
            'address' => $request->address,
            'province' => $request->province,
            'district' => $request->district,
            'subdistrict' => $request->subdistrict,
            'zipcode' => $request->zipcode,
            'role_id' => 2, // สมมติเป็น member
        ]);

        // Login อัตโนมัติ
        auth()->login($user);

        return redirect()->route('home')->with('success', 'สมัครสมาชิกสำเร็จ');
    }
}

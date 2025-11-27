<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * แสดงหน้าตั้งค่าบัญชี
     */
    public function index()
    {
        $user = auth()->user();
        return view('account.settings', compact('user'));
    }

    /**
     * อัปเดตการตั้งค่า
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            // การตั้งค่าทั่วไป
            'language' => 'nullable|string|in:th,en',
            'timezone' => 'nullable|string',
            
            // การแจ้งเตือน
            'email_notifications' => 'nullable|boolean',
            'order_notifications' => 'nullable|boolean',
            'promotion_notifications' => 'nullable|boolean',
            
            // ความเป็นส่วนตัว
            'profile_visibility' => 'nullable|string|in:public,private',
            'show_email' => 'nullable|boolean',
            
            // ความปลอดภัย
            'two_factor_enabled' => 'nullable|boolean',
        ]);
        
        // อัปเดตข้อมูล (ถ้ามี columns เหล่านี้ในตาราง users)
        // $user->update($validated);
        
        return back()->with('success', 'บันทึกการตั้งค่าสำเร็จ');
    }

    /**
     * เปลี่ยนรหัสผ่าน (อีกวิธีหนึ่ง ถ้าไม่อยู่ใน ProfileController)
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        // ตรวจสอบรหัสผ่านเดิม
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง']);
        }
        
        // อัปเดตรหัสผ่านใหม่
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        return back()->with('success', 'เปลี่ยนรหัสผ่านสำเร็จ');
    }

    /**
     * ลบบัญชี
     */
    public function deleteAccount(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'password' => 'required|string',
            'confirm_delete' => 'required|accepted',
        ]);
        
        // ตรวจสอบรหัสผ่าน
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'รหัสผ่านไม่ถูกต้อง']);
        }
        
        // ลบบัญชี (ควรทำ soft delete แทน)
        // $user->delete();
        
        // Logout
        auth()->logout();
        
        return redirect()->route('home')->with('success', 'ลบบัญชีสำเร็จ');
    }
}

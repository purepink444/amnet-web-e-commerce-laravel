@extends('layouts.default')

@section('title', 'สมัครสมาชิก')

@section('content')
<div class="login-container">
    <div class="login-card">
        <img src="/mnt/data/2d1956aa-d7e5-4cdb-93ab-df858379fc06.png" class="login-logo" alt="Logo" />
        <form action="{{ route('register.store') }}" method="POST" class="login-form">
            @csrf

            <input type="text" name="username" value="{{ old('username') }}" class="login-input" placeholder="ชื่อผู้ใช้" required autofocus>

            <div class="name-row">
                <select name="prefix" class="login-input" required>
                    <option value="">คำนำหน้า</option>
                    <option value="นาย" {{ old('prefix') == 'นาย' ? 'selected' : '' }}>นาย</option>
                    <option value="นาง" {{ old('prefix') == 'นาง' ? 'selected' : '' }}>นาง</option>
                    <option value="นางสาว" {{ old('prefix') == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                </select>
                <input type="text" name="first_name" value="{{ old('first_name') }}" class="login-input" placeholder="ชื่อ" required>
                <input type="text" name="last_name" value="{{ old('last_name') }}" class="login-input" placeholder="นามสกุล" required>
            </div>

            <input type="email" name="email" value="{{ old('email') }}" class="login-input" placeholder="อีเมล" required>

            <input type="tel" name="phone" value="{{ old('phone') }}" class="login-input" placeholder="เบอร์โทรศัพท์" required>

            <input type="text" name="address" value="{{ old('address') }}" class="login-input" placeholder="ที่อยู่" required>

            <div class="location-row">
                <select name="province" id="province" class="login-input" required>
                    <option value="">เลือกจังหวัด</option>
                </select>
                <select name="district" id="district" class="login-input" disabled required>
                    <option value="">เลือกอำเภอ</option>
                </select>
                <select name="subdistrict" id="subdistrict" class="login-input" disabled required>
                    <option value="">เลือกตำบล</option>
                </select>
            </div>

            <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode') }}" class="login-input" placeholder="รหัสไปรษณีย์" required>

            <input type="password" name="password" class="login-input" placeholder="รหัสผ่าน" required>

            <input type="password" name="password_confirmation" class="login-input" placeholder="ยืนยันรหัสผ่าน" required>

            <div style="text-align: left; margin-bottom: 20px; font-size: 14px; color: var(--text-secondary);">
                <input class="form-check-input" type="checkbox" name="terms" id="terms" required style="margin-right: 8px;">
                <label class="form-check-label" for="terms" style="display: inline;">
                    ยอมรับ <a href="#" target="_blank" style="color: var(--orange-primary);">เงื่อนไขการใช้งาน</a>
                </label>
            </div>

            <button type="submit" class="login-btn">สมัครสมาชิก</button>
        </form>
    </div>
</div>

@endsection

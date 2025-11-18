@extends('layouts.admin')

@section('title', 'โปรไฟล์ของฉัน')

@section('content')
<div class="bg-gradient-profile py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Profile Card -->
                <div class="card shadow-lg border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="bi bi-person-circle me-2"></i>โปรไฟล์ของฉัน
                        </h3>
                        <p class="mb-0 mt-2">จัดการข้อมูลส่วนตัวและการตั้งค่าบัญชีของคุณ</p>
                    </div>

                    <div class="card-body p-4">
                        <!-- Success Message -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Profile Info Display -->
                        <div class="profile-header mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($user->username ?? $user->email, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <h4 class="mb-1">{{ $user->username ?? 'ผู้ใช้งาน' }}</h4>
                                    <p class="text-muted mb-1">{{ $user->email }}</p>
                                    <span class="badge bg-orange">{{ ucfirst($user->role) }}</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Profile Form -->
                        <form method="POST" action="{{ route('account.profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <!-- ข้อมูลบัญชี -->
                            <div class="mb-4">
                                <h5 class="section-title">
                                    <i class="bi bi-shield-lock text-orange me-2"></i>ข้อมูลบัญชี
                                </h5>
                                <hr>
                            </div>

                            <!-- Username & Email -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        ชื่อผู้ใช้ <span class="text-danger">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="username" 
                                        value="{{ old('username', $user->username) }}"
                                        class="form-control @error('username') is-invalid @enderror"
                                        required
                                    >
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        อีเมล <span class="text-danger">*</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        value="{{ old('email', $user->email) }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        required
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- ข้อมูลส่วนตัว -->
                            <div class="mb-4 mt-4">
                                <h5 class="section-title">
                                    <i class="bi bi-person text-orange me-2"></i>ข้อมูลส่วนตัว
                                </h5>
                                <hr>
                            </div>

                            <!-- First Name & Last Name -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชื่อจริง</label>
                                    <input 
                                        type="text" 
                                        name="first_name" 
                                        value="{{ old('first_name', $user->first_name ?? '') }}"
                                        class="form-control"
                                    >
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">นามสกุล</label>
                                    <input 
                                        type="text" 
                                        name="last_name" 
                                        value="{{ old('last_name', $user->last_name ?? '') }}"
                                        class="form-control"
                                    >
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label class="form-label">เบอร์โทรศัพท์</label>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    value="{{ old('phone', $user->phone ?? '') }}"
                                    class="form-control"
                                    placeholder="0812345678"
                                    pattern="[0-9]{10}"
                                    maxlength="10"
                                >
                                <small class="text-muted">กรอกเบอร์โทร 10 หลัก</small>
                            </div>

                            <!-- เปลี่ยนรหัสผ่าน -->
                            <div class="mb-4 mt-4">
                                <h5 class="section-title">
                                    <i class="bi bi-key text-orange me-2"></i>เปลี่ยนรหัสผ่าน
                                </h5>
                                <hr>
                            </div>

                            <p class="text-muted mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน
                            </p>

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label class="form-label">รหัสผ่านปัจจุบัน</label>
                                <input 
                                    type="password" 
                                    name="current_password" 
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    placeholder="กรอกรหัสผ่านปัจจุบัน"
                                >
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Password & Confirm -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">รหัสผ่านใหม่</label>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="อย่างน้อย 6 ตัวอักษร"
                                    >
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        class="form-control"
                                        placeholder="กรอกรหัสผ่านอีกครั้ง"
                                    >
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>บันทึกการเปลี่ยนแปลง
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>กลับ
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('account.orders') }}" class="quick-link-card">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="bi bi-box-seam quick-link-icon"></i>
                                    <h5 class="mt-3">คำสั่งซื้อของฉัน</h5>
                                    <p class="text-muted mb-0">ดูประวัติการสั่งซื้อ</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="{{ route('account.wishlist') }}" class="quick-link-card">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="bi bi-heart quick-link-icon"></i>
                                    <h5 class="mt-3">รายการโปรด</h5>
                                    <p class="text-muted mb-0">สินค้าที่บันทึกไว้</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="{{ route('account.settings') }}" class="quick-link-card">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="bi bi-gear quick-link-icon"></i>
                                    <h5 class="mt-3">ตั้งค่า</h5>
                                    <p class="text-muted mb-0">จัดการการตั้งค่า</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
:root {
    --orange-primary: #ff6b35;
    --orange-dark: #e85d2a;
    --orange-light: #ff8c5f;
    --black-primary: #1a1a1a;
    --black-secondary: #2d2d2d;
    --gray-text: #6c757d;
}

body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

.bg-gradient-profile {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #e85d2a 100%);
    position: relative;
    min-height: calc(100vh - 120px);
    margin: 0 !important;
    width: 100%;
}

.bg-gradient-profile::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff6b35' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.container {
    position: relative;
    z-index: 1;
}

.card {
    border-radius: 15px;
    overflow: hidden;
    background-color: #ffffff;
    position: relative;
    z-index: 1;
}

.card-header {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    border-bottom: 3px solid var(--black-primary);
}

.avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    margin: 0 auto;
    border: 4px solid #fff;
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
}

.badge.bg-orange {
    background-color: var(--orange-primary) !important;
    color: white;
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0;
    color: var(--black-primary);
}

.section-title i.text-orange {
    color: var(--orange-primary) !important;
}

.form-label {
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--black-secondary);
    margin-bottom: 0.5rem;
}

.form-control,
.form-select {
    font-size: 0.95rem;
    padding: 0.6rem 0.75rem;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    background-color: #ffffff;
    color: var(--black-primary);
}

.form-control:focus,
.form-select:focus {
    border-color: var(--orange-primary);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    background-color: #fff;
}

.btn-primary {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    border: none;
    padding: 0.75rem;
    font-weight: 600;
    color: #ffffff;
    border-radius: 8px;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-primary) 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
}

.btn-outline-secondary {
    border: 2px solid var(--black-secondary);
    color: var(--black-primary);
    border-radius: 8px;
    font-weight: 500;
}

.btn-outline-secondary:hover {
    background-color: var(--black-primary);
    border-color: var(--black-primary);
    color: #ffffff;
}

hr {
    border-color: var(--orange-primary);
    opacity: 0.3;
    border-width: 2px;
}

.text-danger {
    font-size: 0.85rem;
    color: var(--orange-dark) !important;
}

small.text-muted {
    font-size: 0.85rem;
    color: var(--gray-text);
}

.invalid-feedback {
    color: var(--orange-dark);
}

.is-invalid {
    border-color: var(--orange-dark) !important;
}

.shadow-lg {
    box-shadow: 0 25px 50px -12px rgba(255, 107, 53, 0.3) !important;
}

.quick-link-card {
    text-decoration: none;
    color: inherit;
    display: block;
    transition: transform 0.3s ease;
}

.quick-link-card:hover {
    transform: translateY(-5px);
}

.quick-link-card .card {
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

.quick-link-card:hover .card {
    border-color: var(--orange-primary);
    box-shadow: 0 10px 25px rgba(255, 107, 53, 0.2);
}

.quick-link-icon {
    font-size: 3rem;
    color: var(--orange-primary);
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
    border-radius: 8px;
}

.profile-header h4 {
    color: var(--black-primary);
    font-weight: 600;
}
</style>
@endsection
@extends('layouts.default')

@section('title', 'โปรไฟล์ของฉัน')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <!-- Header -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
                <div class="flex-grow-1">
                    <h2 class="mb-1">
                        <i class="bi bi-person-circle text-primary me-2"></i>
                        โปรไฟล์ของฉัน
                    </h2>
                    <p class="text-muted mb-0 small">จัดการข้อมูลส่วนตัวและการตั้งค่าบัญชีของคุณ</p>
                </div>
                <a href="{{ route('account.orders.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-bag-check me-1"></i>
                    <span class="d-none d-sm-inline">ดูคำสั่งซื้อ</span>
                    <span class="d-inline d-sm-none">🛒</span>
                </a>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- Profile Information -->
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="profile-avatar mb-3">
                                <div class="avatar-circle mx-auto">
                                    <i class="bi bi-person-fill display-4 text-primary"></i>
                                </div>
                            </div>

                            <h4 class="card-title mb-1">{{ $user->prefix }} {{ $user->firstname }} {{ $user->lastname }}</h4>
                            <p class="text-muted mb-3">@{{ $user->username }}</p>

                            <div class="profile-info">
                                <div class="info-item mb-2">
                                    <i class="bi bi-envelope text-primary me-2"></i>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                                <div class="info-item mb-2">
                                    <i class="bi bi-telephone text-success me-2"></i>
                                    <small class="text-muted">{{ $user->phone }}</small>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar text-info me-2"></i>
                                    <small class="text-muted">สมาชิกตั้งแต่ {{ $user->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-pencil-square text-primary me-2"></i>
                                แก้ไขข้อมูลบัญชี
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('account.profile.update') }}" method="POST" novalidate>
                                @csrf
                                @method('PATCH')

                                <!-- Email -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-envelope me-2 text-primary"></i>
                                        อีเมล <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           name="email"
                                           value="{{ old('email', $user->email) }}"
                                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                                           placeholder="your@email.com"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">ใช้สำหรับการเข้าสู่ระบบและรับการแจ้งเตือน</small>
                                </div>

                                <!-- Password Section -->
                                <div class="password-section">
                                    <h6 class="fw-semibold text-dark mb-3">
                                        <i class="bi bi-key text-warning me-2"></i>
                                        เปลี่ยนรหัสผ่าน
                                    </h6>
                                    <p class="text-muted small mb-3">เว้นว่างหากไม่ต้องการเปลี่ยนรหัสผ่าน</p>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">รหัสผ่านใหม่</label>
                                            <input type="password"
                                                   name="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   placeholder="อย่างน้อย 8 ตัวอักษร"
                                                   minlength="8">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">ยืนยันรหัสผ่านใหม่</label>
                                            <input type="password"
                                                   name="password_confirmation"
                                                   class="form-control"
                                                   placeholder="กรอกรหัสผ่านอีกครั้ง">
                                        </div>
                                    </div>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="bi bi-info-circle me-1"></i>
                                        รหัสผ่านต้องมีตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก ตัวเลข และอักขระพิเศษ
                                    </small>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-3 mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary btn-lg px-4">
                                        <i class="bi bi-save me-2"></i>บันทึกการเปลี่ยนแปลง
                                    </button>
                                    <a href="{{ route('account.profile') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>ยกเลิก
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Profile Page Styles */
.form-label {
    font-weight: 600 !important;
    color: #2d3748 !important;
    margin-bottom: 0.5rem;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa !important;
}

.avatar-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.profile-info {
    text-align: left;
    max-width: 250px;
    margin: 0 auto;
}

.info-item {
    display: flex;
    align-items: center;
    justify-content: center;
}

.info-item small {
    font-size: 0.85rem;
}

.password-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.form-control {
    border-radius: 8px;
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #ff6b35;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #ff6b35 0%, #e85d2a 100%);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #e85d2a 0%, #ff6b35 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.btn-outline-secondary {
    border-radius: 8px;
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
    border: none;
    border-radius: 8px;
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }

    .card-body {
        padding: 1.5rem !important;
    }

    .btn-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }

    .d-flex.gap-3 {
        flex-direction: column;
    }

    .d-flex.gap-3 .btn {
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection

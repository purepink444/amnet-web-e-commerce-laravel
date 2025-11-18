@extends('layouts.admin')

@section('title', 'โปรไฟล์')
@section('page-title', 'โปรไฟล์ของฉัน')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">โปรไฟล์</li>
@endsection

@section('content')
<div class="row">
    <!-- Left Column: Profile Info -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm profile-card">
            <div class="card-body text-center py-5">
                <!-- Avatar -->
                <div class="profile-avatar mb-3">
                    <i class="fas fa-user-shield"></i>
                </div>
                
                <!-- User Info -->
                <h4 class="mb-1">{{ $user->username }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                
                <!-- Role Badge -->
                <span class="badge badge-admin mb-3">
                    <i class="fas fa-shield-alt me-1"></i>
                    {{ $user->role?->role_name ?? 'Administrator' }}
                </span>
                
                <hr class="my-3">
                
                <!-- Stats -->
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <div class="stat-value">{{ $user->created_at->diffForHumans() }}</div>
                        <div class="stat-label">สมาชิกเมื่อ</div>
                    </div>
                    <div class="col-6">
                        <div class="stat-value">{{ $user->user_id }}</div>
                        <div class="stat-label">User ID</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-link me-2 text-orange"></i>เมนูด่วน
                </h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-cart me-2 text-primary"></i>คำสั่งซื้อของฉัน
                </a>
                <a href="{{ route('account.wishlist') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-heart me-2 text-danger"></i>รายการโปรด
                </a>
                <a href="{{ route('account.settings') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-cog me-2 text-secondary"></i>ตั้งค่าบัญชี
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Edit Forms -->
    <div class="col-lg-8">
        <!-- Personal Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-edit me-2 text-orange"></i>ข้อมูลส่วนตัว
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('account.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label fw-semibold">
                                <i class="fas fa-user me-1 text-muted"></i>Username
                            </label>
                            <input 
                                type="text" 
                                name="username" 
                                id="username"
                                value="{{ old('username', $user->username) }}" 
                                class="form-control @error('username') is-invalid @enderror"
                                placeholder="ชื่อผู้ใช้">
                            @error('username') 
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div> 
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1 text-muted"></i>อีเมล
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email"
                                value="{{ old('email', $user->email) }}" 
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="example@email.com">
                            @error('email') 
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div> 
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label fw-semibold">
                                <i class="fas fa-id-card me-1 text-muted"></i>ชื่อจริง
                            </label>
                            <input 
                                type="text" 
                                name="first_name" 
                                id="first_name"
                                value="{{ old('first_name', $user->first_name) }}" 
                                class="form-control @error('first_name') is-invalid @enderror"
                                placeholder="ชื่อ">
                            @error('first_name') 
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div> 
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label fw-semibold">
                                <i class="fas fa-id-card me-1 text-muted"></i>นามสกุล
                            </label>
                            <input 
                                type="text" 
                                name="last_name" 
                                id="last_name"
                                value="{{ old('last_name', $user->last_name) }}" 
                                class="form-control @error('last_name') is-invalid @enderror"
                                placeholder="นามสกุล">
                            @error('last_name') 
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div> 
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">
                            <i class="fas fa-phone me-1 text-muted"></i>เบอร์โทรศัพท์
                        </label>
                        <input 
                            type="tel" 
                            name="phone" 
                            id="phone"
                            value="{{ old('phone', $user->phone) }}" 
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="0xx-xxx-xxxx">
                        @error('phone') 
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div> 
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lock me-2 text-orange"></i>เปลี่ยนรหัสผ่าน
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('account.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="fas fa-info-circle me-2 fs-5"></i>
                        <small>หากไม่ต้องการเปลี่ยนรหัสผ่าน ให้เว้นว่างไว้</small>
                    </div>

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">
                            <i class="fas fa-key me-1 text-muted"></i>รหัสผ่านปัจจุบัน
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="current_password" 
                                id="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                placeholder="••••••••">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                            @error('current_password') 
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div> 
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            <i class="fas fa-key me-1 text-muted"></i>รหัสผ่านใหม่
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="••••••••">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                            @error('password') 
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div> 
                            @enderror
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            <i class="fas fa-check-circle me-1 text-muted"></i>ยืนยันรหัสผ่านใหม่
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation"
                                class="form-control"
                                placeholder="••••••••">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="fas fa-shield-alt me-2"></i>เปลี่ยนรหัสผ่าน
                        </button>
                    </div>
                </form>
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
}

.text-orange {
    color: var(--orange-primary) !important;
}

.profile-card {
    border-radius: 15px;
    overflow: hidden;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, var(--orange-primary), var(--orange-dark));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 3rem;
    color: white;
    box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
}

.badge-admin {
    background: linear-gradient(135deg, var(--orange-primary), var(--orange-dark));
    color: white;
    padding: 0.5rem 1.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    border-radius: 20px;
}

.stat-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--orange-primary);
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.list-group-item {
    border: none;
    padding: 1rem 1.25rem;
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background-color: #fff8f5;
    padding-left: 1.5rem;
}

.form-label {
    margin-bottom: 0.5rem;
    color: #2d3748;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--orange-primary);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, var(--orange-primary), var(--orange-dark));
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107, #ff9800);
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    color: white;
    transition: all 0.3s ease;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
    color: white;
}

.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
}

.card-header {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-bottom: 2px solid var(--orange-primary);
    border-radius: 15px 15px 0 0 !important;
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d9ff;
    color: #004085;
    border-radius: 10px;
}

.input-group .toggle-password {
    border: 1px solid #e2e8f0;
    border-left: none;
}

.invalid-feedback {
    font-size: 0.875rem;
}

@media (max-width: 991px) {
    .profile-avatar {
        width: 100px;
        height: 100px;
        font-size: 2.5rem;
    }
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('.toggle-password').on('click', function() {
        const input = $(this).closest('.input-group').find('input');
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>
@endsection
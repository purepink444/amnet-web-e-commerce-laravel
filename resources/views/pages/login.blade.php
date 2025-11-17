@extends('layouts.default')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div class="login-page-wrapper">
    <div class="bg-gradient-login">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <!-- Login Card -->
                    <div class="card shadow-2xl border-0 rounded-4 overflow-hidden">
                        <!-- Header with Gradient -->
                        <div class="card-header text-white text-center py-5 position-relative login-header">
                            <div class="login-icon-wrapper mb-3">
                                <i class="bi bi-shield-lock-fill display-4"></i>
                            </div>
                            <h3 class="fw-bold mb-2">ยินดีต้อนรับกลับ</h3>
                            <p class="mb-0 opacity-90">เข้าสู่ระบบเพื่อดำเนินการต่อ</p>
                        </div>

                        <div class="card-body p-5">
                            <form action="{{ route('login') }}" method="POST">
                                @csrf

                                <!-- Username -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-muted small mb-2">
                                        <i class="bi bi-person-circle me-2"></i>ชื่อผู้ใช้
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-person text-orange"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control border-start-0 ps-0" 
                                               name="username" 
                                               placeholder="กรอกชื่อผู้ใช้"
                                               required
                                               autofocus>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-muted small mb-2">
                                        <i class="bi bi-key me-2"></i>รหัสผ่าน
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock text-orange"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control border-start-0 ps-0" 
                                               name="password" 
                                               id="password"
                                               placeholder="กรอกรหัสผ่าน"
                                               required>
                                        <button class="btn btn-light border-start-0" 
                                                type="button" 
                                                onclick="togglePassword()">
                                            <i class="bi bi-eye text-orange" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Remember & Forgot -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember">
                                        <label class="form-check-label small" for="remember">
                                            จดจำฉันไว้
                                        </label>
                                    </div>
                                    <a href="#" class="text-decoration-none small text-orange-link">
                                        ลืมรหัสผ่าน?
                                    </a>
                                </div>

                                <!-- Login Button -->
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 py-3 fw-semibold">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>เข้าสู่ระบบ
                                </button>

                                <!-- Divider -->
                                <div class="divider my-4">
                                    <span class="divider-text">หรือ</span>
                                </div>

                                <!-- Register Link -->
                                <div class="text-center">
                                    <p class="text-muted mb-2">ยังไม่มีบัญชี?</p>
                                    <a href="{{ route('register') }}" class="btn btn-outline-orange w-100 py-2">
                                        <i class="bi bi-person-plus me-2"></i>สมัครสมาชิก
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Additional Links -->
                    <div class="text-center mt-4 mb-5">
                        <a href="/" class="text-decoration-none text-white">
                            <i class="bi bi-house me-2"></i>กลับหน้าหลัก
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

.login-page-wrapper {
    min-height: calc(100vh - 60px); /* ลบความสูงของ navbar */
    display: flex;
    flex-direction: column;
}

.bg-gradient-login {
    background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 50%, var(--orange-dark) 100%);
    position: relative;
    padding: 120px 0 80px 0; /* เพิ่ม padding บนและล่าง */
    flex: 1;
    display: flex;
    align-items: center;
}

.bg-gradient-login::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff6b35' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
    z-index: 0;
}

.bg-gradient-login .container {
    position: relative;
    z-index: 1;
}

.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(255, 107, 53, 0.3);
}

.login-header {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    position: relative;
    overflow: hidden;
    border-bottom: 4px solid var(--black-primary);
}

.login-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(26, 26, 26, 0.2) 0%, transparent 70%);
    animation: pulse 3s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.3; }
    50% { transform: scale(1.1); opacity: 0.5; }
}

.login-icon-wrapper {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.input-group-text {
    border-radius: 12px 0 0 12px;
    background-color: #f8f9fa;
    border: 2px solid #dee2e6;
}

.text-orange {
    color: var(--orange-primary) !important;
}

.form-control {
    border-radius: 0 12px 12px 0;
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
    color: var(--black-primary);
}

.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 107, 53, 0.25);
    border-color: var(--orange-primary);
}

.btn-light {
    border: 2px solid #dee2e6;
    border-radius: 0 12px 12px 0;
}

.btn-light:hover {
    background-color: #fff3f0;
}

.form-check-input:checked {
    background-color: var(--orange-primary);
    border-color: var(--orange-primary);
}

.text-orange-link {
    color: var(--orange-primary);
    font-weight: 500;
}

.text-orange-link:hover {
    color: var(--orange-dark);
    text-decoration: underline !important;
}

.btn-primary {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-primary) 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(255, 107, 53, 0.4);
}

.btn-outline-orange {
    border-radius: 12px;
    border: 2px solid var(--orange-primary);
    color: var(--orange-primary);
    font-weight: 600;
    background-color: transparent;
    transition: all 0.3s ease;
}

.btn-outline-orange:hover {
    background-color: var(--orange-primary);
    border-color: var(--orange-primary);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
}

.divider {
    position: relative;
    text-align: center;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--orange-primary), transparent);
    opacity: 0.3;
}

.divider-text {
    position: relative;
    background: white;
    padding: 0 1rem;
    color: var(--gray-text);
    font-size: 0.875rem;
    font-weight: 500;
}

.text-muted {
    color: var(--gray-text) !important;
}

.card-body {
    background-color: #ffffff;
}

/* Responsive */
@media (max-width: 768px) {
    .bg-gradient-login {
        padding: 100px 0 60px 0;
    }
    
    .card-body {
        padding: 2rem !important;
    }
}
</style>
@endsection

@section('scripts')
<script>
function togglePassword() {
    const password = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endsection
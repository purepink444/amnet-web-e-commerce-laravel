@extends('layouts.default')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6 hero-content">
                <h1 class="display-3 fw-bold text-white mb-4 animate-fade-in">
                    ยินดีต้อนรับสู่
                    <span class="text-orange"></span>
                </h1>
                <p class="lead text-white mb-4 animate-fade-in-delay">
                    เราพร้อมให้บริการที่ดีที่สุดเพื่อตอบสนองทุกความต้องการของคุณ
                </p>
                <div class="d-flex gap-3 animate-fade-in-delay-2">
                    <a href="/product" class="btn btn-orange btn-lg px-4">
                        <i class="bi bi-bag me-2"></i>เลือกซื้อสินค้า
                    </a>
                    <a href="/aboutus" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-info-circle me-2"></i>เกี่ยวกับเรา
                    </a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-image-wrapper">
                    <i class="bi bi-box-seam display-1 text-orange"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="features-section py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">บริการของเรา</h2>
            <p class="lead text-muted">เรามีบริการที่หลากหลายเพื่อตอบสนองความต้องการของคุณ</p>
        </div>
        
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-3">บริการรวดเร็ว</h4>
                    <p class="text-muted">
                        ส่งสินค้าภายใน 24 ชั่วโมง พร้อมระบบติดตามพัสดุแบบเรียลไทม์
                    </p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="fw-bold mb-3">ความปลอดภัย</h4>
                    <p class="text-muted">
                        ระบบรักษาความปลอดภัยระดับสูง ปกป้องข้อมูลส่วนตัวของคุณ
                    </p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h4 class="fw-bold mb-3">ซัพพอร์ต 24/7</h4>
                    <p class="text-muted">
                        ทีมงานพร้อมให้บริการและช่วยเหลือคุณตลอด 24 ชั่วโมง
                    </p>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-award"></i>
                    </div>
                    <h4 class="fw-bold mb-3">คุณภาพระดับ</h4>
                    <p class="text-muted">
                        สินค้าคุณภาพสูง ผ่านการคัดสรรจากผู้เชี่ยวชาญ
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="stats-section">
    <div class="container py-5">
        <div class="row text-center g-4">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="bi bi-people-fill mb-3"></i>
                    <h2 class="fw-bold mb-2">10,000+</h2>
                    <p class="text-white-50">ลูกค้าทั่วประเทศ</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="bi bi-box-seam mb-3"></i>
                    <h2 class="fw-bold mb-2">50,000+</h2>
                    <p class="text-white-50">สินค้าที่จัดส่ง</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="bi bi-star-fill mb-3"></i>
                    <h2 class="fw-bold mb-2">4.9/5</h2>
                    <p class="text-white-50">คะแนนความพึงพอใจ</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="bi bi-trophy-fill mb-3"></i>
                    <h2 class="fw-bold mb-2">15+</h2>
                    <p class="text-white-50">รางวัลที่ได้รับ</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="cta-section py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-4 fw-bold mb-4">พร้อมเริ่มต้นแล้วหรือยัง?</h2>
                <p class="lead mb-4">
                    สมัครสมาชิกวันนี้ รับส่วนลดพิเศษ 20% สำหรับการสั่งซื้อครั้งแรก
                </p>
                <a href="/register" class="btn btn-orange btn-lg px-5">
                    <i class="bi bi-person-plus me-2"></i>สมัครสมาชิกเลย
                </a>
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
    --black-primary: #1a1a1a;
    --black-secondary: #2d2d2d;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 50%, var(--orange-dark) 100%);
    position: relative;
    overflow: hidden;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff6b35' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.min-vh-75 {
    min-height: 75vh;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.text-orange {
    color: var(--orange-primary);
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-image-wrapper {
    text-align: center;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.animate-fade-in {
    animation: fadeIn 1s ease-in;
}

.animate-fade-in-delay {
    animation: fadeIn 1s ease-in 0.3s both;
}

.animate-fade-in-delay-2 {
    animation: fadeIn 1s ease-in 0.6s both;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Buttons */
.btn-orange {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    border: none;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-orange:hover {
    background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-primary) 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(255, 107, 53, 0.4);
    color: white;
}

.btn-outline-light:hover {
    background-color: white;
    color: var(--orange-primary);
}

/* Features Section */
.features-section {
    background-color: #f8f9fa;
}

.feature-card {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(255, 107, 53, 0.2);
}

.feature-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

/* Stats Section */
.stats-section {
    background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%);
    color: white;
}

.stat-item i {
    font-size: 3rem;
    color: var(--orange-primary);
}

.stat-item h2 {
    color: white;
    font-size: 2.5rem;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(to bottom, #f8f9fa 0%, white 100%);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 2rem;
    }
    
    .feature-card {
        margin-bottom: 1rem;
    }
    
    .stat-item h2 {
        font-size: 2rem;
    }
}
</style>
@endsection
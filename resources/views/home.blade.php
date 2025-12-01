@extends('layouts.default')

@section('title', 'Amnet Web')

@section('content')
<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-container">
        <div class="badge">สวัสดีตอนบ่าย</div>

        <h1>ยินดีต้อนรับ Amnet Web</h1>
        <p>
            เราพร้อมให้บริการในด้านระบบเครือข่าย ระบบกล้องวงจรปิด ระบบอินเทอร์เน็ตภายในองค์กร รวมถึงงานติดตั้งต่าง ๆ เพื่อให้คุณได้ใช้สินค้าคุณภาพสูง พร้อมการรับประกันที่เชื่อถือได้
        </p>

        <div class="btn-group">
            <button class="btn-primary">ดูสินค้า</button>
            <button class="btn-secondary">ติดต่อเรา</button>
        </div>
    </div>
</section>

<!-- ================================
     Brand Logos Section
=============================== -->
<div class="brand-section py-5">
    <div class="container">

        <div class="text-center mb-4">
            <h2 class="fw-bold">แบรนด์ที่เราให้บริการ</h2>
            <p class="text-muted">เราคัดสรรสินค้าคุณภาพจากแบรนด์ชั้นนำ</p>
        </div>

        <div class="brand-logos">
            <img src="{{ asset('images/brands/dahua.png') }}" alt="Dahua">
            <img src="{{ asset('images/brands/hikvision.png') }}" alt="Hikvision">
            <img src="{{ asset('images/brands/ruijie.png') }}" alt="Ruijie">
            <img src="{{ asset('images/brands/mikrotik.png') }}" alt="Mikrotik">
            <img src="{{ asset('images/brands/reyee.png') }}" alt="Reyee">
            <img src="{{ asset('images/brands/h3c.png') }}" alt="H3C">
            <img src="{{ asset('images/brands/megvii.png') }}" alt="MEGVII">
            <img src="{{ asset('images/brands/bdcom.png') }}" alt="BDCOM">
            <img src="{{ asset('images/brands/uniview.png') }}" alt="Uniview">
            <img src="{{ asset('images/brands/samcom.png') }}" alt="Samcom">
        </div>

    </div>
</div>


@endsection

@section('styles')
<style>
body {
    margin: 0;
    font-family: "Prompt", sans-serif;
    background: #fff;
}

/* ======= HERO SECTION ======= */
.hero {
    background: linear-gradient(#ec6f24, #2e2e2e);
    color: #fff;
    padding: 100px 20px;
    text-align: left;
    position: relative;
}

.hero-container {
    max-width: 750px;
    margin: 0 auto;
}

/* Responsive adjustments */
@media (min-width: 768px) {
    .hero-container {
        margin-left: 80px;
    }
}

@media (max-width: 767px) {
    .hero {
        padding: 60px 20px;
        text-align: center;
    }

    .hero-container {
        margin: 0 auto;
    }

    .hero h1 {
        font-size: 32px;
    }

    .hero p {
        font-size: 16px;
    }

    .hero .btn-group {
        justify-content: center;
    }

    .hero .btn-group button {
        margin: 5px;
    }
}

.hero .badge {
    display: inline-block;
    background: #721c24;
    color: #fff;
    padding: 5px 15px;
    border-radius: 20px;
    margin-bottom: 10px;
    font-size: 14px;
}

.hero h1 {
    font-size: 40px;
    margin-bottom: 10px;
    color: #fff;
}

.hero p {
    font-size: 18px;
    margin-bottom: 25px;
    line-height: 1.7;
}

.hero .btn-group button {
    padding: 12px 22px;
    border: none;
    border-radius: 5px;
    margin-right: 10px;
    cursor: pointer;
    font-size: 16px;
}

.btn-primary {
    background: #ff8c42;
    color: #fff;
}

.btn-secondary {
    background: #fff;
    color: #333;
}

/* ======= LOGO SECTION ======= */
.brand-section {
    text-align: center;
}

.brand-logos {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 40px;
}

.brand-logos img {
    height: 60px;
    object-fit: contain;
}


</style>
@endsection
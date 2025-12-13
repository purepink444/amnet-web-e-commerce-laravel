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

<!-- ================================
      Services Section
================================ -->
<section class="services-section py-5">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="fw-bold">บริการของเรา</h2>
            <p class="text-muted">ให้บริการครบครันในด้านระบบเครือข่ายและความปลอดภัย</p>
        </div>

        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    🌐
                </div>
                <h3>ระบบเครือข่าย</h3>
                <p>ติดตั้งและบำรุงรักษาระบบเครือข่ายสำหรับองค์กรทุกขนาด</p>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    📹
                </div>
                <h3>กล้องวงจรปิด</h3>
                <p>ติดตั้งระบบกล้องวงจรปิดคุณภาพสูง พร้อมการดูแลหลังการขาย</p>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    📶
                </div>
                <h3>อินเทอร์เน็ตไร้สาย</h3>
                <p>ติดตั้งและปรับแต่งระบบ WiFi ให้ครอบคลุมทุกพื้นที่</p>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    🛡️
                </div>
                <h3>ความปลอดภัย</h3>
                <p>ให้คำปรึกษาและติดตั้งระบบรักษาความปลอดภัยครบวงจร</p>
            </div>
        </div>

    </div>
</section>

@endsection

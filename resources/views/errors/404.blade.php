@extends('layouts.default')

@section('title', '404 - ไม่พบหน้าที่ต้องการ')

@section('content')
<div class="error-page">
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-search"></i>
        </div>
        <h1 class="error-code">404</h1>
        <h2 class="error-message">ไม่พบหน้าที่คุณต้องการ</h2>
        <p class="error-description">
            ขอโทษด้วย! หน้าที่คุณกำลังมองหาอาจถูกย้าย ลบ หรือไม่เคยมีอยู่จริง
            กรุณาตรวจสอบ URL อีกครั้ง หรือกลับไปยังหน้าแรก
        </p>

        <div class="action-buttons">
            <a href="{{ url('/') }}" class="btn-home">
                <i class="bi bi-house-door me-2"></i>
                กลับหน้าแรก
            </a>
            <a href="javascript:history.back()" class="btn-back">
                <i class="bi bi-arrow-left me-2"></i>
                ย้อนกลับ
            </a>
        </div>

        <div class="help-links">
            <h6>หรือคุณอาจต้องการ:</h6>
            <a href="{{ url('/product') }}"><i class="bi bi-bag"></i> ดูสินค้า</a>
            <a href="{{ url('/news') }}"><i class="bi bi-newspaper"></i> อ่านข่าว</a>
            <a href="{{ url('/contact') }}"><i class="bi bi-envelope"></i> ติดต่อเรา</a>
        </div>
    </div>
</div>
@endsection

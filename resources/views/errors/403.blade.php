@extends('layouts.default')

@section('title', '403 - ไม่มีสิทธิ์เข้าถึง')

@section('content')
<div class="error-page">
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h1 class="error-code">403</h1>
        <h2 class="error-message">ไม่มีสิทธิ์เข้าถึง</h2>
        <p class="error-description">
            ขอโทษด้วย! คุณไม่มีสิทธิ์เข้าถึงหน้านี้ กรุณาเข้าสู่ระบบด้วยบัญชีที่มีสิทธิ์
            หรือติดต่อผู้ดูแลระบบหากคุณคิดว่านี่เป็นข้อผิดพลาด
        </p>

        <div class="action-buttons">
            <a href="{{ url('/') }}" class="btn-home">
                <i class="bi bi-house-door me-2"></i>
                กลับหน้าแรก
            </a>
            <a href="{{ route('login') }}" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                เข้าสู่ระบบ
            </a>
        </div>

        <div class="help-text">
            <p class="mb-0">
                <strong>ต้องการความช่วยเหลือ?</strong><br>
                ติดต่อทีมสนับสนุนของเราเพื่อขอสิทธิ์เข้าถึง
            </p>
        </div>
    </div>
</div>
@endsection
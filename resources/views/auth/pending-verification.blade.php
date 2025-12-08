@extends('layouts.default')

@section('title', 'รอการยืนยันอีเมล')

@section('content')
<div class="login-container">
    <div class="login-card">
        <img src="/mnt/data/2d1956aa-d7e5-4cdb-93ab-df858379fc06.png" class="login-logo" alt="Logo" />

        <div style="text-align: center;">
            <div style="font-size: 48px; color: #28a745; margin-bottom: 20px;">
                📧
            </div>

            <h3 style="color: var(--text-primary); margin-bottom: 15px;">ตรวจสอบอีเมลของคุณ</h3>

            @if(session('success'))
                <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-size: 14px;">
                    {{ session('success') }}
                </div>
            @endif

            <p style="color: var(--text-secondary); font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
                เราได้ส่งลิงก์ยืนยันไปยังอีเมลของคุณแล้ว<br>
                กรุณาคลิกลิงก์ในอีเมลเพื่อเสร็จสิ้นการสมัครสมาชิก
            </p>

            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">
                    <strong>เคล็ดลับ:</strong> หากไม่พบอีเมลในกล่องจดหมายหลัก<br>
                    กรุณาตรวจสอบในโฟลเดอร์สแปม (Spam/Junk)
                </p>
            </div>

            <div style="margin-top: 30px;">
                <a href="{{ route('register') }}" style="color: var(--orange-primary); text-decoration: none; font-size: 14px; margin-right: 20px;">
                    ← กลับไปสมัครสมาชิกใหม่
                </a>

                <a href="{{ route('login') }}" style="color: var(--orange-primary); text-decoration: none; font-size: 14px;">
                    เข้าสู่ระบบ →
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh page every 30 seconds to check for new session messages
setTimeout(function() {
    if (!sessionStorage.getItem('verification_checked')) {
        location.reload();
    }
}, 30000);

// Mark as checked when user interacts
document.addEventListener('click', function() {
    sessionStorage.setItem('verification_checked', 'true');
});
</script>
@endsection
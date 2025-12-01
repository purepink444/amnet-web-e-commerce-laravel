@extends('layouts.default')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div style="font-family: 'Kanit', sans-serif; background: #f3f3f3; padding: 20px; display: flex; justify-content: center; min-height: 100vh; align-items: center;">
    <div style="width: 100%; max-width: 400px; background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 14px rgba(0,0,0,0.1); text-align: center;">
        <img src="/mnt/data/2d1956aa-d7e5-4cdb-93ab-df858379fc06.png" style="width:100%; border-radius: 12px; margin-bottom:16px;" />
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="text" name="username" value="{{ old('username') }}" style="width: 100%; background: #d9d9d9; border-radius: 12px; padding: 16px; margin-bottom: 20px; font-size: 18px; text-align: left; border: none;" placeholder="ชื่อผู้ใช้" required autofocus>
            <input type="password" name="password" style="width: 100%; background: #d9d9d9; border-radius: 12px; padding: 16px; margin-bottom: 20px; font-size: 18px; text-align: left; border: none;" placeholder="รหัสผ่าน" required>
            <button type="submit" style="width: 100%; padding: 16px; font-size: 20px; border-radius: 16px; background: #ff8c39; border: none; cursor: pointer; color: white; font-weight: bold;">เข้าสู่ระบบ</button>
        </form>
    </div>
</div>
</div>

<style>
/* Responsive styles for login page */
@media (max-width: 576px) {
    div[style*="padding: 20px"] {
        padding: 10px !important;
    }

    div[style*="padding: 24px"] {
        padding: 20px !important;
        max-width: 100% !important;
        margin: 10px !important;
    }

    input[style*="padding: 16px"] {
        padding: 12px !important;
        font-size: 16px !important;
        margin-bottom: 15px !important;
    }

    button[style*="padding: 16px"] {
        padding: 14px !important;
        font-size: 18px !important;
    }

    img[style*="margin-bottom:16px"] {
        margin-bottom: 12px !important;
    }
}

@media (max-width: 480px) {
    div[style*="padding: 24px"] {
        padding: 16px !important;
    }

    input[style*="padding: 16px"] {
        padding: 10px !important;
        font-size: 14px !important;
    }

    button[style*="padding: 16px"] {
        padding: 12px !important;
        font-size: 16px !important;
    }
}
</style>
@endsection
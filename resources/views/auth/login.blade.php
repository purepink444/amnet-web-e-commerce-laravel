@extends('layouts.default')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div style="font-family: 'Kanit', sans-serif; background: #f3f3f3; padding: 20px; display: flex; justify-content: center; min-height: 100vh; align-items: center;">
    <div style="width: 100%; max-width: 400px; background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 14px rgba(0,0,0,0.1); text-align: center;">
        <img src="/mnt/data/2d1956aa-d7e5-4cdb-93ab-df858379fc06.png" style="width:100%; border-radius: 12px; margin-bottom:16px;" />
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="text" name="username" style="width: 100%; background: #d9d9d9; border-radius: 12px; padding: 16px; margin-bottom: 20px; font-size: 18px; text-align: left; border: none;" placeholder="ชื่อผู้ใช้" required autofocus>
            <input type="password" name="password" style="width: 100%; background: #d9d9d9; border-radius: 12px; padding: 16px; margin-bottom: 20px; font-size: 18px; text-align: left; border: none;" placeholder="รหัสผ่าน" required>
            <button type="submit" style="width: 100%; padding: 16px; font-size: 20px; border-radius: 16px; background: #ff8c39; border: none; cursor: pointer; color: white; font-weight: bold;">เข้าสู่ระบบ</button>
        </form>
    </div>
</div>
@endsection
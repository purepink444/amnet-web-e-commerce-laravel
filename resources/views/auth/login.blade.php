@extends('layouts.default')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div class="container">
<div class="login-container">
    <div class="login-card">
        <img src="/mnt/data/2d1956aa-d7e5-4cdb-93ab-df858379fc06.png" class="login-logo" alt="Logo" />
        <form action="{{ route('login') }}" method="POST" class="login-form">
            @csrf
            <input type="text" name="username" value="{{ old('username') }}" class="login-input" placeholder="ชื่อผู้ใช้" required autofocus>
            <input type="password" name="password" class="login-input" placeholder="รหัสผ่าน" required>
            <button type="submit" class="login-btn">เข้าสู่ระบบ</button>
        </form>
    </div>
</div>
</div>
@endsection

@section('styles')
<style>
.login-container {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.login-card {
    max-width: 400px;
    width: 100%;
}
</style>
@endsection
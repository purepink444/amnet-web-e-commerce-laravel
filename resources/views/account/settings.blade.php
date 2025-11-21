@extends('layouts.default')

@section('title', 'ตั้งค่าบัญชี')

@section('content')

<style>
    /* ===== Wireframe-like Style (เหมือน orders.blade.php) ===== */
    .wf-sidebar-card {
        background: #e6e6e6;
        border: none;
        border-radius: 10px;
        padding: 14px;
    }

    .wf-sidebar-item {
        background: #f7f7f7;
        border-radius: 6px;
        padding: 10px 12px;
        margin-bottom: 10px;
        font-size: 14px;
        display: flex;
        align-items: center;
        color: #333;
        text-decoration: none;
    }
    .wf-sidebar-item.active {
        background: #ffffff;
        border: 2px solid #cfcfcf;
    }

    .wf-main-header {
        background: #f7f7f7;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .wf-main-panel {
        background: #dcdcdc;
        border-radius: 10px;
        padding: 20px;
        min-height: 450px;
    }

    .wf-separator {
        height: 40px;
        background: #0b0b0b;
        border-radius: 4px;
        margin: 40px 0;
    }

    .settings-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
    }

    .settings-section h6 {
        color: #333;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .form-check {
        margin-bottom: 10px;
    }

    .form-check-label {
        font-weight: 500;
    }

    .btn-settings {
        background: #333;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        margin-right: 10px;
    }

    .btn-settings:hover {
        background: #000;
    }

    .info-box {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
    }

    .info-box strong {
        color: #333;
    }
</style>


<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">

            <!-- Layout Row -->
            <div class="row g-4">

                <!-- Sidebar (เหมือน wireframe) -->
                <div class="col-lg-3">
                    <div class="wf-sidebar-card">

                        <a href="{{ route('account.profile') }}"
                           class="wf-sidebar-item {{ request()->routeIs('account.profile') ? 'active' : '' }}">
                            <i class="bi bi-person me-2"></i> โปรไฟล์
                        </a>

                        <a href="{{ route('account.orders.index') }}"
                           class="wf-sidebar-item {{ request()->routeIs('account.orders.index') ? 'active' : '' }}">
                            <i class="bi bi-bag-check me-2"></i> คำสั่งซื้อ
                        </a>

                        <a href="{{ route('account.wishlist') }}"
                           class="wf-sidebar-item {{ request()->routeIs('account.wishlist') ? 'active' : '' }}">
                            <i class="bi bi-heart me-2"></i> สินค้าที่ชอบ
                        </a>

                        <a href="{{ route('account.settings') }}"
                           class="wf-sidebar-item {{ request()->routeIs('account.settings') ? 'active' : '' }}">
                            <i class="bi bi-gear me-2"></i> ตั้งค่า
                        </a>

                    </div>
                </div>

                <!-- Main Panel -->
                <div class="col-lg-9">

                    <div class="wf-main-header">
                        ตั้งค่าบัญชี
                    </div>

                    <div class="wf-main-panel">

                        <form action="{{ route('account.settings.update') }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <!-- Email Notifications -->
                            <div class="settings-section">
                                <h6>การแจ้งเตือนทางอีเมล</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="order_updates" name="notifications[order_updates]" {{ old('notifications.order_updates', $user->notification_preferences['order_updates'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="order_updates">
                                        แจ้งเตือนเมื่อสถานะคำสั่งซื้อเปลี่ยนแปลง
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="promotions" name="notifications[promotions]" {{ old('notifications.promotions', $user->notification_preferences['promotions'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="promotions">
                                        รับข่าวสารและโปรโมชั่นพิเศษ
                                    </label>
                                </div>
                            </div>

                            <!-- Privacy Settings -->
                            <div class="settings-section">
                                <h6>ความเป็นส่วนตัว</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="profile_visible" name="privacy[profile_visible]" {{ old('privacy.profile_visible', $user->privacy_settings['profile_visible'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="profile_visible">
                                        แสดงโปรไฟล์ให้ผู้อื่นเห็น
                                    </label>
                                </div>
                            </div>

                            <!-- Language & Region -->
                            <div class="settings-section">
                                <h6>ภาษาและภูมิภาค</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">ภาษา</label>
                                        <select class="form-control" name="language">
                                            <option value="th" {{ old('language', $user->language ?? 'th') == 'th' ? 'selected' : '' }}>ไทย</option>
                                            <option value="en" {{ old('language', $user->language ?? 'th') == 'en' ? 'selected' : '' }}>English</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">เขตเวลา</label>
                                        <select class="form-control" name="timezone">
                                            <option value="Asia/Bangkok" {{ old('timezone', $user->timezone ?? 'Asia/Bangkok') == 'Asia/Bangkok' ? 'selected' : '' }}>Asia/Bangkok (GMT+7)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-settings">บันทึกการตั้งค่า</button>

                        </form>

                        <!-- Security Options -->
                        <div class="settings-section">
                            <h6>ความปลอดภัยของบัญชี</h6>
                            <button class="btn-settings" onclick="alert('ฟีเจอร์นี้กำลังพัฒนา')">เปลี่ยนรหัสผ่าน</button>
                            <button class="btn-settings" onclick="alert('ฟีเจอร์นี้กำลังพัฒนา')">การยืนยันสองชั้น</button>
                        </div>

                        <!-- Account Information -->
                        <div class="settings-section">
                            <h6>ข้อมูลบัญชี</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <strong>ชื่อผู้ใช้:</strong><br>
                                        {{ $user->username }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <strong>อีเมล:</strong><br>
                                        {{ $user->email }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <strong>วันที่สมัคร:</strong><br>
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <strong>สถานะบัญชี:</strong><br>
                                        <span style="color: green;">ปกติ</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Black separator (เหมือนภาพ) -->
            <div class="wf-separator"></div>

        </div>
    </div>
</div>

@endsection

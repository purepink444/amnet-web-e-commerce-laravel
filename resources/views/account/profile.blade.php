@extends('layouts.default')

@section('title', 'โปรไฟล์ของฉัน')

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

    .form-group label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    .form-control {
        border-radius: 6px;
        border: 1px solid #ccc;
        padding: 10px;
        height: 42px;
    }

    .btn-flat {
        background: #333;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 6px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-flat:hover {
        background: #000;
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
                        แก้ไขข้อมูลโปรไฟล์
                    </div>

                    <div class="wf-main-panel">

                        <form action="{{ route('account.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label>ชื่อ</label>
                                <input type="text" name="name" class="form-control"
                                       value="{{ old('name', $user->name) }}">
                            </div>

                            <div class="form-group mb-3">
                                <label>อีเมล</label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ old('email', $user->email) }}">
                            </div>

                            <div class="form-group mb-3">
                                <label>เบอร์โทร</label>
                                <input type="text" name="phone" class="form-control"
                                       value="{{ old('phone', $user->phone) }}">
                            </div>

                            <div class="form-group mb-4">
                                <label>ที่อยู่</label>
                                <textarea name="address" class="form-control" rows="3">{{ old('address', $user->address) }}</textarea>
                            </div>

                            <button type="submit" class="btn-flat">บันทึกการเปลี่ยนแปลง</button>

                        </form>

                    </div>
                </div>
            </div>

            <!-- Black separator (เหมือนภาพ) -->
            <div class="wf-separator"></div>

        </div>
    </div>
</div>

@endsection

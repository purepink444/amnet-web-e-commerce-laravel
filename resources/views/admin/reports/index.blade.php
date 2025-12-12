@extends('layouts.admin')

@section('title', 'รายงาน')

@section('content')
<div class="admin-page-header">
    <h1 class="admin-page-title">รายงาน</h1>
    <p class="admin-page-subtitle">ดูรายงานและสถิติต่างๆ ของระบบ</p>
</div>

<!-- Reports Grid -->
<div class="admin-card admin-animate-slide-in">
    <div class="admin-card-header">
        <h3 class="admin-card-title admin-mb-0">
            <i class="fas fa-chart-line" style="margin-right: var(--admin-spacing-sm);"></i>
            รายงานหลัก
        </h3>
    </div>
    <div class="admin-card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--admin-spacing-xl);">
            <!-- Sales Report -->
            <a href="{{ route('admin.reports.sales') }}" class="admin-card admin-card-link" style="text-decoration: none;">
                <div class="admin-card-body text-center">
                    <div style="font-size: 3rem; color: var(--admin-primary); margin-bottom: var(--admin-spacing-md);">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h5 class="admin-card-title admin-mb-2">รายงานยอดขาย</h5>
                    <p class="admin-text-muted admin-mb-0">ดูรายงานยอดขายตามช่วงเวลา</p>
                </div>
            </a>

            <!-- Products Report -->
            <a href="{{ route('admin.products.index') }}" class="admin-card admin-card-link" style="text-decoration: none;">
                <div class="admin-card-body text-center">
                    <div style="font-size: 3rem; color: var(--admin-success); margin-bottom: var(--admin-spacing-md);">
                        <i class="fas fa-box-seam"></i>
                    </div>
                    <h5 class="admin-card-title admin-mb-2">รายงานสินค้า</h5>
                    <p class="admin-text-muted admin-mb-0">จัดการและดูรายงานสินค้า</p>
                </div>
            </a>

            <!-- Users Report -->
            <a href="{{ route('admin.users.index') }}" class="admin-card admin-card-link" style="text-decoration: none;">
                <div class="admin-card-body text-center">
                    <div style="font-size: 3rem; color: var(--admin-info); margin-bottom: var(--admin-spacing-md);">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="admin-card-title admin-mb-2">รายงานผู้ใช้</h5>
                    <p class="admin-text-muted admin-mb-0">ดูข้อมูลผู้ใช้และกิจกรรม</p>
                </div>
            </a>

            <!-- Orders Report -->
            <a href="{{ route('admin.orders.index') }}" class="admin-card admin-card-link" style="text-decoration: none;">
                <div class="admin-card-body text-center">
                    <div style="font-size: 3rem; color: var(--admin-warning); margin-bottom: var(--admin-spacing-md);">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h5 class="admin-card-title admin-mb-2">รายงานคำสั่งซื้อ</h5>
                    <p class="admin-text-muted admin-mb-0">ดูรายงานคำสั่งซื้อและสถานะ</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="admin-card admin-animate-slide-in">
    <div class="admin-card-header">
        <h3 class="admin-card-title admin-mb-0">
            <i class="fas fa-tachometer-alt" style="margin-right: var(--admin-spacing-sm);"></i>
            สถิติสรุป
        </h3>
    </div>
    <div class="admin-card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--admin-spacing-lg);">
            <div class="text-center">
                <div style="font-size: 2rem; font-weight: 700; color: var(--admin-primary);">{{ number_format($stats['total_sales'] ?? 0, 0) }}</div>
                <div style="color: var(--admin-text-muted);">ยอดขายรวม (บาท)</div>
            </div>
            <div class="text-center">
                <div style="font-size: 2rem; font-weight: 700; color: var(--admin-success);">{{ number_format($stats['total_orders'] ?? 0) }}</div>
                <div style="color: var(--admin-text-muted);">จำนวนคำสั่งซื้อ</div>
            </div>
            <div class="text-center">
                <div style="font-size: 2rem; font-weight: 700; color: var(--admin-info);">{{ number_format($stats['total_users'] ?? 0) }}</div>
                <div style="color: var(--admin-text-muted);">จำนวนผู้ใช้</div>
            </div>
            <div class="text-center">
                <div style="font-size: 2rem; font-weight: 700; color: var(--admin-warning);">{{ number_format($stats['total_products'] ?? 0) }}</div>
                <div style="color: var(--admin-text-muted);">จำนวนสินค้า</div>
            </div>
        </div>
    </div>
</div>
@endsection

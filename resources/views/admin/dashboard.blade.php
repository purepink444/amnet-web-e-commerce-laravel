@extends('layouts.admin')

@section('title', '')

@section('content')
<!-- Stats Cards -->
<div class="admin-stats-grid">
    <div class="admin-stat-card admin-animate-fade-in">
        <div class="admin-stat-icon">
            <i class="fas fa-box"></i>
        </div>
        <div class="admin-stat-value">{{ number_format($stats['total_products']) }}</div>
        <div class="admin-stat-label">สินค้าทั้งหมด</div>
        <a href="{{ route('admin.products.index') }}" class="admin-stat-link">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="admin-stat-card admin-animate-fade-in">
        <div class="admin-stat-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="admin-stat-value">{{ number_format($stats['total_orders']) }}</div>
        <div class="admin-stat-label">คำสั่งซื้อ</div>
        <a href="{{ route('admin.orders.index') }}" class="admin-stat-link">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="admin-stat-card admin-animate-fade-in">
        <div class="admin-stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="admin-stat-value">{{ number_format($stats['total_users']) }}</div>
        <div class="admin-stat-label">จำนวนผู้ใช้</div>
        <a href="{{ route('admin.users.index') }}" class="admin-stat-link">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="admin-stat-card admin-animate-fade-in">
        <div class="admin-stat-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="admin-stat-value">฿{{ number_format($stats['total_sales'], 0) }}</div>
        <div class="admin-stat-label">ยอดขาย</div>
        <a href="{{ route('admin.reports.index') }}" class="admin-stat-link">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<!-- Welcome Section -->
<div class="admin-card admin-animate-slide-in">
    <div class="admin-card-body">
        <div class="admin-d-flex admin-justify-between admin-align-center admin-mb-4">
            <div>
                <h2 class="admin-mb-2" style="font-size: 1.5rem; font-weight: 600; color: var(--admin-text-primary);">
                    ยินดีต้อนรับสู่ระบบจัดการหลังบ้าน
                </h2>
                <p style="color: var(--admin-text-secondary); margin: 0;">
                    สวัสดี {{ auth()->user()->username }} • วันนี้ {{ now()->format('d/m/Y') }}
                </p>
            </div>
            <div class="admin-d-flex admin-align-center" style="gap: 1rem;">
                <div class="admin-text-center">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--admin-primary);">{{ number_format($todayStats['orders_today']) }}</div>
                    <div style="font-size: 0.75rem; color: var(--admin-text-muted);">คำสั่งซื้อวันนี้</div>
                </div>
                <div class="admin-text-center">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--admin-success);">
                        ฿{{ number_format($todayStats['sales_today'], 0) }}
                    </div>
                    <div style="font-size: 0.75rem; color: var(--admin-text-muted);">ยอดขายวันนี้</div>
                </div>
                <div class="admin-text-center">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--admin-info);">{{ number_format($todayStats['users_today']) }}</div>
                    <div style="font-size: 0.75rem; color: var(--admin-text-muted);">ผู้ใช้ใหม่วันนี้</div>
                </div>
            </div>
        </div>

        <div class="admin-d-flex admin-justify-start" style="gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn-primary admin-btn-sm">
                <i class="fas fa-plus"></i>
                <span>เพิ่มสินค้า</span>
            </a>
            <a href="{{ route('admin.users.create') }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                <i class="fas fa-user-plus"></i>
                <span>เพิ่มผู้ใช้</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                <i class="fas fa-chart-bar"></i>
                <span>ดูรายงาน</span>
            </a>
            <button class="admin-btn admin-btn-secondary admin-btn-sm" onclick="exportDashboard()">
                <i class="fas fa-download"></i>
                <span>ส่งออกข้อมูล</span>
            </button>
        </div>
    </div>
</div>

<!-- Alerts Section -->
@if($lowStockProducts->count() > 0 || $pendingOrders->count() > 0)
<div class="admin-mb-4">
    @if($lowStockProducts->count() > 0)
        <div class="admin-alert admin-alert-warning admin-animate-fade-in">
            <i class="admin-alert-icon fas fa-exclamation-triangle"></i>
            <div>
                <strong>สินค้าคงเหลือน้อย</strong>
                <p style="margin: 0.25rem 0 0 0;">
                    มี {{ number_format($lowStockProducts->count()) }} สินค้าที่มีจำนวนคงเหลือน้อยกว่า 10 ชิ้น
                    <a href="{{ route('admin.products.index', ['stock_filter' => 'low']) }}" style="color: var(--admin-warning-dark); font-weight: 600; text-decoration: none;">
                        จัดการสินค้า →
                    </a>
                </p>
            </div>
        </div>
    @endif

    @if($pendingOrders->count() > 0)
        <div class="admin-alert admin-alert-info admin-animate-fade-in">
            <i class="admin-alert-icon fas fa-clock"></i>
            <div>
                <strong>คำสั่งซื้อรอดำเนินการ</strong>
                <p style="margin: 0.25rem 0 0 0;">
                    มี {{ number_format($pendingOrders->count()) }} คำสั่งซื้อที่รอดำเนินการ
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" style="color: var(--admin-info-dark); font-weight: 600; text-decoration: none;">
                        จัดการคำสั่งซื้อ →
                    </a>
                </p>
            </div>
        </div>
    @endif
</div>
@endif

<!-- Charts Section -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--admin-spacing-xl); margin-bottom: var(--admin-spacing-2xl);">
    <!-- Sales Chart -->
    <div class="admin-card admin-animate-slide-in">
        <div class="admin-card-header">
            <h3 class="admin-card-title admin-mb-0">
                <i class="fas fa-chart-line" style="margin-right: var(--admin-spacing-sm);"></i>
                สถิติยอดขายต่อเดือน
            </h3>
            <div class="admin-d-flex" style="gap: var(--admin-spacing-sm);">
                <select id="chartPeriod" class="admin-form-select" style="font-size: var(--admin-font-size-xs); padding: var(--admin-spacing-xs) var(--admin-spacing-sm);">
                    <option value="monthly">รายเดือน</option>
                    <option value="weekly">รายสัปดาห์</option>
                    <option value="daily">รายวัน</option>
                </select>
                <button class="admin-btn admin-btn-secondary admin-btn-sm" onclick="refreshChart()">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button class="admin-btn admin-btn-secondary admin-btn-sm" onclick="exportChart()">
                    <i class="fas fa-download"></i>
                </button>
            </div>
        </div>
        <div class="admin-card-body">
            <canvas id="salesChart" data-monthly-sales="{{ json_encode($monthlySales) }}" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Top Products Chart -->
    <div class="admin-card admin-animate-slide-in">
        <div class="admin-card-header">
            <h3 class="admin-card-title admin-mb-0">
                <i class="fas fa-trophy" style="margin-right: var(--admin-spacing-sm);"></i>
                สินค้าขายดี
            </h3>
            <button class="admin-btn admin-btn-secondary admin-btn-sm" onclick="refreshTopProducts()">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
        <div class="admin-card-body">
            <canvas id="topProductsChart" data-top-products="{{ json_encode($topProducts) }}" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="admin-card admin-animate-slide-in">
    <div class="admin-card-header">
        <h3 class="admin-card-title admin-mb-0">
            <i class="fas fa-history" style="margin-right: var(--admin-spacing-sm);"></i>
            กิจกรรมล่าสุด
        </h3>
        <a href="#" class="admin-btn admin-btn-secondary admin-btn-sm">
            <i class="fas fa-eye"></i>
            <span>ดูทั้งหมด</span>
        </a>
    </div>
    <div class="admin-card-body">
        @forelse($activityFeed as $activity)
            <div class="admin-d-flex admin-align-start" style="padding: var(--admin-spacing-md) 0; border-bottom: 1px solid var(--admin-border-accent);">
                <div class="admin-d-flex admin-align-center admin-justify-center"
                     style="width: 40px; height: 40px; border-radius: var(--admin-radius-full); background: var(--admin-{{ $activity['color'] }}-lighter); color: var(--admin-{{ $activity['color'] }}-dark); margin-right: var(--admin-spacing-md); flex-shrink: 0;">
                    <i class="{{ $activity['icon'] }}"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: var(--admin-text-primary); margin-bottom: var(--admin-spacing-xs);">
                        {{ $activity['title'] }}
                    </div>
                    <div style="color: var(--admin-text-secondary); font-size: var(--admin-font-size-sm); margin-bottom: var(--admin-spacing-xs);">
                        {{ $activity['description'] }}
                    </div>
                    @if(isset($activity['amount']))
                        <div style="font-weight: 700; color: var(--admin-success); margin-bottom: var(--admin-spacing-xs);">
                            {{ $activity['amount'] }}
                        </div>
                    @endif
                    <div style="font-size: var(--admin-font-size-xs); color: var(--admin-text-muted);">
                        {{ $activity['time'] }}
                    </div>
                </div>
            </div>
        @empty
            <div class="admin-text-center" style="padding: var(--admin-spacing-2xl) 0;">
                <i class="fas fa-inbox" style="font-size: 3rem; color: var(--admin-text-muted); margin-bottom: var(--admin-spacing-md);"></i>
                <p style="color: var(--admin-text-muted); margin: 0;">ยังไม่มีกิจกรรมล่าสุด</p>
            </div>
        @endforelse
    </div>
</div>

@endsection



@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-modern.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection



@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="{{ asset('js/pages/admin-dashboard.js') }}"></script>
@endsection
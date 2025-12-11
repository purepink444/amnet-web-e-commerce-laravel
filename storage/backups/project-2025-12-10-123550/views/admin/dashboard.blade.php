@extends('layouts.admin')

@section('title', '')

@section('content')
<div class="dashboard-container">
    <!-- Stats Cards Row -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ number_format($stats['total_products']) }}</h3>
                <p class="stat-label">สินค้าทั้งหมด</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="stat-link">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ number_format($stats['total_orders']) }}</h3>
                <p class="stat-label">คำสั่งซื้อ</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="stat-link">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ number_format($stats['total_users']) }}</h3>
                <p class="stat-label">จำนวนผู้ใช้</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="stat-link">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">฿{{ number_format($stats['total_sales'], 0) }}</h3>
                <p class="stat-label">ยอดขาย</p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="stat-link">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-main">
        <!-- Welcome Section with Quick Actions -->
        <div class="welcome-card">
            <div class="welcome-content">
                <h2 class="welcome-title">ระบบจัดการ Admin Dashboard</h2>
                <p class="welcome-subtitle">สวัสดี {{ auth()->user()->username }}</p>
                <div class="welcome-stats">
                    <div class="stat-mini">
                        <span class="stat-number">{{ number_format($todayStats['orders_today']) }}</span>
                        <span class="stat-label">คำสั่งซื้อวันนี้</span>
                    </div>
                    <div class="stat-mini">
                        <span class="stat-number">฿{{ number_format($todayStats['sales_today'], 0) }}</span>
                        <span class="stat-label">ยอดขายวันนี้</span>
                    </div>
                    <div class="stat-mini">
                        <span class="stat-number">{{ number_format($todayStats['users_today']) }}</span>
                        <span class="stat-label">ผู้ใช้ใหม่วันนี้</span>
                    </div>
                </div>
            </div>
            <div class="welcome-actions">
                <div class="quick-actions">
                    <a href="{{ route('admin.products.create') }}" class="quick-action-btn">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มสินค้า</span>
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="quick-action-btn">
                        <i class="bi bi-person-plus"></i>
                        <span>เพิ่มผู้ใช้</span>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="quick-action-btn">
                        <i class="bi bi-graph-up"></i>
                        <span>ดูรายงาน</span>
                    </a>
                    <button class="quick-action-btn" onclick="exportDashboard()">
                        <i class="bi bi-download"></i>
                        <span>ส่งออกข้อมูล</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        @if($lowStockProducts->count() > 0 || $pendingOrders->count() > 0)
        <div class="alerts-section">
            @if($lowStockProducts->count() > 0)
                            <div class="alert-card alert-warning">
                                <div class="alert-icon">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <div class="alert-content">
                                    <h6>สินค้าคงเหลือน้อย</h6>
                                    <p>มี {{ number_format($lowStockProducts->count()) }} สินค้าที่มีจำนวนคงเหลือน้อยกว่า 10 ชิ้น</p>
                                    <a href="{{ route('admin.products.index', ['stock_filter' => 'low']) }}" class="alert-link">จัดการสินค้า</a>
                                </div>
                            </div>
                        @endif
            
                        @if($pendingOrders->count() > 0)
                            <div class="alert-card alert-info">
                                <div class="alert-icon">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div class="alert-content">
                                    <h6>คำสั่งซื้อรอดำเนินการ</h6>
                                    <p>มี {{ number_format($pendingOrders->count()) }} คำสั่งซื้อที่รอดำเนินการ</p>
                                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="alert-link">จัดการคำสั่งซื้อ</a>
                                </div>
                            </div>
                        @endif
        </div>
        @endif

        <!-- Charts Section -->
        <div class="charts-grid">
            <!-- Sales Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">สถิติยอดขายต่อเดือน</h3>
                    <div class="chart-actions">
                        <select id="chartPeriod" class="chart-select">
                            <option value="monthly">รายเดือน</option>
                            <option value="weekly">รายสัปดาห์</option>
                            <option value="daily">รายวัน</option>
                        </select>
                        <button class="btn-refresh" onclick="refreshChart()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button class="btn-export" onclick="exportChart()">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart" data-monthly-sales="{{ json_encode($monthlySales) }}"></canvas>
                </div>
            </div>

            <!-- Top Products Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">สินค้าขายดี</h3>
                    <div class="chart-actions">
                        <button class="btn-refresh" onclick="refreshTopProducts()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="topProductsChart" data-top-products="{{ json_encode($topProducts) }}" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="activity-section">
            <div class="section-header">
                <h3 class="section-title">กิจกรรมล่าสุด</h3>
                <a href="#" class="view-all-link">ดูทั้งหมด</a>
            </div>
            <div class="activity-feed">
                @forelse($activityFeed as $activity)
                    <div class="activity-item">
                        <div class="activity-icon {{ $activity['color'] }}">
                            <i class="{{ $activity['icon'] }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $activity['title'] }}</div>
                            <div class="activity-description">{{ $activity['description'] }}</div>
                            @if(isset($activity['amount']))
                                <div class="activity-amount">{{ $activity['amount'] }}</div>
                            @endif
                            <div class="activity-time">{{ $activity['time'] }}</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-activity">
                        <i class="bi bi-activity"></i>
                        <p>ยังไม่มีกิจกรรมล่าสุด</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Enhanced Sidebar -->
    <div class="dashboard-sidebar">
        <!-- System Health -->
        <div class="sidebar-widget">
            <div class="widget-header">
                <h4 class="widget-title">
                    <i class="bi bi-activity"></i>
                    สถานะระบบ
                </h4>
            </div>
            <div class="widget-content">
                <div class="health-metrics">
                    <div class="health-item">
                        <span class="health-label">Server Load</span>
                        <div class="health-bar">
                            <div class="health-fill" style="width: {{ $systemHealth['server_load'] }}%"></div>
                        </div>
                        <span class="health-value">{{ $systemHealth['server_load'] }}%</span>
                    </div>
                    <div class="health-item">
                        <span class="health-label">Memory</span>
                        <div class="health-bar">
                            <div class="health-fill" style="width: {{ $systemHealth['memory_usage'] }}%"></div>
                        </div>
                        <span class="health-value">{{ $systemHealth['memory_usage'] }}%</span>
                    </div>
                    <div class="health-item">
                        <span class="health-label">Disk Usage</span>
                        <div class="health-bar">
                            <div class="health-fill" style="width: {{ $systemHealth['disk_usage'] }}%"></div>
                        </div>
                        <span class="health-value">{{ $systemHealth['disk_usage'] }}%</span>
                    </div>
                    <div class="health-item">
                        <span class="health-label">Uptime</span>
                        <span class="health-value">{{ $systemHealth['uptime'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="sidebar-widget">
            <div class="widget-header">
                <h4 class="widget-title">
                    <i class="bi bi-person-plus"></i>
                    ผู้ใช้ใหม่ล่าสุด
                </h4>
                <a href="{{ route('admin.users.index') }}" class="widget-link">ดูทั้งหมด</a>
            </div>
            <div class="widget-content">
                @forelse($recentUsers as $user)
                    <div class="user-item">
                        <div class="user-avatar">
                            @if($user->member && $user->member->photo_path)
                                <img src="{{ asset('storage/' . $user->member->photo_path) }}" alt="{{ $user->username }}">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ $user->member ? $user->member->first_name . ' ' . $user->member->last_name : $user->username }}</div>
                            <div class="user-time">{{ $user->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-widget">
                        <i class="bi bi-people"></i>
                        <p>ยังไม่มีผู้ใช้ใหม่</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="sidebar-widget">
            <div class="widget-header">
                <h4 class="widget-title">
                    <i class="bi bi-receipt"></i>
                    คำสั่งซื้อล่าสุด
                </h4>
                <a href="{{ route('admin.orders.index') }}" class="widget-link">ดูทั้งหมด</a>
            </div>
            <div class="widget-content">
                @forelse($recentOrders as $order)
                    <div class="order-item">
                        <div class="order-header">
                            <span class="order-id">#{{ $order->order_id }}</span>
                            <span class="order-amount">฿{{ number_format($order->total_amount, 0) }}</span>
                        </div>
                        <div class="order-customer">
                            {{ $order->member ? $order->member->first_name . ' ' . $order->member->last_name : 'ผู้ใช้' }}
                        </div>
                        <div class="order-time">{{ $order->created_at->diffForHumans() }}</div>
                        <div class="order-status">
                            <span class="status-badge {{ $order->order_status }}">
                                {{ $order->order_status === 'pending' ? 'รอดำเนินการ' : ($order->order_status === 'completed' ? 'เสร็จสิ้น' : $order->order_status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-widget">
                        <i class="bi bi-cart-x"></i>
                        <p>ยังไม่มีคำสั่งซื้อ</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="sidebar-widget">
            <div class="widget-header">
                <h4 class="widget-title">
                    <i class="bi bi-lightning"></i>
                    การดำเนินการด่วน
                </h4>
            </div>
            <div class="widget-content">
                <div class="quick-actions-grid">
                    <a href="{{ route('admin.products.create') }}" class="action-btn">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มสินค้า</span>
                    </a>
                    <a href="{{ route('admin.categories.create') }}" class="action-btn">
                        <i class="bi bi-folder-plus"></i>
                        <span>เพิ่มหมวดหมู่</span>
                    </a>
                    <a href="{{ route('admin.brands.create') }}" class="action-btn">
                        <i class="bi bi-tag"></i>
                        <span>เพิ่มแบรนด์</span>
                    </a>
                    <a href="{{ route('admin.reports.export', ['type' => 'sales']) }}" class="action-btn">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        <span>ส่งออกรายงาน</span>
                    </a>
                    <button onclick="clearCache()" class="action-btn">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>ล้างแคช</span>
                    </button>
                    <button onclick="backupData()" class="action-btn">
                        <i class="bi bi-cloud-upload"></i>
                        <span>สำรองข้อมูล</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection



@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="{{ asset('js/pages/admin-dashboard.js') }}"></script>
@endsection
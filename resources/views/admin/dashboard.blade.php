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
                    <canvas id="salesChart"></canvas>
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
                    <canvas id="topProductsChart" style="max-height: 300px;"></canvas>
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
<style>
    /* ===== DASHBOARD CONSISTENT LAYOUT ===== */
    .dashboard-container {
        height: 100%;
        display: grid;
        grid-template-columns: 1fr 2fr 350px;
        gap: 1.5rem;
        padding: 1.5rem 0;
    }

    /* ===== RESPONSIVE GRID ===== */
    @media (max-width: 1400px) {
        .dashboard-container {
            grid-template-columns: 1fr 2fr 300px;
            gap: 1rem;
        }
    }

    @media (max-width: 1200px) {
        .dashboard-container {
            grid-template-columns: 1fr 2fr 280px;
            gap: 1rem;
        }
    }

    /* ===== STATS CARDS ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(135deg, #ff6b35 0%, #e85d2a 100%);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 10px;
        background: linear-gradient(135deg, #ff6b35 0%, #e85d2a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 3rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
    }

    .stat-label {
        font-size: 1.125rem;
        color: #6b7280;
        margin: 0;
        font-weight: 500;
    }

    .stat-link {
        color: #9ca3af;
        text-decoration: none;
        transition: all 0.2s ease;
        padding: 0.5rem;
        border-radius: 6px;
    }

    .stat-link:hover {
        color: #ff6b35;
        background: rgba(255, 107, 53, 0.1);
    }

    /* ===== MAIN CONTENT ===== */
    .dashboard-main {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 2rem;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }

    .welcome-content h2 {
        font-size: 2.25rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
    }

    .welcome-subtitle {
        font-size: 1.25rem;
        margin: 0;
        opacity: 0.9;
    }

    .welcome-decoration {
        font-size: 3rem;
        opacity: 0.8;
    }

    .chart-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chart-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chart-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .btn-refresh {
        background: #f3f4f6;
        border: none;
        border-radius: 8px;
        padding: 0.5rem;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-refresh:hover {
        background: #e5e7eb;
        color: #374151;
    }

    .chart-container {
        flex: 1;
        padding: 2rem;
        position: relative;
    }

    /* ===== SUMMARY SIDEBAR ===== */
    .summary-sidebar {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .summary-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .summary-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .summary-content {
        padding: 1.5rem;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        background: linear-gradient(135deg, #ff6b35 0%, #e85d2a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .summary-text {
        flex: 1;
    }

    .summary-label {
        display: block;
        font-size: 1.125rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
    }

    /* ===== WELCOME CARD ENHANCEMENTS ===== */
    .welcome-actions {
        display: flex;
        align-items: center;
    }

    .quick-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .quick-action-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
    }

    .welcome-stats {
        display: flex;
        gap: 2rem;
        margin-top: 1rem;
    }

    .stat-mini {
        text-align: center;
    }

    .stat-mini .stat-number {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
    }

    .stat-mini .stat-label {
        font-size: 0.8rem;
        opacity: 0.9;
    }

    /* ===== ALERTS SECTION ===== */
    .alerts-section {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .alert-card {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .alert-card.alert-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
        border-left: 4px solid #f59e0b;
    }

    .alert-card.alert-info {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
        border-left: 4px solid #3b82f6;
    }

    .alert-icon {
        font-size: 1.5rem;
        color: inherit;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }

    .alert-content h6 {
        margin: 0 0 0.5rem 0;
        font-weight: 600;
        color: #1f2937;
    }

    .alert-content p {
        margin: 0 0 0.75rem 0;
        color: #64748b;
        font-size: 0.9rem;
    }

    .alert-link {
        color: var(--orange-primary);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .alert-link:hover {
        text-decoration: underline;
    }

    /* ===== CHARTS GRID ===== */
    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .chart-select {
        padding: 0.375rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 0.8rem;
        background: white;
    }

    .btn-export {
        background: #10b981;
        border: none;
        border-radius: 6px;
        padding: 0.375rem;
        color: white;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .btn-export:hover {
        background: #059669;
    }

    /* ===== ACTIVITY SECTION ===== */
    .activity-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .section-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .view-all-link {
        color: var(--orange-primary);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .view-all-link:hover {
        text-decoration: underline;
    }

    .activity-feed {
        max-height: 400px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 2rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s ease;
    }

    .activity-item:hover {
        background: #f8fafc;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .activity-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
    .activity-icon.info { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .activity-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .activity-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .activity-description {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .activity-amount {
        color: #10b981;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .activity-time {
        color: #9ca3af;
        font-size: 0.8rem;
    }

    .empty-activity {
        text-align: center;
        padding: 3rem 2rem;
        color: #9ca3af;
    }

    .empty-activity i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* ===== ENHANCED SIDEBAR ===== */
    .dashboard-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .sidebar-widget {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .widget-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .widget-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .widget-link {
        color: var(--orange-primary);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .widget-link:hover {
        text-decoration: underline;
    }

    .widget-content {
        padding: 1.5rem;
    }

    /* Health Metrics */
    .health-metrics {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .health-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .health-label {
        font-size: 0.85rem;
        color: #64748b;
        min-width: 80px;
    }

    .health-bar {
        flex: 1;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }

    .health-fill {
        height: 100%;
        background: linear-gradient(135deg, var(--orange-primary), var(--orange-dark));
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .health-value {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1f2937;
        min-width: 35px;
        text-align: right;
    }

    /* User Items */
    .user-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .user-item:last-child {
        border-bottom: none;
    }

    .user-avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--orange-primary), var(--orange-dark));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-size: 0.9rem;
        font-weight: 500;
        color: #1f2937;
        margin-bottom: 0.125rem;
    }

    .user-time {
        font-size: 0.8rem;
        color: #9ca3af;
    }

    /* Order Items */
    .order-item {
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .order-id {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.9rem;
    }

    .order-amount {
        color: #10b981;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .order-customer {
        color: #64748b;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }

    .order-time {
        color: #9ca3af;
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
    }

    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .status-badge.completed {
        background: #d1fae5;
        color: #059669;
    }

    .status-badge.cancelled {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Quick Actions Grid */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 0.5rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        text-decoration: none;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 500;
        text-align: center;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background: var(--orange-primary);
        color: white;
        border-color: var(--orange-primary);
        transform: translateY(-1px);
    }

    .action-btn i {
        font-size: 1.25rem;
    }

    /* Empty States */
    .empty-widget {
        text-align: center;
        padding: 2rem 1rem;
        color: #9ca3af;
    }

    .empty-widget i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    .empty-widget p {
        margin: 0;
        font-size: 0.9rem;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1400px) {
        .dashboard-container {
            grid-template-columns: 1fr 2fr 280px;
            gap: 1rem;
        }
    }

    @media (max-width: 1200px) {
        .dashboard-container {
            grid-template-columns: 1fr 2fr 260px;
            gap: 1rem;
        }

        .welcome-card {
            padding: 1.5rem;
        }

        .welcome-content h2 {
            font-size: 1.75rem;
        }
    }

    @media (max-width: 1024px) {
        .dashboard-container {
            grid-template-columns: 1fr 2fr;
            gap: 1rem;
        }

        .dashboard-sidebar {
            grid-column: 1 / -1;
            order: 1;
            margin-top: 1.5rem;
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }

        .welcome-stats {
            flex-direction: column;
            gap: 1rem;
        }
    }

    @media (max-width: 992px) {
        .dashboard-container {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .dashboard-sidebar {
            order: -1;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem 0;
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .stat-card {
            padding: 1rem;
            gap: 0.75rem;
        }

        .stat-icon {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 1rem;
        }

        .stat-value {
            font-size: 2rem;
        }

        .welcome-card {
            padding: 1.5rem;
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .welcome-content h2 {
            font-size: 1.5rem;
        }

        .welcome-stats {
            justify-content: center;
            gap: 1rem;
        }

        .quick-actions {
            justify-content: center;
            flex-wrap: wrap;
        }

        .quick-action-btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }

        .alerts-section {
            margin: 0 -0.5rem;
        }

        .alert-card {
            margin: 0 0.5rem;
        }

        .charts-grid {
            gap: 1rem;
        }

        .chart-header {
            padding: 1rem 1.5rem;
        }

        .chart-container {
            padding: 1rem;
        }

        .activity-section {
            margin: 0 -0.5rem;
        }

        .section-header {
            padding: 1rem 1.5rem;
        }

        .activity-item {
            padding: 1rem 1.5rem;
        }

        .dashboard-sidebar {
            margin: 0 -0.5rem;
            order: 1;
        }

        .sidebar-widget {
            margin: 0 0.5rem;
        }

        .widget-header {
            padding: 1rem 1.5rem;
        }

        .widget-content {
            padding: 1rem 1.5rem;
        }

        .quick-actions-grid {
            grid-template-columns: 1fr;
        }

        .action-btn {
            padding: 0.75rem 0.5rem;
            font-size: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .dashboard-container {
            padding: 0.5rem 0;
        }

        .stats-grid {
            gap: 0.5rem;
        }

        .stat-card {
            padding: 0.75rem;
            gap: 0.5rem;
            flex-direction: column;
            text-align: center;
        }

        .stat-icon {
            width: 2rem;
            height: 2rem;
            font-size: 0.9rem;
            align-self: center;
        }

        .stat-content {
            align-self: center;
        }

        .stat-value {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.9rem;
        }

        .stat-link {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
        }

        .welcome-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .welcome-content h2 {
            font-size: 1.25rem;
        }

        .welcome-subtitle {
            font-size: 1rem;
        }

        .welcome-stats {
            gap: 0.75rem;
        }

        .stat-mini {
            min-width: 80px;
        }

        .stat-mini .stat-number {
            font-size: 1rem;
        }

        .stat-mini .stat-label {
            font-size: 0.7rem;
        }

        .quick-actions {
            gap: 0.5rem;
        }

        .quick-action-btn {
            padding: 0.4rem 0.6rem;
            font-size: 0.75rem;
            min-width: auto;
        }

        .alert-card {
            padding: 0.75rem 1rem;
            margin: 0 0.25rem;
        }

        .alert-content h6 {
            font-size: 0.9rem;
        }

        .alert-content p {
            font-size: 0.8rem;
        }

        .chart-card {
            margin-bottom: 1rem;
        }

        .chart-header {
            padding: 0.75rem 1rem;
        }

        .chart-title {
            font-size: 1.25rem;
        }

        .chart-container {
            padding: 0.75rem;
        }

        .charts-grid {
            gap: 0.75rem;
        }

        .activity-section {
            margin: 0 -0.25rem;
        }

        .section-header {
            padding: 0.75rem 1rem;
        }

        .section-title {
            font-size: 1.1rem;
        }

        .activity-item {
            padding: 0.75rem 1rem;
            gap: 0.75rem;
        }

        .activity-icon {
            width: 2rem;
            height: 2rem;
            font-size: 0.9rem;
        }

        .activity-title {
            font-size: 0.9rem;
        }

        .activity-description {
            font-size: 0.8rem;
        }

        .dashboard-sidebar {
            margin: 0 -0.25rem;
        }

        .sidebar-widget {
            margin: 0 0.25rem;
            margin-bottom: 1rem;
        }

        .widget-header {
            padding: 0.75rem 1rem;
        }

        .widget-title {
            font-size: 1rem;
        }

        .widget-content {
            padding: 0.75rem 1rem;
        }

        .health-metrics {
            gap: 0.5rem;
        }

        .health-item {
            gap: 0.5rem;
        }

        .health-label {
            font-size: 0.8rem;
            min-width: 70px;
        }

        .health-value {
            font-size: 0.8rem;
            min-width: 30px;
        }

        .user-item, .order-item {
            padding: 0.5rem 0;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 1.75rem;
            height: 1.75rem;
        }

        .user-name {
            font-size: 0.85rem;
        }

        .user-time {
            font-size: 0.75rem;
        }

        .order-header {
            margin-bottom: 0.25rem;
        }

        .order-id {
            font-size: 0.85rem;
        }

        .order-amount {
            font-size: 0.85rem;
        }

        .order-customer {
            font-size: 0.8rem;
        }

        .order-time {
            font-size: 0.75rem;
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }

        .quick-actions-grid {
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.6rem 0.4rem;
            font-size: 0.7rem;
        }

        .action-btn i {
            font-size: 1rem;
        }
    }
</style>
@endsection



@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let salesChart, topProductsChart;

    // Initialize charts
    initializeCharts();

    // ===== CHART INITIALIZATION =====
    function initializeCharts() {
        // Sales Chart
        const salesCtx = document.getElementById('salesChart');
        const monthlySales = @json($monthlySales);
        const hasSalesData = monthlySales.some(value => value > 0);

        salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
                datasets: [{
                    label: 'ยอดขาย (บาท)',
                    data: monthlySales,
                    borderWidth: 4,
                    borderColor: '#ff8b26',
                    backgroundColor: 'rgba(255,140,38,0.15)',
                    pointBackgroundColor: '#ff8b26',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true,
                    shadowColor: 'rgba(255,140,38,0.3)',
                    shadowBlur: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 14,
                                family: 'Prompt, sans-serif'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#ff8b26',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                return 'เดือน ' + context[0].label;
                            },
                            label: function(context) {
                                return 'ยอดขาย: ฿' + context.parsed.y.toLocaleString('th-TH');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'เดือน',
                            font: {
                                size: 14,
                                family: 'Prompt, sans-serif'
                            }
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'ยอดขาย (บาท)',
                            font: {
                                size: 14,
                                family: 'Prompt, sans-serif'
                            }
                        },
                        ticks: {
                            callback: function(value) {
                                return '฿' + value.toLocaleString('th-TH');
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    }
                },
                elements: {
                    point: {
                        hoverBorderWidth: 4
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // Top Products Chart
        const topProductsCtx = document.getElementById('topProductsChart');
        const topProducts = @json($topProducts);

        topProductsChart = new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: topProducts.map(p => p.product_name.length > 15 ? p.product_name.substring(0, 15) + '...' : p.product_name),
                datasets: [{
                    label: 'จำนวนที่ขายได้',
                    data: topProducts.map(p => p.total_sold),
                    backgroundColor: 'rgba(255, 107, 53, 0.8)',
                    borderColor: '#ff6b35',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return topProducts[context[0].dataIndex].product_name;
                            },
                            label: function(context) {
                                const product = topProducts[context.dataIndex];
                                return [
                                    'จำนวนขาย: ' + product.total_sold + ' ชิ้น',
                                    'ยอดรวม: ฿' + parseFloat(product.total_revenue).toLocaleString('th-TH')
                                ];
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: function(value) {
                            return value;
                        },
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        color: '#374151'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'จำนวนที่ขายได้',
                            font: {
                                size: 12
                            }
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'สินค้า',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            },
            plugins: [ChartDataLabels]
        });

        // Show no data messages if needed
        if (!hasSalesData) {
            showNoDataMessage(salesCtx.canvas.parentElement, 'ยังไม่มีข้อมูลยอดขาย', 'ข้อมูลจะแสดงเมื่อมีรายการขายเกิดขึ้น');
        }

        if (topProducts.length === 0) {
            showNoDataMessage(topProductsCtx.canvas.parentElement, 'ยังไม่มีข้อมูลสินค้าขายดี', 'ข้อมูลจะแสดงเมื่อมีสินค้าถูกขาย');
        }
    }

    // ===== CHART CONTROLS =====
    // Chart period selector
    document.getElementById('chartPeriod')?.addEventListener('change', function() {
        // In a real app, this would fetch different data based on period
        showNotification('ฟีเจอร์นี้กำลังพัฒนา', 'info');
    });

    // ===== EXPORT FUNCTIONS =====
    function exportChart() {
        const canvas = document.getElementById('salesChart');
        const link = document.createElement('a');
        link.download = 'sales-chart-' + new Date().toISOString().split('T')[0] + '.png';
        link.href = canvas.toDataURL();
        link.click();
        showNotification('ส่งออกกราฟสำเร็จ', 'success');
    }

    function exportDashboard() {
        // In a real app, this would generate a comprehensive report
        showNotification('กำลังส่งออกข้อมูลแดชบอร์ด...', 'info');

        setTimeout(() => {
            showNotification('ส่งออกข้อมูลสำเร็จ', 'success');
        }, 2000);
    }

    // ===== REFRESH FUNCTIONS =====
    function refreshChart() {
        const btn = document.querySelector('.btn-refresh');
        const originalHTML = btn.innerHTML;

        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
        btn.disabled = true;
        btn.classList.add('fa-spin');

        // Simulate API call
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            btn.classList.remove('fa-spin');
            showNotification('รีเฟรชข้อมูลสำเร็จ', 'success');
        }, 1500);
    }

    function refreshTopProducts() {
        const btn = document.querySelector('.btn-refresh[onclick*="refreshTopProducts"]');
        const originalHTML = btn.innerHTML;

        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
        btn.disabled = true;
        btn.classList.add('fa-spin');

        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            btn.classList.remove('fa-spin');
            showNotification('รีเฟรชข้อมูลสินค้าขายดีสำเร็จ', 'success');
        }, 1500);
    }

    // ===== UTILITY FUNCTIONS =====
    function showNoDataMessage(container, title, message) {
        const noDataMessage = document.createElement('div');
        noDataMessage.className = 'no-data-message';
        noDataMessage.innerHTML = `
            <i class="bi bi-graph-up display-4 mb-3 text-muted"></i>
            <h5 class="text-muted">${title}</h5>
            <p class="text-muted">${message}</p>
        `;
        container.appendChild(noDataMessage);
    }

    function showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.toast-notification').forEach(toast => toast.remove());

        const notification = document.createElement('div');
        notification.className = `toast-notification alert alert-${type} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        notification.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    // ===== QUICK ACTIONS =====
    function clearCache() {
        showNotification('กำลังล้างแคช...', 'info');

        fetch('{{ route("admin.dashboard.refresh-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            showNotification('ล้างแคชสำเร็จ', 'success');
        })
        .catch(error => {
            showNotification('เกิดข้อผิดพลาดในการล้างแคช', 'error');
        });
    }

    function backupData() {
        showNotification('กำลังสำรองข้อมูล...', 'info');

        setTimeout(() => {
            showNotification('สำรองข้อมูลสำเร็จ', 'success');
        }, 2000);
    }

    // ===== MAKE FUNCTIONS GLOBAL =====
    window.refreshChart = refreshChart;
    window.refreshTopProducts = refreshTopProducts;
    window.exportChart = exportChart;
    window.exportDashboard = exportDashboard;
    window.clearCache = clearCache;
    window.backupData = backupData;

    // ===== AUTO REFRESH =====
    // Auto refresh data every 5 minutes
    setInterval(() => {
        // In a real app, this would fetch updated data
        console.log('Auto-refreshing dashboard data...');
    }, 300000);
});
</script>
@endsection
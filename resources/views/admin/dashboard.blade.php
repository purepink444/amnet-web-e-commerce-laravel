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
                <h3 class="stat-value">{{ $stats['total_products'] }}</h3>
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
                <h3 class="stat-value">{{ $stats['total_orders'] }}</h3>
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
                <h3 class="stat-value">{{ $stats['total_users'] }}</h3>
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
        <!-- Welcome Section -->
        <div class="welcome-card">
            <div class="welcome-content">
                <h2 class="welcome-title">ระบบจัดการ Admin Dashboard</h2>
                <p class="welcome-subtitle">สวัสดี {{ auth()->user()->username }}</p>
            </div>
            <div class="welcome-decoration">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">สถิติยอดขายต่อเดือน</h3>
                <div class="chart-actions">
                    <button class="btn-refresh" onclick="refreshChart()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Summary Sidebar -->
    <div class="summary-sidebar">
        <div class="summary-header">
            <h3 class="summary-title">สรุปข้อมูล</h3>
        </div>
        <div class="summary-content">
            <div class="summary-item">
                <div class="summary-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="summary-text">
                    <span class="summary-label">คำสั่งซื้อทั้งหมด</span>
                    <strong class="summary-value">{{ $stats['total_orders'] }}</strong>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="summary-text">
                    <span class="summary-label">สินค้าทั้งหมด</span>
                    <strong class="summary-value">{{ $stats['total_products'] }}</strong>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="summary-text">
                    <span class="summary-label">ผู้ใช้ทั้งหมด</span>
                    <strong class="summary-value">{{ $stats['total_users'] }}</strong>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="summary-text">
                    <span class="summary-label">ยอดขายรวม</span>
                    <strong class="summary-value">฿{{ number_format($stats['total_sales'], 0) }}</strong>
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

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1200px) {
        .dashboard-container {
            grid-template-columns: 1fr 2fr 300px;
            gap: 1rem;
        }
    }

    @media (max-width: 992px) {
        .dashboard-container {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .summary-sidebar {
            order: -1;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem 0;
        }

        .stats-grid {
            grid-template-columns: 1fr;
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

        .chart-card .chart-container {
            padding: 1rem;
        }

        .summary-sidebar {
            margin: 0 -0.5rem;
        }
    }

    @media (max-width: 576px) {
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
            font-size: 1.5rem;
        }

        .welcome-card {
            padding: 1rem;
        }

        .chart-header {
            padding: 1rem 1.5rem;
        }

        .summary-header,
        .summary-content {
            padding: 1rem 1.5rem;
        }
    }
</style>
@endsection



@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart');

    // ตรวจสอบว่ามีข้อมูลหรือไม่
    const monthlySales = @json($monthlySales);
    const hasData = monthlySales.some(value => value > 0);

    new Chart(ctx, {
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

    // แสดงข้อความถ้าไม่มีข้อมูล
    if (!hasData) {
        const chartContainer = document.querySelector('.chart-container');
        const noDataMessage = document.createElement('div');
        noDataMessage.className = 'text-center text-muted position-absolute top-50 start-50 translate-middle';
        noDataMessage.innerHTML = '<i class="bi bi-graph-up display-4 mb-3"></i><h5>ยังไม่มีข้อมูลยอดขาย</h5><p>ข้อมูลจะแสดงเมื่อมีรายการขายเกิดขึ้น</p>';
        chartContainer.appendChild(noDataMessage);
    }
});

// Refresh chart function
function refreshChart() {
    const btn = document.querySelector('.btn-refresh');
    const originalHTML = btn.innerHTML;

    // Show loading state
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    // Simulate refresh (in real app, this would fetch new data)
    setTimeout(() => {
        // Reset button
        btn.innerHTML = originalHTML;
        btn.disabled = false;

        // Show success message
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'รีเฟรชข้อมูลสำเร็จ',
                text: 'กราฟได้อัปเดตข้อมูลล่าสุดแล้ว',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }, 1000);
}
</script>
@endsection
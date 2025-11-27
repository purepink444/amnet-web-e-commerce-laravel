@extends('layouts.admin')

@section('title', '')

@section('content')
<!-- Info boxes - Mobile First Responsive -->
<div class="row g-2 g-md-3">
    <!-- Total Products -->
    <div class="col-6 col-md-3">
        <div class="small-box bg-info">
            <div class="inner text-center">
                <h3 class="mb-1 fs-4 fs-md-3">{{ $stats['total_products'] }}</h3>
                <p class="mb-0 small">สินค้าทั้งหมด</p>
            </div>
            <div class="icon">
                <i class="fas fa-box-seam"></i>
            </div>
            <a href="{{ route('admin.products.index') }}" class="small-box-footer d-block d-md-flex align-items-center justify-content-between">
                <span class="d-none d-md-inline">ดูเพิ่มเติม</span>
                <span class="d-md-none">ดู</span>
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-6 col-md-3">
        <div class="small-box bg-success">
            <div class="inner text-center">
                <h3 class="mb-1 fs-4 fs-md-3">{{ $stats['total_orders'] }}</h3>
                <p class="mb-0 small">คำสั่งซื้อ</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="small-box-footer d-block d-md-flex align-items-center justify-content-between">
                <span class="d-none d-md-inline">ดูเพิ่มเติม</span>
                <span class="d-md-none">ดู</span>
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Total Users -->
    <div class="col-6 col-md-3">
        <div class="small-box bg-warning">
            <div class="inner text-center">
                <h3 class="mb-1 fs-4 fs-md-3">{{ $stats['total_users'] }}</h3>
                <p class="mb-0 small">ผู้ใช้</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('admin.users.index') }}" class="small-box-footer d-block d-md-flex align-items-center justify-content-between">
                <span class="d-none d-md-inline">ดูเพิ่มเติม</span>
                <span class="d-md-none">ดู</span>
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Total Sales -->
    <div class="col-6 col-md-3">
        <div class="small-box bg-danger">
            <div class="inner text-center">
                <h3 class="mb-1 fs-5 fs-md-3">฿{{ number_format($stats['total_sales'], 0) }}</h3>
                <p class="mb-0 small">ยอดขาย</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="small-box-footer d-block d-md-flex align-items-center justify-content-between">
                <span class="d-none d-md-inline">ดูเพิ่มเติม</span>
                <span class="d-md-none">ดู</span>
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Charts row - Fully Responsive -->
<div class="row g-3">
    <!-- Chart Section -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h3 class="card-title mb-2 mb-md-0 h5 h-md-4">สถิติรายเดือน</h3>
                <div class="card-tools w-100 w-md-auto">
                    <div class="btn-group btn-group-sm d-flex flex-wrap" role="group">
                        <input type="radio" class="btn-check" name="chartType" id="combined" autocomplete="off" checked>
                        <label class="btn btn-outline-primary btn-sm flex-fill" for="combined">รวม</label>

                        <input type="radio" class="btn-check" name="chartType" id="orders" autocomplete="off">
                        <label class="btn btn-outline-primary btn-sm flex-fill" for="orders">คำสั่งซื้อ</label>

                        <input type="radio" class="btn-check" name="chartType" id="sales" autocomplete="off">
                        <label class="btn btn-outline-primary btn-sm flex-fill" for="sales">ยอดขาย</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="combinedChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats Section -->
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0 h5 h-md-4">สรุปข้อมูล</h3>
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-2 p-md-3 bg-light rounded">
                            <span class="fw-semibold small">คำสั่งซื้อทั้งหมด:</span>
                            <strong class="text-primary">{{ $stats['total_orders'] }}</strong>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-2 p-md-3 bg-light rounded">
                            <span class="fw-semibold small">สินค้าทั้งหมด:</span>
                            <strong class="text-success">{{ $stats['total_products'] }}</strong>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-2 p-md-3 bg-light rounded">
                            <span class="fw-semibold small">ผู้ใช้ทั้งหมด:</span>
                            <strong class="text-info">{{ $stats['total_users'] }}</strong>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-2 p-md-3 bg-warning rounded text-white">
                            <span class="fw-semibold small">ยอดขายรวม:</span>
                            <strong class="fs-6">฿{{ number_format($stats['total_sales'], 0) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main row - Fully Responsive Welcome Section -->
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="card-title mb-0 h5 h-md-4">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    ยินดีต้อนรับสู่ระบบจัดการ Admin Dashboard
                </h3>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Welcome Message -->
                    <div class="col-12 col-md-8">
                        <h4 class="mb-2 h6 h-md-5">
                            <i class="fas fa-user-circle me-2 text-primary"></i>
                            สวัสดี, {{ auth()->user()->username }}!
                        </h4>
                        <p class="mb-3 mb-md-0 text-muted small">
                            คุณสามารถจัดการระบบต่างๆ ได้จากเมนูด้านซ้าย รวมถึงจัดการสินค้า คำสั่งซื้อ ผู้ใช้ และดูรายงานต่างๆ
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 col-md-4 text-center text-md-end">
                        <div class="d-flex flex-column flex-sm-row flex-md-column gap-2 justify-content-center justify-content-md-end">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm w-100 w-sm-auto">
                                <i class="fas fa-box-seam me-1"></i>
                                <span class="d-none d-sm-inline">จัดการสินค้า</span>
                                <span class="d-sm-none">สินค้า</span>
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-success btn-sm w-100 w-sm-auto">
                                <i class="fas fa-shopping-cart me-1"></i>
                                <span class="d-none d-sm-inline">จัดการคำสั่งซื้อ</span>
                                <span class="d-sm-none">คำสั่งซื้อ</span>
                            </a>
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-info btn-sm w-100 w-sm-auto">
                                <i class="fas fa-chart-line me-1"></i>
                                <span class="d-none d-sm-inline">ดูรายงาน</span>
                                <span class="d-sm-none">รายงาน</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* ===== MOBILE-FIRST RESPONSIVE DESIGN ===== */

/* Base responsive improvements */
.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.08) !important;
    border: none !important;
    transition: all 0.3s ease !important;
    margin-bottom: 1rem;
}

.card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
    transform: translateY(-1px);
}

/* ===== SMALL BOXES - MOBILE OPTIMIZED ===== */
.small-box {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease !important;
    margin-bottom: 0.5rem;
    min-height: 100px;
}

.small-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
}

.small-box .inner {
    padding: 0.75rem 1rem !important;
    text-align: center;
}

.small-box .inner h3 {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    margin-bottom: 0.25rem !important;
    line-height: 1.2;
}

.small-box .inner p {
    font-size: 0.75rem !important;
    margin-bottom: 0 !important;
    font-weight: 500;
}

.small-box .icon {
    font-size: 2rem !important;
    opacity: 0.8;
    top: 10px !important;
    right: 10px !important;
}

.small-box-footer {
    padding: 0.5rem 1rem !important;
    font-size: 0.75rem !important;
    font-weight: 500;
}

/* ===== CHART CONTAINER - RESPONSIVE ===== */
.chart-container {
    position: relative;
    margin: auto;
    width: 100%;
    height: 250px !important;
    min-height: 200px;
}

/* ===== CARD HEADER - MOBILE FRIENDLY ===== */
.card-header {
    padding: 1rem !important;
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-bottom: 2px solid var(--admin-orange);
    border-radius: 15px 15px 0 0 !important;
}

.card-header h3.card-title {
    font-size: 1.1rem !important;
    font-weight: 600 !important;
    margin: 0 !important;
    color: #2d3748;
}

/* ===== WELCOME SECTION ===== */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.welcome-content h4 {
    font-size: 1.1rem !important;
    margin-bottom: 0.5rem !important;
}

.welcome-content p {
    font-size: 0.85rem !important;
    line-height: 1.4;
}

/* ===== STATS SUMMARY ===== */
.bg-light.rounded {
    transition: all 0.3s ease;
    margin-bottom: 0.5rem;
}

.bg-light.rounded:hover {
    background-color: rgba(0,0,0,0.05) !important;
    transform: scale(1.01);
}

.stats-item {
    padding: 0.75rem !important;
    font-size: 0.85rem !important;
}

/* ===== BUTTON GROUPS - MOBILE STACKED ===== */
.btn-group-sm {
    flex-wrap: wrap;
    gap: 0.25rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem !important;
    font-size: 0.75rem !important;
    margin: 0 !important;
    border-radius: 6px !important;
}

/* ===== RESPONSIVE BREAKPOINTS ===== */

/* Extra Small Devices (phones, < 576px) */
@media (max-width: 575.98px) {
    .container-fluid {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }

    .card {
        margin-bottom: 0.75rem;
        border-radius: 10px !important;
    }

    .card-body {
        padding: 1rem 0.75rem !important;
    }

    .small-box .inner h3 {
        font-size: 1.25rem !important;
    }

    .small-box .inner p {
        font-size: 0.7rem !important;
    }

    .chart-container {
        height: 200px !important;
    }

    .btn-group-sm .btn {
        flex: 1 1 auto;
        min-width: 70px;
    }

    /* Stack action buttons vertically on very small screens */
    .d-flex.flex-column.flex-sm-row {
        flex-direction: column !important;
    }

    .d-flex.flex-column.flex-sm-row .btn {
        width: 100% !important;
        margin-bottom: 0.25rem;
    }
}

/* Small Devices (tablets, >= 576px) */
@media (min-width: 576px) and (max-width: 767.98px) {
    .small-box .inner h3 {
        font-size: 1.75rem !important;
    }

    .small-box .inner p {
        font-size: 0.8rem !important;
    }

    .chart-container {
        height: 280px !important;
    }

    .card-header h3.card-title {
        font-size: 1.2rem !important;
    }

    .btn-group-sm .btn {
        padding: 0.3rem 0.75rem !important;
        font-size: 0.8rem !important;
    }
}

/* Medium Devices (small laptops, >= 768px) */
@media (min-width: 768px) and (max-width: 991.98px) {
    .small-box .inner h3 {
        font-size: 2rem !important;
    }

    .small-box .inner p {
        font-size: 0.85rem !important;
    }

    .chart-container {
        height: 320px !important;
    }

    .card-header h3.card-title {
        font-size: 1.3rem !important;
    }
}

/* Large Devices (desktops, >= 992px) */
@media (min-width: 992px) {
    .small-box .inner h3 {
        font-size: 2.5rem !important;
    }

    .small-box .inner p {
        font-size: 0.9rem !important;
    }

    .chart-container {
        height: 350px !important;
    }

    .card-header h3.card-title {
        font-size: 1.4rem !important;
    }

    .small-box .icon {
        font-size: 3rem !important;
    }
}

/* ===== ACCESSIBILITY IMPROVEMENTS ===== */
@media (prefers-reduced-motion: reduce) {
    .card,
    .small-box,
    .bg-light.rounded {
        transition: none !important;
    }

    .card:hover,
    .small-box:hover {
        transform: none !important;
    }
}

/* ===== HIGH CONTRAST MODE ===== */
@media (prefers-contrast: high) {
    .card {
        border: 2px solid #000 !important;
    }

    .small-box {
        border: 1px solid #000 !important;
    }

    .bg-light.rounded {
        border: 1px solid #000;
    }
}

/* ===== PRINT STYLES ===== */
@media print {
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }

    .small-box {
        break-inside: avoid;
    }

    .chart-container {
        display: none;
    }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const combinedCtx = document.getElementById('combinedChart').getContext('2d');

    const labels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    const ordersData = @json($monthlyOrders);
    const salesData = @json($monthlySales);

    let chart = null;

    function createChart(type) {
        if (chart) {
            chart.destroy();
        }

        let datasets = [];
        let scales = {};

        if (type === 'combined') {
            datasets = [{
                label: 'คำสั่งซื้อ',
                data: ordersData,
                borderColor: '#ff6b35',
                backgroundColor: 'rgba(255, 107, 53, 0.1)',
                yAxisID: 'y',
                tension: 0.4,
                fill: true
            }, {
                label: 'ยอดขาย (บาท)',
                data: salesData,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                yAxisID: 'y1',
                tension: 0.4,
                fill: true
            }];
            scales = {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'จำนวนคำสั่งซื้อ' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'ยอดขาย (บาท)' },
                    grid: { drawOnChartArea: false }
                }
            };
        } else if (type === 'orders') {
            datasets = [{
                label: 'คำสั่งซื้อ',
                data: ordersData,
                borderColor: '#ff6b35',
                backgroundColor: 'rgba(255, 107, 53, 0.2)',
                tension: 0.4,
                fill: true
            }];
            scales = {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'จำนวนคำสั่งซื้อ' }
                }
            };
        } else if (type === 'sales') {
            datasets = [{
                label: 'ยอดขาย (บาท)',
                data: salesData,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                tension: 0.4,
                fill: true
            }];
            scales = {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'ยอดขาย (บาท)' }
                }
            };
        }

        chart = new Chart(combinedCtx, {
            type: 'line',
            data: { labels: labels, datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: scales,
                elements: {
                    point: {
                        radius: 4,
                        hoverRadius: 6
                    },
                    line: {
                        borderWidth: 2
                    }
                }
            }
        });
    }

    // Initial chart
    createChart('combined');

    // Chart type change
    document.querySelectorAll('input[name="chartType"]').forEach(radio => {
        radio.addEventListener('change', function() {
            createChart(this.id);
        });
    });
});
</script>
@endsection
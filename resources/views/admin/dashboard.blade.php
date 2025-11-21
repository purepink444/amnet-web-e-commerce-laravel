@extends('layouts.admin')

@section('title', '')

@section('content')
<!-- Info boxes -->
<div class="row g-3">
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 class="mb-1">{{ $stats['total_products'] }}</h3>
                <p class="mb-0">สินค้าทั้งหมด</p>
            </div>
            <div class="icon">
                <i class="fas fa-box-seam"></i>
            </div>
            <a href="{{ route('admin.products.index') }}" class="small-box-footer">
                ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="small-box bg-success">
            <div class="inner">
                <h3 class="mb-1">{{ $stats['total_orders'] }}</h3>
                <p class="mb-0">คำสั่งซื้อ</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="small-box-footer">
                ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3 class="mb-1">{{ $stats['total_users'] }}</h3>
                <p class="mb-0">ผู้ใช้</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3 class="mb-1">฿{{ number_format($stats['total_sales'], 2) }}</h3>
                <p class="mb-0">ยอดขาย</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="small-box-footer">
                ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Charts row -->
<div class="row g-3">
    <div class="col-lg-8 col-md-7 col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">สถิติรายเดือน</h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="chartType" id="combined" autocomplete="off" checked>
                        <label class="btn btn-outline-primary btn-sm" for="combined">รวม</label>

                        <input type="radio" class="btn-check" name="chartType" id="orders" autocomplete="off">
                        <label class="btn btn-outline-primary btn-sm" for="orders">คำสั่งซื้อ</label>

                        <input type="radio" class="btn-check" name="chartType" id="sales" autocomplete="off">
                        <label class="btn btn-outline-primary btn-sm" for="sales">ยอดขาย</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 350px; width: 100%;">
                    <canvas id="combinedChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-5 col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">สรุปข้อมูล</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <span class="fw-semibold">คำสั่งซื้อทั้งหมด:</span>
                            <strong class="text-primary">{{ $stats['total_orders'] }}</strong>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <span class="fw-semibold">สินค้าทั้งหมด:</span>
                            <strong class="text-success">{{ $stats['total_products'] }}</strong>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <span class="fw-semibold">ผู้ใช้ทั้งหมด:</span>
                            <strong class="text-info">{{ $stats['total_users'] }}</strong>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-warning rounded text-white">
                            <span class="fw-semibold">ยอดขายรวม:</span>
                            <strong>฿{{ number_format($stats['total_sales'], 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main row -->
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="card-title mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    ยินดีต้อนรับสู่ระบบจัดการ Admin Dashboard
                </h3>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8 col-12">
                        <h4 class="mb-2">👋 สวัสดี, {{ auth()->user()->username }}!</h4>
                        <p class="mb-0 text-muted">คุณสามารถจัดการระบบต่างๆ ได้จากเมนูด้านซ้าย รวมถึงจัดการสินค้า คำสั่งซื้อ ผู้ใช้ และดูรายงานต่างๆ</p>
                    </div>
                    <div class="col-md-4 col-12 text-md-end text-center mt-3 mt-md-0">
                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-box-seam me-1"></i>จัดการสินค้า
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-shopping-cart me-1"></i>จัดการคำสั่งซื้อ
                            </a>
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-chart-line me-1"></i>ดูรายงาน
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
/* Admin Dashboard Responsive Improvements */
@media (max-width: 768px) {
    .small-box .inner h3 {
        font-size: 1.8rem !important;
        margin-bottom: 0.25rem !important;
    }

    .small-box .inner p {
        font-size: 0.85rem !important;
        margin-bottom: 0 !important;
    }

    .card-header h3 {
        font-size: 1.1rem !important;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.75rem !important;
    }

    .chart-container {
        height: 250px !important;
    }
}

@media (max-width: 576px) {
    .small-box {
        margin-bottom: 1rem !important;
    }

    .card-body .row .col-12 {
        margin-bottom: 0.5rem !important;
    }

    .d-flex.flex-column.flex-md-row.gap-2 {
        gap: 0.5rem !important;
    }

    .btn-group-sm {
        flex-wrap: wrap;
    }

    .chart-container {
        height: 200px !important;
    }
}

/* Enhanced card styling */
.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.08) !important;
    border: none !important;
    transition: all 0.3s ease !important;
}

.card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
    transform: translateY(-1px);
}

/* Small box improvements */
.small-box {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease !important;
}

.small-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
}

.small-box .inner {
    padding: 1.2rem !important;
}

.small-box .icon {
    font-size: 3rem !important;
    opacity: 0.8;
}

/* Stats summary improvements */
.bg-light.rounded {
    transition: all 0.3s ease;
}

.bg-light.rounded:hover {
    background-color: rgba(0,0,0,0.05) !important;
    transform: scale(1.02);
}

/* Welcome section gradient */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

/* Chart improvements */
.chart-container {
    position: relative;
    margin: auto;
    width: 100%;
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
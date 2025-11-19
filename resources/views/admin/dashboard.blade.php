@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Info boxes -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_products'] }}</h3>
                <p>สินค้าทั้งหมด</p>
            </div>
            <div class="icon">
                <i class="fas fa-box-seam"></i>
            </div>
            <a href="{{ route('admin.products.index') }}" class="small-box-footer">
                ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['total_orders'] }}</h3>
                <p>คำสั่งซื้อ</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="small-box-footer">
                ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['total_users'] }}</h3>
                <p>ผู้ใช้</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>฿{{ number_format($stats['total_sales'], 2) }}</h3>
                <p>ยอดขาย</p>
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
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">สถิติรายเดือน</h3>
                <div class="card-tools">
                    <div class="btn-group" role="group">
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
                <canvas id="combinedChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">สรุปข้อมูล</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>คำสั่งซื้อทั้งหมด:</span>
                    <strong>{{ $stats['total_orders'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>สินค้าทั้งหมด:</span>
                    <strong>{{ $stats['total_products'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>ผู้ใช้ทั้งหมด:</span>
                    <strong>{{ $stats['total_users'] }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>ยอดขายรวม:</span>
                    <strong>฿{{ number_format($stats['total_sales'], 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main row -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">ยินดีต้อนรับสู่ระบบจัดการ Admin Dashboard</h3>
            </div>
            <div class="card-body">
                <p>👋 สวัสดี, {{ auth()->user()->username }}!</p>
                <p>คุณสามารถจัดการระบบต่างๆ ได้จากเมนูด้านซ้าย</p>
            </div>
        </div>
    </div>
</div>
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
                interaction: { mode: 'index', intersect: false },
                scales: scales
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
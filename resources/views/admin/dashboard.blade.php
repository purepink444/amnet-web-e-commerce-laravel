@extends('layouts.admin')

@section('title', '')

@section('content')
<div class="container">

    <!-- LEFT CARDS -->
    <div class="left-box">

        <div class="card-small">
            สินค้าทั้งหมด :
            <div class="value">{{ $stats['total_products'] }}</div>
            <a href="{{ route('admin.products.index') }}"><button>ดูเพิ่มเติม</button></a>
        </div>

        <div class="card-small">
            คำสั่งซื้อ :
            <div class="value">{{ $stats['total_orders'] }}</div>
            <a href="{{ route('admin.orders.index') }}"><button>ดูเพิ่มเติม</button></a>
        </div>

        <div class="card-small">
            จำนวนผู้ใช้ :
            <div class="value">{{ $stats['total_users'] }}</div>
            <a href="{{ route('admin.users.index') }}"><button>ดูเพิ่มเติม</button></a>
        </div>

        <div class="card-small">
            ยอดขาย :
            <div class="value">฿{{ number_format($stats['total_sales'], 0) }}</div>
            <a href="{{ route('admin.reports.index') }}"><button>ดูเพิ่มเติม</button></a>
        </div>

    </div>

    <!-- MIDDLE AREA -->
    <div>
        <div class="middle-top">
            <h2>ระบบจัดการ Admin Dashboard</h2>
            <p>สวัสดี {{ auth()->user()->username }}</p>
        </div>

        <div style="height: 20px;"></div>

        <div class="middle-bottom">
            <h2>สถิติยอดขายต่อเดือน</h2>

            <!-- พื้นที่สำหรับกราฟ -->
            <canvas id="salesChart" style="width:100%; height:500px;"></canvas>

        </div>
    </div>

    <!-- RIGHT AREA -->
    <div class="right-box">
        <h2>สรุปข้อมูล</h2>
        <!-- content area -->
        <div class="summary-content">
            <div class="summary-item">
                <span>คำสั่งซื้อทั้งหมด:</span>
                <strong>{{ $stats['total_orders'] }}</strong>
            </div>
            <div class="summary-item">
                <span>สินค้าทั้งหมด:</span>
                <strong>{{ $stats['total_products'] }}</strong>
            </div>
            <div class="summary-item">
                <span>ผู้ใช้ทั้งหมด:</span>
                <strong>{{ $stats['total_users'] }}</strong>
            </div>
            <div class="summary-item">
                <span>ยอดขายรวม:</span>
                <strong>฿{{ number_format($stats['total_sales'], 0) }}</strong>
            </div>
        </div>
    </div>

</div>
@endsection

@section('styles')
<style>

    body {
        margin: 0;
        font-family: "Prompt", sans-serif;
        background: #fafafa;
    }

    /* ====== GRID LAYOUT ====== */
    .container {
        display: grid;
        grid-template-columns: 320px auto 450px;
        gap: 25px;
        padding: 25px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .container {
            grid-template-columns: 280px auto 380px;
            gap: 20px;
            padding: 20px;
        }
    }

    @media (max-width: 1024px) {
        .container {
            grid-template-columns: 250px auto 350px;
            gap: 15px;
            padding: 15px;
        }
    }

    @media (max-width: 768px) {
        .container {
            grid-template-columns: 1fr;
            gap: 15px;
            padding: 15px;
        }

        .left-box {
            order: 1;
        }

        .middle-top,
        .middle-bottom {
            order: 2;
        }

        .right-box {
            order: 3;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 10px;
            gap: 10px;
        }

        .card-small {
            padding: 15px;
            font-size: 18px;
        }

        .card-small .value {
            font-size: 32px;
        }

        .middle-top h2,
        .middle-bottom h2,
        .right-box h2 {
            font-size: 18px;
            padding: 10px;
        }

        .middle-bottom {
            min-height: 350px;
        }

        .right-box {
            min-height: 350px;
        }
    }

    /* ====== LEFT CARDS ====== */
    .left-box {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .card-small {
        background: #fff;
        border: 2px solid #ff8b26;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        font-size: 20px;
        font-weight: 500;
    }

    .card-small .value {
        margin: 15px 0;
        font-size: 40px;
        font-weight: bold;
    }

    .card-small button {
        background: #ff8b26;
        border: none;
        color: #fff;
        width: 100%;
        padding: 10px 0;
        margin-top: 15px;
        font-size: 16px;
        border-radius: 0 0 8px 8px;
        cursor: pointer;
    }

    /* ====== MIDDLE SECTION ====== */
    .middle-top,
    .middle-bottom,
    .right-box {
        background: #fff;
        border: 2px solid #ff8b26;
        border-radius: 10px;
        padding: 20px;
    }

    .middle-top h2,
    .middle-bottom h2,
    .right-box h2 {
        background: #ff8b26;
        color: #fff;
        padding: 12px;
        border-radius: 8px 8px 0 0;
        margin: -20px -20px 20px -20px;
        font-size: 22px;
    }

    .middle-bottom {
        min-height: 420px;
    }

    .right-box {
        min-height: 420px;
    }

    .summary-content {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 8px;
    }

    .summary-item span {
        font-weight: 500;
    }

    .summary-item strong {
        font-size: 18px;
        color: #ff8b26;
    }

</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ข้อมูลตัวอย่าง (สามารถดึงจากฐานข้อมูลได้)
    const ctx = document.getElementById('salesChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
            datasets: [{
                label: 'ยอดขาย (บาท)',
                data: @json($monthlySales),
                borderWidth: 3,
                borderColor: '#ff8b26',
                backgroundColor: 'rgba(255,140,38,0.25)',
                tension: 0.35,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

</script>
@endsection
@extends('layouts.default')

@section('title', 'โปรไฟล์ของฉัน - คำสั่งซื้อ')

@section('content')

<style>
    /* ===== Wireframe-like Style ===== */
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

    .table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
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
                        รายการคำสั่งซื้อทั้งหมด
                    </div>

                    <div class="wf-main-panel">

                        @if ($orders->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-bag-x display-1 text-muted mb-3"></i>
                                <h5 class="text-muted">คุณยังไม่มีคำสั่งซื้อใดๆ</h5>
                                <p class="text-muted mb-4">เริ่มช้อปปิ้งและสร้างคำสั่งซื้อแรกของคุณ</p>
                                <a href="{{ url('/product') }}" class="btn btn-primary">
                                    <i class="bi bi-shop me-2"></i>เลือกซื้อสินค้า
                                </a>
                            </div>
                        @else

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="d-none d-md-table-cell">#</th>
                                            <th>หมายเลขคำสั่งซื้อ</th>
                                            <th class="d-none d-sm-table-cell">วันที่</th>
                                            <th>สถานะ</th>
                                            <th class="d-none d-md-table-cell">ยอดรวม</th>
                                            <th>การกระทำ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td class="d-none d-md-table-cell">{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong class="text-primary">{{ $order->order_number }}</strong>
                                                    <div class="d-block d-sm-none small text-muted">{{ $order->created_at->format('d/m/Y') }}</div>
                                                </td>
                                                <td class="d-none d-sm-table-cell">{{ $order->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge 
                                                        @if($order->status == 'pending') bg-warning
                                                        @elseif($order->status == 'processing') bg-info
                                                        @elseif($order->status == 'shipped') bg-primary
                                                        @elseif($order->status == 'delivered') bg-success
                                                        @else bg-secondary @endif">
                                                        @if($order->status == 'pending') รอดำเนินการ
                                                        @elseif($order->status == 'processing') กำลังดำเนินการ
                                                        @elseif($order->status == 'shipped') จัดส่งแล้ว
                                                        @elseif($order->status == 'delivered') รับสินค้าแล้ว
                                                        @else {{ $order->status }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <strong>{{ number_format($order->total_amount, 2) }} บาท</strong>
                                                </td>
                                                <td>
                                                    <a href="{{ route('account.order.show', $order->id) }}"
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye me-1"></i> ดู
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $orders->links() }}
                            </div>

                        @endif

                    </div>
                </div>
            </div>

            <!-- Black separator (เหมือนภาพ) -->
            <div class="wf-separator"></div>

        </div>
    </div>
</div>

@endsection

@extends('layouts.default')

@section('title', 'โปรไฟล์ของฉัน - คำสั่งซื้อ')

@section('content')

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <!-- Header -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
                <div class="flex-grow-1">
                    <h2 class="mb-1">
                        <i class="bi bi-bag-check text-primary me-2"></i>
                        คำสั่งซื้อของฉัน
                    </h2>
                    <p class="text-muted mb-0 small">ดูและจัดการคำสั่งซื้อทั้งหมดของคุณ</p>
                </div>
                <a href="{{ route('account.profile') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-person me-1"></i>
                    <span class="d-none d-sm-inline">กลับไปโปรไฟล์</span>
                    <span class="d-inline d-sm-none">👤</span>
                </a>
            </div>

            <div class="row g-4">
                <!-- Sidebar Navigation -->
                <div class="col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-list me-2"></i>
                                เมนูบัญชี
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <nav class="nav flex-column">
                                <a href="{{ route('account.profile') }}" class="nav-link px-3 py-2">
                                    <i class="bi bi-person me-2"></i>
                                    <span class="d-none d-lg-inline">โปรไฟล์</span>
                                    <span class="d-inline d-lg-none">👤</span>
                                </a>
                                <a href="{{ route('account.orders.index') }}" class="nav-link px-3 py-2 active">
                                    <i class="bi bi-bag-check me-2"></i>
                                    <span class="d-none d-lg-inline">คำสั่งซื้อ</span>
                                    <span class="d-inline d-lg-none">🛒</span>
                                </a>
                                <a href="{{ route('account.wishlist') }}" class="nav-link px-3 py-2">
                                    <i class="bi bi-heart me-2"></i>
                                    <span class="d-none d-lg-inline">สินค้าที่ชอบ</span>
                                    <span class="d-inline d-lg-none">❤️</span>
                                </a>
                                <a href="{{ route('account.settings') }}" class="nav-link px-3 py-2">
                                    <i class="bi bi-gear me-2"></i>
                                    <span class="d-none d-lg-inline">ตั้งค่า</span>
                                    <span class="d-inline d-lg-none">⚙️</span>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-receipt me-2"></i>
                                รายการคำสั่งซื้อทั้งหมด
                            </h5>
                        </div>
                        <div class="card-body">
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
                                <!-- Orders Table -->
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
                                                            @else bg-secondary
                                                            @endif">
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
                                                        <a href="{{ route('account.order.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="bi bi-eye me-1"></i>
                                                            <span class="d-none d-sm-inline">ดู</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $orders->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
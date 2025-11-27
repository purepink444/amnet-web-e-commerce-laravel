@extends('layouts.default')

@section('title', 'ชำระเงินสำเร็จ')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 text-center">
                <div class="card-body p-5">
                    <!-- Success Icon -->
                    <div class="success-icon mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="text-success mb-3">ชำระเงินสำเร็จ!</h2>
                    <p class="text-muted mb-4">คำสั่งซื้อของคุณได้รับการยืนยันแล้ว</p>

                    <!-- Order Details -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">หมายเลขคำสั่งซื้อ</h6>
                                    <h4 class="text-primary mb-0">#{{ $order->order_id }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="text-start mb-4">
                        <h5 class="mb-3">รายละเอียดคำสั่งซื้อ</h5>
                        @foreach($order->items as $item)
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>{{ $item->product->product_name }} ({{ $item->quantity }} ชิ้น)</span>
                            <span>฿{{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                        @endforeach
                        <div class="d-flex justify-content-between py-2 mt-2 fw-bold">
                            <span>รวมทั้งสิ้น</span>
                            <span class="text-success">฿{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="alert alert-info text-start">
                        <h6><i class="bi bi-info-circle me-2"></i>ข้อมูลการชำระเงิน</h6>
                        <p class="mb-1">วิธีการชำระเงิน: <strong>{{ $order->payment_method === 'credit' ? 'บัตรเครดิต/เดบิต' : ($order->payment_method === 'qr' ? 'QR พร้อมเพย์' : 'ชำระปลายทาง') }}</strong></p>
                        <p class="mb-0">สถานะ: <span class="badge bg-{{ $order->payment->status === 'completed' ? 'success' : 'warning' }}">{{ $order->payment->status === 'completed' ? 'ชำระแล้ว' : 'รอการชำระ' }}</span></p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row g-2 mt-4">
                        <div class="col-6">
                            <a href="{{ route('account.orders.show', $order->order_id) }}" class="btn btn-primary w-100">
                                <i class="bi bi-eye me-2"></i>ดูคำสั่งซื้อ
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('client.products.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-bag me-2"></i>เลือกซื้อสินค้าเพิ่ม
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

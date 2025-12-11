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
                        @foreach($order->orderItems as $item)
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
                        <p class="mb-1">วิธีการชำระเงิน: <strong>{{ $order->shipping_method === 'credit' ? 'บัตรเครดิต/เดบิต' : ($order->shipping_method === 'qr' ? 'QR พร้อมเพย์' : 'ชำระปลายทาง') }}</strong></p>
                        <p class="mb-1">บริษัทขนส่ง: <strong>{{ $order->shipping->shipping_company ?? 'รอดำหนด' }}</strong></p>
                        <p class="mb-0">สถานะ: <span class="badge bg-{{ $order->payment->status === 'completed' ? 'success' : 'warning' }}">{{ $order->payment->status === 'completed' ? 'ชำระแล้ว' : 'รอการชำระ' }}</span></p>

                        @if($order->shipping_method === 'qr')
                        <div class="mt-3 text-center">
                            @php
                                $paymentData = $order->payment->payment_data;
                                if (is_string($paymentData)) {
                                    $paymentData = json_decode($paymentData, true);
                                }
                            @endphp
                            @if($paymentData && isset($paymentData['qr_code']))
                            <p class="mb-2">QR Code สำหรับชำระเงิน:</p>
                            <img src="{{ $paymentData['qr_code'] }}" alt="QR Payment" class="border p-2 rounded shadow-sm" style="max-width: 200px;">
                            <p class="mt-2 mb-1">ยอดชำระ: <strong>฿{{ number_format($order->total_amount, 2) }}</strong></p>
                            <small class="text-muted">สแกน QR Code นี้เพื่อชำระเงิน</small>
                            @else
                            <p class="mb-2 text-warning">QR Code ไม่สามารถสร้างได้ กรุณาติดต่อผู้ดูแลระบบ</p>
                            <small class="text-muted">Payment Data: {{ json_encode($paymentData) }}</small>
                            @endif
                        </div>
                        @endif
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

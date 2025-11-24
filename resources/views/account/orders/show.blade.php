@extends('layouts.default')

@section('title', 'รายละเอียดคำสั่งซื้อ - ' . $order->order_number)

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">

            <!-- Header -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">รายละเอียดคำสั่งซื้อ</h2>
                            <p class="text-muted mb-0">คำสั่งซื้อ #{{ $order->order_number }}</p>
                        </div>
                        <a href="{{ route('account.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>กลับไปรายการคำสั่งซื้อ
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Status -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-2">สถานะคำสั่งซื้อ</h5>
                                    <span class="badge fs-6 px-3 py-2 bg-{{ $order->status_badge }}">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p class="mb-1 text-muted">วันที่สั่งซื้อ</p>
                                    <p class="mb-0 fw-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">รายการสินค้า</h5>
                        </div>
                        <div class="card-body p-0">
                            @foreach($order->orderItems as $item)
                            <div class="p-4 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="{{ $item->product->image_url ?: 'https://via.placeholder.com/80x80' }}"
                                             alt="{{ $item->product->product_name }}"
                                             class="img-fluid rounded">
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1">{{ $item->product->product_name }}</h6>
                                        <p class="text-muted mb-0">จำนวน: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <div class="mb-1">
                                            <span class="text-muted">ราคา/หน่วย:</span>
                                            <span class="fw-semibold">{{ number_format($item->price_at_purchase, 2) }} บาท</span>
                                        </div>
                                        <div>
                                            <span class="text-muted">รวม:</span>
                                            <span class="fw-bold text-primary">{{ number_format($item->subtotal, 2) }} บาท</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">ข้อมูลการจัดส่ง</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>ที่อยู่จัดส่ง</h6>
                                    <p class="mb-0">{{ $order->shipping_address ?: 'ไม่ระบุ' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>วิธีการจัดส่ง</h6>
                                    <p class="mb-0">
                                        @if($order->shipping_method == 'cod')
                                            ชำระปลายทาง
                                        @elseif($order->shipping_method == 'credit_card')
                                            จัดส่งปกติ
                                        @elseif($order->shipping_method == 'qr_code')
                                            จัดส่งปกติ
                                        @else
                                            {{ $order->shipping_method ?: 'ไม่ระบุ' }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">สรุปคำสั่งซื้อ</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>จำนวนสินค้า:</span>
                                <span>{{ $order->orderItems->sum('quantity') }} ชิ้น</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>ยอดรวม:</span>
                                <span class="fw-bold text-primary fs-5">{{ number_format($order->total_amount, 2) }} บาท</span>
                            </div>

                            @if(in_array($order->order_status, ['pending', 'paid']) && $order->created_at->diffInHours(now()) <= 24)
                            <div class="alert alert-warning mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                คุณสามารถยกเลิกคำสั่งซื้อได้ภายใน 24 ชั่วโมงหลังสั่งซื้อ
                            </div>
                            <form method="POST" action="{{ route('account.orders.cancel', $order->order_id) }}"
                                  onsubmit="return confirm('คุณต้องการยกเลิกคำสั่งซื้อนี้หรือไม่? การยกเลิกจะคืนสินค้าเข้าสต็อกและดำเนินการคืนเงิน (ถ้ามี)')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-x-circle me-2"></i>ยกเลิกคำสั่งซื้อ
                                </button>
                            </form>
                            @elseif($order->order_status === 'cancelled')
                            <div class="alert alert-danger">
                                <i class="bi bi-x-circle me-2"></i>
                                คำสั่งซื้อนี้ถูกยกเลิกแล้ว
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

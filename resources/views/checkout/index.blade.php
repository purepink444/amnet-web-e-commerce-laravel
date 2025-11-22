@extends('layouts.default')

@section('title', 'ชำระเงิน')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- ข้อมูลคำสั่งซื้อ -->
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-gradient-orange text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>รายการสินค้า
                    </h4>
                </div>
                <div class="card-body">
                    @foreach($cart->items as $item)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <img src="{{ $item->product->image_url ?: 'https://via.placeholder.com/60x60' }}"
                             alt="{{ $item->product->product_name }}"
                             class="rounded me-3"
                             style="width: 60px; height: 60px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $item->product->product_name }}</h6>
                            <small class="text-muted">จำนวน: {{ $item->quantity }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">฿{{ number_format($item->subtotal, 2) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ฟอร์มชำระเงิน -->
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-gradient-orange text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card me-2"></i>ข้อมูลการชำระเงิน
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.checkout.process') }}" method="POST">
                        @csrf

                        <!-- ที่อยู่จัดส่ง -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ที่อยู่จัดส่ง</label>
                            <textarea class="form-control" name="shipping_address" rows="3" required
                                      placeholder="กรุณากรอกที่อยู่จัดส่ง">{{ old('shipping_address', auth()->user()->address) }}</textarea>
                        </div>

                        <!-- วิธีการชำระเงิน -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">วิธีการชำระเงิน</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="credit" id="credit" checked>
                                <label class="form-check-label" for="credit">
                                    บัตรเครดิต/เดบิต
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="qr" id="qr">
                                <label class="form-check-label" for="qr">
                                    QR พร้อมเพย์
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod">
                                <label class="form-check-label" for="cod">
                                    ชำระปลายทาง
                                </label>
                            </div>
                        </div>

                        <!-- สรุปราคา -->
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>จำนวนสินค้า:</span>
                            <span class="fw-bold">{{ $cart->total_items }} ชิ้น</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>ราคารวม:</span>
                            <span class="fw-bold text-orange">฿{{ number_format($cart->total_price, 2) }}</span>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 mb-3">
                            <i class="bi bi-check-circle me-2"></i>ยืนยันการสั่งซื้อ
                        </button>

                        <a href="{{ route('account.cart.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-2"></i>กลับไปแก้ไขตะกร้า
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

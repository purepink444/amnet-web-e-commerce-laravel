@extends('layouts.default')

@section('title', 'ยกเลิกการชำระเงิน')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 text-center">
                <div class="card-body p-5">
                    <!-- Cancel Icon -->
                    <div class="cancel-icon mb-4">
                        <i class="bi bi-x-circle-fill text-warning" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="text-warning mb-3">ยกเลิกการชำระเงิน</h2>
                    <p class="text-muted mb-4">การชำระเงินถูกยกเลิก ไม่มีการเรียกเก็บเงินจากบัญชีของท่าน</p>

                    <!-- Order Info -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">หมายเลขคำสั่งซื้อ</h6>
                                    <h4 class="text-primary mb-0">#{{ $order->order_id ?? 'N/A' }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reason (if provided) -->
                    @if(isset($reason) && $reason)
                    <div class="alert alert-warning text-start mb-4">
                        <h6><i class="bi bi-info-circle me-2"></i>เหตุผลการยกเลิก</h6>
                        <p class="mb-0">{{ $reason }}</p>
                    </div>
                    @endif

                    <!-- What happens next -->
                    <div class="alert alert-info text-start mb-4">
                        <h6><i class="bi bi-question-circle me-2"></i>สิ่งที่จะเกิดขึ้นต่อไป</h6>
                        <ul class="mb-0">
                            <li>คำสั่งซื้อของท่านยังคงอยู่ในระบบ แต่สถานะเป็น "ยกเลิก"</li>
                            <li>ท่านสามารถสั่งซื้อใหม่ได้ทุกเมื่อ</li>
                            <li>สินค้าในตะกร้าของท่านยังคงอยู่ หากต้องการสั่งซื้อใหม่</li>
                            <li>หากมีคำถาม สามารถติดต่อทีมบริการลูกค้าได้ตลอดเวลา</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row g-2 mt-4">
                        <div class="col-6">
                            <a href="{{ route('account.cart.index') }}" class="btn btn-primary w-100">
                                <i class="bi bi-cart me-2"></i>กลับไปตะกร้าสินค้า
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('client.products.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-bag me-2"></i>เลือกซื้อสินค้าเพิ่ม
                            </a>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="mt-4 pt-4 border-top">
                        <p class="text-muted mb-2">ต้องการความช่วยเหลือ?</p>
                        <div class="row text-center">
                            <div class="col-6">
                                <i class="bi bi-envelope text-primary"></i>
                                <br>
                                <small class="text-muted">support@example.com</small>
                            </div>
                            <div class="col-6">
                                <i class="bi bi-telephone text-primary"></i>
                                <br>
                                <small class="text-muted">02-123-4567</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

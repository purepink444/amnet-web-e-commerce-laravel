@extends('layouts.default')

@section('title', 'ชำระเงิน')

@section('content')
<style>
.checkout-progress .step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    border: 2px solid #e9ecef;
    background: white;
    transition: all 0.3s ease;
}

.checkout-progress .step.completed .step-circle {
    border-color: #198754;
}

.checkout-progress .step.active .step-circle {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.address-option-card, .payment-option-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid #e9ecef !important;
}

.address-option-card:hover, .payment-option-card:hover {
    border-color: #0d6efd !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-check-input:checked ~ .form-check-label .bg-primary {
    background-color: #0d6efd !important;
}

@media (max-width: 768px) {
    .checkout-progress .d-flex {
        flex-wrap: wrap;
        gap: 10px;
    }

    .checkout-progress .step {
        flex: 1;
        min-width: 80px;
    }

    .step-circle {
        width: 35px !important;
        height: 35px !important;
        font-size: 12px !important;
    }
}
</style>

<div class="container-fluid py-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh;">
    <!-- Progress Indicator -->
    <div class="container mb-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="checkout-progress">
                    <div class="progress mb-4" style="height: 4px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="step completed">
                            <div class="step-circle bg-success text-white">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <small class="text-success fw-semibold">ตะกร้าสินค้า</small>
                        </div>
                        <div class="step completed">
                            <div class="step-circle bg-success text-white">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <small class="text-success fw-semibold">ข้อมูลจัดส่ง</small>
                        </div>
                        <div class="step active">
                            <div class="step-circle bg-primary text-white">
                                <i class="bi bi-credit-card"></i>
                            </div>
                            <small class="text-primary fw-semibold">ชำระเงิน</small>
                        </div>
                        <div class="step">
                            <div class="step-circle bg-light text-muted">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <small class="text-muted">เสร็จสิ้น</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <!-- ข้อมูลคำสั่งซื้อ -->
            <div class="col-12 col-lg-7 mb-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="card-header bg-white border-0 py-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                <i class="bi bi-receipt text-primary fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark">รายการสินค้า</h5>
                                <small class="text-muted">{{ $cart->total_items }} รายการ</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @foreach($cart->items as $item)
                        @php
                            $subtotal = $item->quantity * $item->product->price;
                        @endphp
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-light">
                            <img src="{{ $item->product->image_url ?: 'https://via.placeholder.com/80x80' }}"
                                 alt="{{ $item->product->product_name }}"
                                 class="rounded-3 me-3 shadow-sm"
                                 style="width: 70px; height: 70px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-dark fw-semibold">{{ $item->product->product_name }}</h6>
                                <small class="text-muted">จำนวน: <span class="fw-semibold">{{ $item->quantity }}</span></small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary fs-5">฿{{ number_format($subtotal, 2) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ฟอร์มชำระเงิน -->
            <div class="col-12 col-lg-5">
                <div class="sticky-top" style="top: 20px;">
                    <!-- Order Summary Card -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                                        <i class="bi bi-calculator text-success fs-5"></i>
                                    </div>
                                    <h6 class="mb-0 text-dark fw-semibold">สรุปคำสั่งซื้อ</h6>
                                </div>
                                <span class="badge bg-primary">{{ $cart->total_items }} รายการ</span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">จำนวนสินค้า:</span>
                                <span class="fw-semibold">{{ $cart->total_items }} ชิ้น</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">ราคารวม:</span>
                                <span class="fw-bold text-primary fs-4">฿{{ number_format($cart->total_price, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form Card -->
                    <div class="card shadow-sm border-0 rounded-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                        <div class="card-header bg-white border-0 py-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3">
                                    <i class="bi bi-credit-card text-warning fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-dark">ข้อมูลการชำระเงิน</h5>
                                    <small class="text-muted">กรุณากรอกข้อมูลให้ครบถ้วน</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- แสดง error messages -->
                            @if($errors->any())
                                <div class="alert alert-danger border-0 rounded-3 mb-4" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-20 p-2 rounded-circle me-3">
                                            <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="alert-heading mb-1 text-danger">พบข้อผิดพลาด</h6>
                                            <ul class="mb-0 small">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- แสดง success message -->
                            @if(session('success'))
                                <div class="alert alert-success border-0 rounded-3 mb-4" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle text-success fs-5 me-3"></i>
                                        <span class="fw-semibold">{{ session('success') }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- แสดง error message -->
                            @if(session('error'))
                                <div class="alert alert-danger border-0 rounded-3 mb-4" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle text-danger fs-5 me-3"></i>
                                        <span class="fw-semibold">{{ session('error') }}</span>
                                    </div>
                                </div>
                            @endif

                    <form action="{{ route('account.checkout.process') }}" method="POST">
                        @csrf

                            <!-- เลือกที่อยู่จัดส่ง -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold d-flex align-items-center mb-3">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    ที่อยู่จัดส่ง <span class="text-danger">*</span>
                                </label>

                                <!-- ตัวเลือกที่อยู่ -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="address-option-card p-3 border rounded-3" data-type="registered">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input address-option" type="radio" name="address_type" value="registered" id="registered_address" checked>
                                                <label class="form-check-label fw-semibold w-100" for="registered_address">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                                            <i class="bi bi-house-door text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold text-dark">ใช้ที่อยู่ที่ลงทะเบียนไว้</div>
                                                            <small class="text-muted">ที่อยู่หลักของคุณ</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="address-option-card p-3 border rounded-3" data-type="new">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input address-option" type="radio" name="address_type" value="new" id="new_address">
                                                <label class="form-check-label fw-semibold w-100" for="new_address">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                                                            <i class="bi bi-plus-circle text-success"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold text-dark">เพิ่มที่อยู่ใหม่</div>
                                                            <small class="text-muted">สำหรับการจัดส่งครั้งนี้</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!-- แสดงที่อยู่ที่ลงทะเบียนไว้ -->
                            <div id="registered-address-display" class="border rounded p-3 mb-3 bg-light">
                                @if($memberAddress)
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-geo-alt text-primary me-2 mt-1"></i>
                                        <div>
                                            <div class="fw-semibold mb-1">{{ $memberAddress['full_name'] }}</div>
                                            <div style="white-space: pre-line;">{{ $memberAddress['formatted'] }}</div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="shipping_address" value="{{ $memberAddress['formatted'] }}">
                                @else
                                    <div class="text-muted">
                                        <i class="bi bi-info-circle me-2"></i>ไม่พบข้อมูลที่อยู่ กรุณาเพิ่มที่อยู่ใหม่
                                    </div>
                                    <input type="hidden" name="shipping_address" value="">
                                @endif
                            </div>

                            <!-- ฟอร์มเพิ่มที่อยู่ใหม่ -->
                            <div id="new-address-form" class="border rounded p-4 mb-3 bg-light" style="display: none;">
                                <h6 class="fw-semibold mb-4">
                                    <i class="bi bi-plus-circle text-primary me-2"></i>เพิ่มที่อยู่ใหม่
                                </h6>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="new_first_name" placeholder="ชื่อ" data-required style="height: 50px; font-size: 16px;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="new_last_name" placeholder="นามสกุล" data-required style="height: 50px; font-size: 16px;">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">ที่อยู่ <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="new_address" rows="3" placeholder="บ้านเลขที่ ถนน ตำบล/แขวง" data-required style="font-size: 16px;"></textarea>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">ตำบล/แขวง</label>
                                        <select name="new_subdistrict" id="new_subdistrict" class="form-control form-select-lg" disabled style="height: 50px; font-size: 16px; width: 100%;">
                                            <option value="">-- เลือกตำบล/แขวง --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">อำเภอ/เขต</label>
                                        <select name="new_district" id="new_district" class="form-control form-select-lg" disabled style="height: 50px; font-size: 16px; width: 100%;">
                                            <option value="">-- เลือกอำเภอ/เขต --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">จังหวัด <span class="text-danger">*</span></label>
                                        <select name="new_province" id="new_province" class="form-control form-select-lg" data-required style="height: 50px; font-size: 16px; width: 100%;">
                                            <option value="">-- เลือกจังหวัด --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">รหัสไปรษณีย์ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="new_postal_code" id="new_postal_code" placeholder="รหัสไปรษณีย์ 5 หลัก" maxlength="5" readonly data-required style="height: 50px; font-size: 16px; width: 100%;">
                                    </div>
                                </div>
                                <input type="hidden" name="shipping_address" id="new-address-hidden" value="">
                            </div>
                        </div>

                    <!-- เลือกบริษัทขนส่ง -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-flex align-items-center mb-3">
                            <i class="bi bi-truck text-primary me-2"></i>
                            บริษัทขนส่ง
                        </label>
                        <div class="row g-3">
                            <div class="col-12">
                                <select name="shipping_company" class="form-select form-select-lg" style="height: 50px; font-size: 16px;" required>
                                    <option value="">-- เลือกบริษัทขนส่ง --</option>
                                    <option value="ไปรษณีย์ไทย">ไปรษณีย์ไทย</option>
                                    <option value="Kerry Express">Kerry Express</option>
                                    <option value="Flash Express">Flash Express</option>
                                    <option value="J&T Express">J&T Express</option>
                                    <option value="DHL">DHL</option>
                                    <option value="FedEx">FedEx</option>
                                    <option value="อื่นๆ">อื่นๆ</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- วิธีการชำระเงิน -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold d-flex align-items-center mb-3">
                                    <i class="bi bi-wallet text-primary me-2"></i>
                                    วิธีการชำระเงิน
                                </label>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="payment-option-card p-3 border rounded-3" data-method="credit">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input payment-method" type="radio" name="payment_method" value="credit" id="credit" checked>
                                                <label class="form-check-label fw-semibold w-100" for="credit">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                                            <i class="bi bi-credit-card text-primary fs-5"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold text-dark">บัตรเครดิต/เดบิต</div>
                                                            <small class="text-muted">Visa, MasterCard, JCB</small>
                                                        </div>
                                                        <div class="text-success">
                                                            <i class="bi bi-shield-check fs-4"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="payment-option-card p-3 border rounded-3" data-method="qr">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input payment-method" type="radio" name="payment_method" value="qr" id="qr">
                                                <label class="form-check-label fw-semibold w-100" for="qr">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                                                            <i class="bi bi-qr-code text-success fs-5"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold text-dark">QR พร้อมเพย์</div>
                                                            <small class="text-muted">สแกน QR Code เพื่อชำระเงิน</small>
                                                        </div>
                                                        <div class="text-success">
                                                            <i class="bi bi-lightning-charge fs-4"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="payment-option-card p-3 border rounded-3" data-method="cod">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input payment-method" type="radio" name="payment_method" value="cod" id="cod">
                                                <label class="form-check-label fw-semibold w-100" for="cod">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3">
                                                            <i class="bi bi-cash text-warning fs-5"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold text-dark">ชำระปลายทาง</div>
                                                            <small class="text-muted">ชำระเงินเมื่อได้รับสินค้า</small>
                                                        </div>
                                                        <div class="text-warning">
                                                            <i class="bi bi-truck fs-4"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <!-- ฟอร์มบัตรเครดิต -->
                        <div id="credit-form" class="payment-form">
                            <div class="border rounded p-3 mb-3 bg-light">
                                <h6 class="fw-semibold mb-3">
                                    <i class="bi bi-credit-card text-primary me-2"></i>ข้อมูลบัตรเครดิต
                                </h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">ชื่อบนบัตร</label>
                                        <input type="text" class="form-control" name="card_name" placeholder="เช่น JOHN DOE" data-required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">หมายเลขบัตร</label>
                                        <input type="text" class="form-control" name="card_number_display" placeholder="0000 0000 0000 0000" maxlength="19" data-required>
                                        <input type="hidden" name="card_number" id="card_number_hidden">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">วันหมดอายุ</label>
                                        <input type="text" class="form-control" name="card_exp" placeholder="MM/YY" maxlength="5" data-required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">รหัส CVV</label>
                                        <input type="password" class="form-control" name="card_cvv" placeholder="***" maxlength="4" data-required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ฟอร์ม QR Payment -->
                        <div id="qr-form" class="payment-form" style="display: none;">
                            <div class="border rounded p-3 mb-3 bg-light text-center">
                                <h6 class="fw-semibold mb-3">
                                    <i class="bi bi-qr-code text-success me-2"></i>QR พร้อมเพย์
                                </h6>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    QR Code จะถูกสร้างขึ้นเมื่อคุณยืนยันการสั่งซื้อ
                                </div>
                                <p class="mb-0 text-muted">วิธีนี้รวดเร็วและปลอดภัย สแกน QR แล้วชำระเงินได้ทันที</p>
                            </div>
                        </div>

                        <!-- ฟอร์ม COD -->
                        <div id="cod-form" class="payment-form" style="display: none;">
                            <div class="border rounded p-3 mb-3 bg-warning text-center">
                                <h6 class="fw-semibold mb-3">
                                    <i class="bi bi-cash text-warning me-2"></i>ชำระปลายทาง
                                </h6>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    คุณจะชำระเงินให้กับพนักงานส่งของเมื่อได้รับสินค้า
                                </div>
                                <p class="mb-0 text-muted">วิธีนี้สะดวกและปลอดภัย ไม่ต้องกังวลเรื่องการโอนเงิน</p>
                            </div>
                        </div>

                            <!-- Submit Buttons -->
                            <div class="d-grid gap-3 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center" id="submit-btn">
                                    <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                                    <i class="bi bi-check-circle me-2"></i>
                                    <span id="submit-text">ยืนยันการสั่งซื้อ</span>
                                </button>

                                <a href="{{ route('account.cart.index') }}" class="btn btn-outline-secondary btn-lg d-flex align-items-center justify-content-center">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    กลับไปแก้ไขตะกร้า
                                </a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
console.log('Checkout JavaScript loaded successfully');

// จัดการการแสดง/ซ่อนฟอร์มการชำระเงิน
function showPaymentForm(method) {
    // ซ่อนทุกฟอร์ม
    document.querySelectorAll('.payment-form').forEach(form => {
        form.style.display = 'none';
        // Remove required from hidden fields
        form.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
            field.removeAttribute('required');
        });
    });

    // แสดงฟอร์มที่เลือก
    const selectedForm = document.getElementById(method + '-form');
    if (selectedForm) {
        selectedForm.style.display = 'block';
        // Add required to visible fields
        selectedForm.querySelectorAll('input, select, textarea').forEach(field => {
            if (field.hasAttribute('data-required')) {
                field.setAttribute('required', '');
            }
        });
    }
}

// จัดการการเลือกที่อยู่
function showAddressForm(type) {
    const registeredDisplay = document.getElementById('registered-address-display');
    const newAddressForm = document.getElementById('new-address-form');

    if (type === 'registered') {
        registeredDisplay.style.display = 'block';
        newAddressForm.style.display = 'none';
        // Remove required from hidden new address fields
        newAddressForm.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
            field.removeAttribute('required');
        });
    } else if (type === 'new') {
        registeredDisplay.style.display = 'none';
        newAddressForm.style.display = 'block';
        // Add required to visible new address fields
        newAddressForm.querySelectorAll('input[data-required], select[data-required], textarea[data-required]').forEach(field => {
            field.setAttribute('required', '');
        });
    }
}

// จัดการ visual feedback สำหรับ address options
function updateAddressOptionStyles() {
    document.querySelectorAll('.address-option-card').forEach(card => {
        const radio = card.querySelector('.address-option');
        if (radio.checked) {
            card.classList.add('border-primary', 'bg-primary', 'bg-opacity-5');
            card.classList.remove('border-light');
        } else {
            card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-5');
            card.classList.add('border-light');
        }
    });
}

// จัดการ visual feedback สำหรับ payment options
function updatePaymentOptionStyles() {
    document.querySelectorAll('.payment-option-card').forEach(card => {
        const radio = card.querySelector('.payment-method');
        if (radio.checked) {
            card.classList.add('border-primary', 'bg-primary', 'bg-opacity-5', 'shadow-sm');
            card.classList.remove('border-light');
        } else {
            card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-5', 'shadow-sm');
            card.classList.add('border-light');
        }
    });
}

// อัปเดตที่อยู่ที่ซ่อนไว้สำหรับฟอร์มใหม่
function updateNewAddress() {
    const firstName = document.querySelector('input[name="new_first_name"]').value;
    const lastName = document.querySelector('input[name="new_last_name"]').value;
    const address = document.querySelector('textarea[name="new_address"]').value;
    const subdistrict = document.querySelector('select[name="new_subdistrict"]').value;
    const district = document.querySelector('select[name="new_district"]').value;
    const provinceSelect = document.querySelector('select[name="new_province"]');
    const province = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
    const postalCode = document.querySelector('input[name="new_postal_code"]').value;

    const fullAddress = `${firstName} ${lastName}\n${address}\n${subdistrict ? subdistrict + ' ' : ''}${district ? district + ' ' : ''}${province ? province + ' ' : ''}${postalCode}`;
    document.getElementById('new-address-hidden').value = fullAddress;
}

document.querySelectorAll('.payment-method').forEach(radio => {
    radio.addEventListener('change', function() {
        showPaymentForm(this.value);
        updatePaymentOptionStyles();
    });
});

document.querySelectorAll('.address-option').forEach(radio => {
    radio.addEventListener('change', function() {
        showAddressForm(this.value);
        updateAddressOptionStyles();
        // Update shipping address when address type changes
        if (this.value === 'registered') {
            const registeredAddress = document.querySelector('#registered-address-display input[name="shipping_address"]');
            if (registeredAddress) {
                document.getElementById('new-address-hidden').value = registeredAddress.value;
            }
        }
    });
});

// จัดการการเปลี่ยนแปลงในฟอร์มที่อยู่ใหม่
document.querySelectorAll('#new-address-form input:not([name="new_postal_code"]), #new-address-form textarea, #new-address-form select').forEach(element => {
    element.addEventListener('input', updateNewAddress);
    element.addEventListener('change', updateNewAddress);
});

// จัดการ Dropdown ที่อยู่
document.addEventListener('DOMContentLoaded', function() {
    const newProvinceSelect = document.getElementById('new_province');
    const newDistrictSelect = document.getElementById('new_district');
    const newSubdistrictSelect = document.getElementById('new_subdistrict');
    const newPostalCodeInput = document.getElementById('new_postal_code');

    let newProvinces = [];
    let newDistricts = [];
    let newSubdistricts = [];

    // Load provinces for new address form
    fetch('/json/src/provinces.json')
        .then(response => response.json())
        .then(data => {
            newProvinces = data;
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.provinceCode;
                option.textContent = province.provinceNameTh;
                newProvinceSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading provinces:', error));

    // Province change for new address
    newProvinceSelect.addEventListener('change', function() {
        const provinceCode = this.value;
        newDistrictSelect.innerHTML = '<option value="">เลือกอำเภอ</option>';
        newSubdistrictSelect.innerHTML = '<option value="">เลือกตำบล</option>';
        newDistrictSelect.disabled = !provinceCode;
        newSubdistrictSelect.disabled = true;
        newPostalCodeInput.value = '';

        if (provinceCode) {
            fetch('/json/src/districts.json')
                .then(response => response.json())
                .then(data => {
                    newDistricts = data.filter(d => d.provinceCode == provinceCode);
                    newDistricts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.districtCode;
                        option.textContent = district.districtNameTh;
                        newDistrictSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading districts:', error));
        }
    });

    // District change for new address
    newDistrictSelect.addEventListener('change', function() {
        const districtCode = this.value;
        newSubdistrictSelect.innerHTML = '<option value="">เลือกตำบล</option>';
        newSubdistrictSelect.disabled = !districtCode;
        newPostalCodeInput.value = '';

        if (districtCode) {
            fetch('/json/src/subdistricts.json')
                .then(response => response.json())
                .then(data => {
                    newSubdistricts = data.filter(s => s.districtCode == districtCode);
                    newSubdistricts.forEach(subdistrict => {
                        const option = document.createElement('option');
                        option.value = subdistrict.subdistrictNameTh;
                        option.dataset.zipcode = subdistrict.postalCode;
                        option.textContent = subdistrict.subdistrictNameTh;
                        newSubdistrictSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading subdistricts:', error));
        }
    });

    // Subdistrict change for new address
    newSubdistrictSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.zipcode) {
            newPostalCodeInput.value = selectedOption.dataset.zipcode;
        }
    });
});

// จัดการการ submit ฟอร์ม
document.querySelector('form').addEventListener('submit', function(e) {
    console.log('Form submission started - processing...');

    // Show loading state
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const spinner = submitBtn.querySelector('.spinner-border');

    submitBtn.disabled = true;
    submitText.textContent = 'กำลังดำเนินการ...';
    spinner.classList.remove('d-none');

    // Card number is already cleaned in the hidden input during input

    // ตั้งค่าที่อยู่ก่อน submit
    const addressType = document.querySelector('input[name="address_type"]:checked');
    console.log('Address type:', addressType ? addressType.value : 'none');

    if (addressType) {
        if (addressType.value === 'registered') {
            const registeredAddress = document.querySelector('#registered-address-display input[name="shipping_address"]');
            if (registeredAddress) {
                document.getElementById('new-address-hidden').value = registeredAddress.value;
                console.log('Set registered address:', registeredAddress.value);
            }
        } else if (addressType.value === 'new') {
            // ตรวจสอบข้อมูลที่อยู่ใหม่
            const requiredFields = ['new_first_name', 'new_last_name', 'new_address', 'new_province'];
            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                const element = document.getElementById(field);
                if (element && !element.value.trim()) {
                    element.classList.add('is-invalid');
                    if (!firstInvalidField) firstInvalidField = element;
                    isValid = false;
                } else if (element) {
                    element.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Reset loading state
                submitBtn.disabled = false;
                submitText.textContent = 'ยืนยันการสั่งซื้อ';
                spinner.classList.add('d-none');

                alert('กรุณากรอกข้อมูลที่อยู่ให้ครบถ้วน');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            // อัปเดตที่อยู่ครั้งสุดท้าย
            updateNewAddress();
            console.log('Updated new address');
        }
    }

    const shippingAddress = document.getElementById('new-address-hidden').value;
    console.log('Final shipping address:', shippingAddress);

    // ตรวจสอบข้อมูลพื้นฐาน
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    console.log('Payment method:', paymentMethod ? paymentMethod.value : 'none');

    console.log('Form submission completed');

    // Show what data is being submitted
    const formData = new FormData(this);
    console.log('Form data:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
});

// แสดงฟอร์มเริ่มต้น
document.addEventListener('DOMContentLoaded', function() {
    // แสดงฟอร์มการชำระเงินเริ่มต้น (credit)
    const checkedPaymentRadio = document.querySelector('.payment-method:checked');
    if (checkedPaymentRadio) {
        showPaymentForm(checkedPaymentRadio.value);
    }

    // แสดงที่อยู่เริ่มต้น (registered)
    const checkedAddressRadio = document.querySelector('.address-option:checked');
    if (checkedAddressRadio) {
        showAddressForm(checkedAddressRadio.value);
    }

    // Initialize visual styles
    updateAddressOptionStyles();
    updatePaymentOptionStyles();
});



// จัดรูปแบบหมายเลขบัตรเครดิต และเก็บค่า original
document.querySelector('input[name="card_number_display"]')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    // เก็บค่า original (ไม่มี spaces) ใน hidden input
    document.getElementById('card_number_hidden').value = value;
    // แสดงค่า formatted (มี spaces)
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    e.target.value = value.trim();
});

// จัดรูปแบบวันหมดอายุ
document.querySelector('input[name="card_exp"]')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
});
</script>

@endsection

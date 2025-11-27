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
                    <!-- แสดง error messages -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">
                                <i class="bi bi-exclamation-triangle me-2"></i>พบข้อผิดพลาด
                            </h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- แสดง success message -->
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <!-- แสดง error message -->
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

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
                                <input class="form-check-input payment-method" type="radio" name="payment_method" value="credit" id="credit" checked>
                                <label class="form-check-label" for="credit">
                                    <i class="bi bi-credit-card me-2"></i>บัตรเครดิต/เดบิต
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input payment-method" type="radio" name="payment_method" value="qr" id="qr">
                                <label class="form-check-label" for="qr">
                                    <i class="bi bi-qr-code me-2"></i>QR พร้อมเพย์
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input payment-method" type="radio" name="payment_method" value="cod" id="cod">
                                <label class="form-check-label" for="cod">
                                    <i class="bi bi-cash me-2"></i>ชำระปลายทาง
                                </label>
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
                                        <input type="text" class="form-control" name="card_name" placeholder="เช่น JOHN DOE" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">หมายเลขบัตร</label>
                                        <input type="text" class="form-control" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">วันหมดอายุ</label>
                                        <input type="text" class="form-control" name="card_exp" placeholder="MM/YY" maxlength="5" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">รหัส CVV</label>
                                        <input type="password" class="form-control" name="card_cvv" placeholder="***" maxlength="4" required>
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
                                <div id="qr-loading" class="d-none">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="visually-hidden">กำลังสร้าง QR Code...</span>
                                    </div>
                                    <p class="mt-2 text-muted">กำลังสร้าง QR Code...</p>
                                </div>
                                <div id="qr-container" style="display: none;">
                                    <img id="qrImage" src="" width="180" alt="QR Payment" class="border p-2 rounded shadow-sm mb-2">
                                    <p class="mb-1">ยอดชำระ: <strong>฿{{ number_format($cart->total_price, 2) }}</strong></p>
                                    <small class="text-muted">บัญชี: 1234567890 (ธนาคารตัวอย่าง)</small>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-2"></i>ดำเนินการชำระเงิน
                                    </button>
                                    <small class="d-block mt-2 text-muted">* สแกน QR Code แล้วระบบจะดำเนินการชำระเงินอัตโนมัติ</small>
                                </div>
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

<script>
// จัดการการแสดง/ซ่อนฟอร์มการชำระเงิน
function showPaymentForm(method) {
    // ซ่อนทุกฟอร์ม
    document.querySelectorAll('.payment-form').forEach(form => {
        form.style.display = 'none';
    });

    // แสดงฟอร์มที่เลือก
    const selectedForm = document.getElementById(method + '-form');
    if (selectedForm) {
        selectedForm.style.display = 'block';

        // ถ้าเลือก QR ให้สร้าง QR code
        if (method === 'qr') {
            generateQRCode();
        }
    }
}

document.querySelectorAll('.payment-method').forEach(radio => {
    radio.addEventListener('change', function() {
        showPaymentForm(this.value);
    });
});

// แสดงฟอร์มเริ่มต้น (credit)
document.addEventListener('DOMContentLoaded', function() {
    const checkedRadio = document.querySelector('.payment-method:checked');
    if (checkedRadio) {
        showPaymentForm(checkedRadio.value);
    }
});

// ฟังก์ชันสร้าง QR Code
function generateQRCode() {
    const qrLoading = document.getElementById('qr-loading');
    const qrContainer = document.getElementById('qr-container');

    // แสดง loading
    qrLoading.classList.remove('d-none');
    qrContainer.style.display = 'none';

    // จำลองการสร้าง QR code (ในระบบจริงควรเรียก API)
    setTimeout(() => {
        // ซ่อน loading
        qrLoading.classList.add('d-none');

        // แสดง QR code (ใช้ placeholder หรือเรียก API จริง)
        const qrImage = document.getElementById('qrImage');
        qrImage.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+CiAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkFSIENPREU8L3RleHQ+Cjwvc3ZnPg==';
        qrContainer.style.display = 'block';
    }, 1000);
}


// จัดรูปแบบหมายเลขบัตรเครดิต
document.querySelector('input[name="card_number"]')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
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

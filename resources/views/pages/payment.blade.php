@extends('layouts.default')

@section('title', 'การชำระเงิน')

@section('content')
<div class="container mt-5 mb-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 600px;">
        <div class="card-header text-white text-center" style="background-color: #ff7f32;">
            <h4 class="mt-2 mb-2">การชำระเงิน</h4>
            @if(isset($order))
                <p class="mb-0">ออเดอร์ #{{ $order->order_id }} - ยอดรวม: {{ number_format($order->total_amount, 2) }} บาท</p>
            @endif
        </div>
        <div class="card-body p-4">

            <!-- แสดงสถานะการชำระเงิน -->
            @if(isset($order) && $order->payment)
                @if($order->payment->isCompleted())
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        การชำระเงินสำเร็จแล้ว
                    </div>
                @elseif($order->payment->status === 'failed')
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        การชำระเงินล้มเหลว: {{ $order->payment->payment_data['error_message'] ?? 'ไม่ทราบสาเหตุ' }}
                        @if($order->payment->canRetry())
                            <br><br>
                            <button type="button" onclick="retryPayment()" class="btn btn-warning btn-sm">
                                <i class="bi bi-arrow-repeat me-1"></i>ลองชำระเงินใหม่
                            </button>
                        @endif
                    </div>
                @elseif($order->payment->status === 'pending')
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        รอการชำระเงิน
                    </div>
                @endif
            @endif

            <form id="paymentForm" action="{{ route('payment.process', $order->order_id ?? '') }}" method="POST">
                @csrf

                <!-- วิธีการชำระเงิน -->
                <div class="mb-3">
                    <label for="payment_method" class="form-label">เลือกวิธีการชำระเงิน:</label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                        <option value="">-- กรุณาเลือก --</option>
                        <option value="credit_card">บัตรเครดิต / เดบิต</option>
                        <option value="qr_code">QR พร้อมเพย์</option>
                        <option value="cod">ชำระปลายทาง</option>
                    </select>
                </div>

                <!-- ส่วนบัตรเครดิต -->
                <div id="card-section" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อบนบัตร:</label>
                            <input type="text" class="form-control" name="card_name" placeholder="เช่น Takiang P." />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">หมายเลขบัตร:</label>
                            <input type="text" class="form-control" name="card_number" maxlength="16" placeholder="0000 0000 0000 0000" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันหมดอายุ:</label>
                            <input type="text" class="form-control" name="card_exp" placeholder="MM/YY" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">รหัส CVV:</label>
                            <input type="password" class="form-control" name="card_cvv" maxlength="3" placeholder="***" />
                        </div>
                    </div>
                </div>

                <!-- ส่วน QR พร้อมเพย์ -->
                <div id="qr-section" class="text-center mt-3" style="display: none;">
                    <p>สแกนเพื่อชำระเงิน:</p>
                    <div id="qr-container" class="d-flex justify-content-center mb-2">
                        <div id="qr-loading" class="d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">กำลังสร้าง QR Code...</span>
                            </div>
                            <p class="mt-2">กำลังสร้าง QR Code...</p>
                        </div>
                        <img id="qrImage" src="" width="180" alt="QR Payment" class="border p-2 rounded shadow-sm" style="display: none;">
                    </div>
                    <small>ยอดชำระ {{ isset($order) ? number_format($order->total_amount, 2) : '0.00' }} บาท</small>
                    <br>
                    <small class="text-muted">บัญชี: 1234567890 (ธนาคารตัวอย่าง)</small>
                    <br><br>
                    <button type="button" id="confirm-qr-btn" class="btn btn-success btn-sm" onclick="confirmQRPayment()">
                        <i class="bi bi-check-circle me-1"></i>ยืนยันการชำระเงินแล้ว
                    </button>
                    <small class="d-block mt-2 text-muted">* สำหรับ demo: คลิกปุ่มนี้หลังจากสแกน QR</small>
                </div>

                <!-- ชำระปลายทาง -->
                <div id="cod-section" class="text-center mt-3" style="display: none;">
                    <p>กรุณาชำระเงินกับพนักงานส่งของเมื่อได้รับสินค้า</p>
                </div>

                <!-- ปุ่มยืนยัน -->
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn text-white px-4" style="background-color: #ff7f32;">
                        ยืนยันการชำระเงิน
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- สคริปต์สำหรับแสดง/ซ่อนตามการเลือก -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.getElementById("payment_method").addEventListener("change", function() {
    const method = this.value;

    // ซ่อนทุกส่วนก่อน
    document.getElementById("card-section").style.display = "none";
    document.getElementById("qr-section").style.display = "none";
    document.getElementById("cod-section").style.display = "none";

    if (method === "credit_card") {
        document.getElementById("card-section").style.display = "block";
    }
    else if (method === "qr_code") {
        // แสดง loading
        document.getElementById("qr-loading").classList.remove("d-none");
        document.getElementById("qrImage").style.display = "none";

        // เรียก API เพื่อสร้าง QR code
        const orderId = {{ $order->order_id ?? 0 }};
        fetch(`/payment/order/${orderId}/generate-qr`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // ซ่อน loading
            document.getElementById("qr-loading").classList.add("d-none");

            if (data.success) {
                document.getElementById("qrImage").src = data.qr_code;
                document.getElementById("qrImage").style.display = "block";
                document.getElementById("qr-section").style.display = "block";
            } else {
                alert('ไม่สามารถสร้าง QR Code ได้: ' + (data.message || 'Unknown error'));
                document.getElementById("qr-section").style.display = "none";
            }
        })
        .catch(error => {
            // ซ่อน loading
            document.getElementById("qr-loading").classList.add("d-none");
            console.error('Error generating QR code:', error);
            alert('เกิดข้อผิดพลาดในการสร้าง QR Code');
            document.getElementById("qr-section").style.display = "none";
        });
    }
    else if (method === "cod") {
        document.getElementById("cod-section").style.display = "block";
    }
});

function confirmQRPayment() {
    if (confirm('ยืนยันว่าคุณได้ชำระเงินผ่าน QR แล้ว?')) {
        const orderId = {{ $order->order_id ?? 0 }};
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/payment/order/${orderId}/confirm-qr`;

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = csrfToken.getAttribute('content');
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }
}

function retryPayment() {
    if (confirm('ต้องการลองชำระเงินใหม่หรือไม่?')) {
        const orderId = {{ $order->order_id ?? 0 }};
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/payment/order/${orderId}/retry`;

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = csrfToken.getAttribute('content');
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }
}
</script>


@endsection

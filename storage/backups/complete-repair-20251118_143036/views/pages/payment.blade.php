@extends('layouts.default')

@section('title', 'การชำระเงิน')

@section('content')
<div class="container mt-5 mb-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 600px;">
        <div class="card-header text-white text-center" style="background-color: #ff7f32;">
            <h4 class="mt-2 mb-2">การชำระเงิน</h4>
        </div>
        <div class="card-body p-4">

            <form id="paymentForm">
                @csrf

                <!-- วิธีการชำระเงิน -->
                <div class="mb-3">
                    <label for="payment_method" class="form-label">เลือกวิธีการชำระเงิน:</label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                        <option value="">-- กรุณาเลือก --</option>
                        <option value="credit">บัตรเครดิต / เดบิต</option>
                        <option value="qr">QR พร้อมเพย์</option>
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
                        <img id="qrImage" src="" width="180" alt="QR Payment" class="border p-2 rounded shadow-sm">
                    </div>
                    <small>ยอดชำระ 99.00 บาท (ตัวอย่าง)</small>
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

    if (method === "credit") {
        document.getElementById("card-section").style.display = "block";
    } 
    else if (method === "qr") {
        const qr = document.getElementById("qrImage");
        const mockData = "00020101021129370016A000000677010111011300668123456785802TH530376454099.005802TH6304ABCD";
        const qrAPI = "https://api.qrserver.com/v1/create-qr-code/?data=" + encodeURIComponent(mockData) + "&size=180x180";
        qr.src = qrAPI;
        document.getElementById("qr-section").style.display = "block";
    } 
    else if (method === "cod") {
        document.getElementById("cod-section").style.display = "block";
    }
});
</script>


@endsection

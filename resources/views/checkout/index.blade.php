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

                        <!-- เลือกที่อยู่จัดส่ง -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">ที่อยู่จัดส่ง <span class="text-danger">*</span></label>

                            <!-- ตัวเลือกที่อยู่ -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input address-option" type="radio" name="address_type" value="registered" id="registered_address" checked>
                                    <label class="form-check-label fw-semibold" for="registered_address">
                                        <i class="bi bi-house-door me-2"></i>ใช้ที่อยู่ที่ลงทะเบียนไว้
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input address-option" type="radio" name="address_type" value="new" id="new_address">
                                    <label class="form-check-label fw-semibold" for="new_address">
                                        <i class="bi bi-plus-circle me-2"></i>เพิ่มที่อยู่ใหม่
                                    </label>
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
                                        <input type="text" class="form-control" name="new_first_name" placeholder="ชื่อ" style="height: 50px; font-size: 16px;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="new_last_name" placeholder="นามสกุล" style="height: 50px; font-size: 16px;">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">ที่อยู่ <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="new_address" rows="3" placeholder="บ้านเลขที่ ถนน ตำบล/แขวง" style="font-size: 16px;"></textarea>
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
                                        <select name="new_province" id="new_province" class="form-control form-select-lg" style="height: 50px; font-size: 16px; width: 100%;">
                                            <option value="">-- เลือกจังหวัด --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">รหัสไปรษณีย์ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="new_postal_code" id="new_postal_code" placeholder="รหัสไปรษณีย์ 5 หลัก" maxlength="5" readonly required style="height: 50px; font-size: 16px; width: 100%;">
                                    </div>
                                </div>
                                <input type="hidden" name="shipping_address" id="new-address-hidden" value="">
                            </div>
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
                                        <input type="text" class="form-control" name="card_number_display" placeholder="0000 0000 0000 0000" maxlength="19" required>
                                        <input type="hidden" name="card_number" id="card_number_hidden">
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
console.log('Checkout JavaScript loaded successfully');

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

// จัดการการเลือกที่อยู่
function showAddressForm(type) {
    const registeredDisplay = document.getElementById('registered-address-display');
    const newAddressForm = document.getElementById('new-address-form');

    if (type === 'registered') {
        registeredDisplay.style.display = 'block';
        newAddressForm.style.display = 'none';
    } else if (type === 'new') {
        registeredDisplay.style.display = 'none';
        newAddressForm.style.display = 'block';
    }
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
    });
});

document.querySelectorAll('.address-option').forEach(radio => {
    radio.addEventListener('change', function() {
        showAddressForm(this.value);
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

@extends('layouts.default')

@section('title', 'สมัครสมาชิก')

@section('styles')
<style>
/* Layout พื้นหลัง */
.register-layout {
    background: #fff;
    font-family: 'Kanit', sans-serif;
}

/* กล่องฟอร์ม */
.register-card {
    background: #ffffff;
    border-radius: 12px;
}

/* Label */
.form-label {
    font-weight: 600;
    font-size: 16px;
}

/* Input / Select */
.form-input {
    width: 100%;
    background: #e5e5e5;
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-size: 16px;
    color: #333;
}

select.form-input {
    font-weight: normal;
}

.form-input:focus {
    outline: 3px solid #ff8a42;
    background: #fefefe;
}

/* ปุ่มสมัคร */
.register-btn {
    width: 200px;
    padding: 12px;
    border: none;
    background: #ff8a42;
    color: #fff;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.2s;
}

.register-btn:hover {
    background: #ff7a2c;
}
</style>
@endsection

@section('content')
<div class="register-layout py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="register-card shadow-sm border-0 p-5">

                    <form action="{{ route('register.store') }}" method="POST" id="registerForm">
                        @csrf

                        {{-- ชื่อผู้ใช้ --}}
                        <label class="form-label mb-1">ชื่อผู้ใช้</label>
                        <input type="text" name="username" class="form-input mb-3" placeholder="ชื่อผู้ใช้">

                        {{-- ชื่อ – นามสกุล --}}
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label mb-1">คำนำหน้า</label>
                                <select name="prefix" class="form-input">
                                    <option value="">เลือก</option>
                                    <option value="นาย">นาย</option>
                                    <option value="นาง">นาง</option>
                                    <option value="นางสาว">นางสาว</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1">ชื่อ</label>
                                <input type="text" name="firstname" class="form-input">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1">นามสกุล</label>
                                <input type="text" name="lastname" class="form-input">
                            </div>
                        </div>

                        {{-- อีเมล --}}
                        <label class="form-label mt-3 mb-1">อีเมล</label>
                        <input type="email" name="email" class="form-input mb-3">

                        {{-- เบอร์โทร --}}
                        <label class="form-label mb-1">เบอร์โทรศัพท์</label>
                        <input type="tel" name="phone" class="form-input mb-3">

                        {{-- ที่อยู่ --}}
                        <label class="form-label mb-1">ที่อยู่</label>
                        <input type="text" name="address" class="form-input mb-3">

                        <div class="row">
                             <div class="col-md-4">
                                 <label class="form-label mb-1">จังหวัด</label>
                                 <select name="province" id="province" class="form-input">
                                     <option value="">เลือกจังหวัด</option>
                                 </select>
                             </div>
                             <div class="col-md-4">
                                 <label class="form-label mb-1">อำเภอ/เขต</label>
                                 <select name="district" id="district" class="form-input" disabled>
                                     <option value="">เลือกอำเภอ</option>
                                 </select>
                             </div>
                             <div class="col-md-4">
                                 <label class="form-label mb-1">ตำบล/แขวง</label>
                                 <select name="subdistrict" id="subdistrict" class="form-input" disabled>
                                     <option value="">เลือกตำบล</option>
                                 </select>
                             </div>
                         </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label mb-1">รหัสไปรษณีย์</label>
                                <input type="text" name="zipcode" id="zipcode" class="form-input">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-1">รหัสผ่าน</label>
                                <input type="password" name="password" class="form-input">
                            </div>
                        </div>

                        <label class="form-label mt-3 mb-1">ยืนยันรหัสผ่าน</label>
                        <input type="password" name="password_confirmation" class="form-input mb-3">

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                            <label class="form-check-label" for="terms">
                                ยอมรับ <a href="#" target="_blank">เงื่อนไขการใช้งาน</a>
                            </label>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="register-btn">สมัครสมาชิก</button>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const subdistrictSelect = document.getElementById('subdistrict');
    const zipcodeInput = document.getElementById('zipcode');

    let provinces = [];
    let districts = [];
    let subdistricts = [];

    // Load provinces
    fetch('/json/src/provinces.json')
        .then(response => response.json())
        .then(data => {
            provinces = data;
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.provinceCode;
                option.textContent = province.provinceNameTh;
                provinceSelect.appendChild(option);
            });
        });

    // Province change
    provinceSelect.addEventListener('change', function() {
        const provinceCode = this.value;
        districtSelect.innerHTML = '<option value="">เลือกอำเภอ</option>';
        subdistrictSelect.innerHTML = '<option value="">เลือกตำบล</option>';
        districtSelect.disabled = !provinceCode;
        subdistrictSelect.disabled = true;
        zipcodeInput.value = '';

        if (provinceCode) {
            fetch('/json/src/districts.json')
                .then(response => response.json())
                .then(data => {
                    districts = data.filter(d => d.provinceCode == provinceCode);
                    districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.districtCode;
                        option.textContent = district.districtNameTh;
                        districtSelect.appendChild(option);
                    });
                });
        }
    });

    // District change
    districtSelect.addEventListener('change', function() {
        const districtCode = this.value;
        subdistrictSelect.innerHTML = '<option value="">เลือกตำบล</option>';
        subdistrictSelect.disabled = !districtCode;
        zipcodeInput.value = '';

        if (districtCode) {
            fetch('/json/src/subdistricts.json')
                .then(response => response.json())
                .then(data => {
                    subdistricts = data.filter(s => s.districtCode == districtCode);
                    subdistricts.forEach(subdistrict => {
                        const option = document.createElement('option');
                        option.value = subdistrict.subdistrictNameTh;
                        option.dataset.zipcode = subdistrict.postalCode;
                        option.textContent = subdistrict.subdistrictNameTh;
                        subdistrictSelect.appendChild(option);
                    });
                });
        }
    });

    // Subdistrict change
    subdistrictSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        zipcodeInput.value = selectedOption.dataset.zipcode || '';
    });
});
</script>
@endsection

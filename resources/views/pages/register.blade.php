@extends('layouts.default')

@section('title', 'สมัครสมาชิก')

@section('content')
<div class="bg-gradient-register py-5">
    <div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Card Header -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="bi bi-person-plus-fill me-2"></i>สมัครสมาชิก
                    </h3>
                    <p class="mb-0 mt-2">กรอกข้อมูลเพื่อสมัครสมาชิกกับเรา</p>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('register.store') }}" method="POST">
                        @csrf

                        <!-- ข้อมูลบัญชี -->
                        <div class="mb-4">
                            <h5 class="section-title">
                                <i class="bi bi-shield-lock text-primary me-2"></i>ข้อมูลบัญชี
                            </h5>
                            <hr>
                        </div>

                        <!-- ชื่อผู้ใช้ -->
                        <div class="mb-3">
                            <label class="form-label">
                                ชื่อผู้ใช้ <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   placeholder="ชื่อผู้ใช้สำหรับเข้าสู่ระบบ"
                                   required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- อีเมล -->
                        <div class="mb-3">
                            <label class="form-label">
                                อีเมล <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="example@email.com"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- รหัสผ่าน -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    รหัสผ่าน <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       placeholder="อย่างน้อย 6 ตัวอักษร"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    ยืนยันรหัสผ่าน <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       name="password_confirmation" 
                                       placeholder="กรอกรหัสผ่านอีกครั้ง"
                                       required>
                            </div>
                        </div>

                        <!-- ข้อมูลส่วนตัว -->
                        <div class="mb-4 mt-4">
                            <h5 class="section-title">
                                <i class="bi bi-person text-orange me-2"></i>ข้อมูลส่วนตัว
                            </h5>
                            <hr>
                        </div>

                        <!-- คำนำหน้า -->
                        <div class="mb-3">
                            <label class="form-label">
                                คำนำหน้าชื่อ <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('prefix') is-invalid @enderror" 
                                    name="prefix" 
                                    required>
                                <option value="">-- เลือกคำนำหน้า --</option>
                                <option value="นาย" {{ old('prefix') == 'นาย' ? 'selected' : '' }}>นาย</option>
                                <option value="นาง" {{ old('prefix') == 'นาง' ? 'selected' : '' }}>นาง</option>
                                <option value="นางสาว" {{ old('prefix') == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                            </select>
                            @error('prefix')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ชื่อ-นามสกุล -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    ชื่อ <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('firstname') is-invalid @enderror" 
                                       name="firstname" 
                                       value="{{ old('firstname') }}" 
                                       placeholder="ชื่อจริง"
                                       required>
                                @error('firstname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    นามสกุล <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('lastname') is-invalid @enderror" 
                                       name="lastname" 
                                       value="{{ old('lastname') }}" 
                                       placeholder="นามสกุล"
                                       required>
                                @error('lastname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- เบอร์โทรศัพท์ -->
                        <div class="mb-3">
                            <label class="form-label">
                                เบอร์โทรศัพท์ <span class="text-danger">*</span>
                            </label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" 
                                   value="{{ old('phone') }}" 
                                   placeholder="0812345678"
                                   pattern="[0-9]{10}"
                                   maxlength="10"
                                   required>
                            <small class="text-muted">กรอกเบอร์โทร 10 หลัก</small>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ที่อยู่ -->
                        <div class="mb-4 mt-4">
                            <h5 class="section-title">
                                <i class="bi bi-geo-alt text-orange me-2"></i>ที่อยู่
                            </h5>
                            <hr>
                        </div>

                        <!-- ที่อยู่เต็ม -->
                        <div class="mb-3">
                            <label class="form-label">
                                ที่อยู่ <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      name="address" 
                                      rows="3"
                                      placeholder="บ้านเลขที่ หมู่ที่ ซอย ถนน"
                                      required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- จังหวัด อำเภอ ตำบล รหัสไปรษณีย์ -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    จังหวัด <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('province') is-invalid @enderror" 
                                        id="province" 
                                        name="province" 
                                        required>
                                    <option value="">-- เลือกจังหวัด --</option>
                                </select>
                                @error('province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    อำเภอ <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('district') is-invalid @enderror" 
                                        id="amphur" 
                                        name="district" 
                                        required>
                                    <option value="">-- เลือกอำเภอ --</option>
                                </select>
                                @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    ตำบล <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('subdistrict') is-invalid @enderror" 
                                        id="district" 
                                        name="subdistrict" 
                                        required>
                                    <option value="">-- เลือกตำบล --</option>
                                </select>
                                @error('subdistrict')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    รหัสไปรษณีย์ <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('zipcode') is-invalid @enderror" 
                                       id="postal_code" 
                                       name="zipcode" 
                                       value="{{ old('zipcode') }}" 
                                       placeholder="รหัสไปรษณีย์ 5 หลัก"
                                       pattern="[0-9]{5}"
                                       maxlength="5"
                                       readonly
                                       required>
                                @error('zipcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- ปุ่มส่งฟอร์ม -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>สมัครสมาชิก
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>มีบัญชีอยู่แล้ว? เข้าสู่ระบบ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
:root {
    --orange-primary: #ff6b35;
    --orange-dark: #e85d2a;
    --orange-light: #ff8c5f;
    --black-primary: #1a1a1a;
    --black-secondary: #2d2d2d;
    --gray-text: #6c757d;
}

body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

.bg-gradient-register {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #e85d2a 100%);
    position: relative;
    min-height: calc(100vh - 120px);
    margin: 0 !important;
    width: 100%;
}

.bg-gradient-register::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff6b35' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.container {
    position: relative;
    z-index: 1;
}

.row {
    position: relative;
    z-index: 1;
}

.card {
    border-radius: 15px;
    overflow: hidden;
    background-color: #ffffff;
    position: relative;
    z-index: 1;
}

.card-header {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    border-bottom: 3px solid var(--black-primary);
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0;
    color: var(--black-primary);
}

.section-title i.text-primary {
    color: var(--orange-primary) !important;
}

.section-title i.text-orange {
    color: var(--orange-primary) !important;
}

.form-label {
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--black-secondary);
    margin-bottom: 0.5rem;
}

.form-control,
.form-select {
    font-size: 0.95rem;
    padding: 0.6rem 0.75rem;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    background-color: #ffffff;
    color: var(--black-primary);
}

.form-control:focus,
.form-select:focus {
    border-color: var(--orange-primary);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    background-color: #fff;
}

.btn-primary {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    border: none;
    padding: 0.75rem;
    font-weight: 600;
    color: #ffffff;
    border-radius: 8px;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-primary) 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
}

.btn-outline-secondary {
    border: 2px solid var(--black-secondary);
    color: var(--black-primary);
    border-radius: 8px;
    font-weight: 500;
}

.btn-outline-secondary:hover {
    background-color: var(--black-primary);
    border-color: var(--black-primary);
    color: #ffffff;
}

hr {
    border-color: var(--orange-primary);
    opacity: 0.3;
    border-width: 2px;
}

.text-danger {
    font-size: 0.85rem;
    color: var(--orange-dark) !important;
}

small.text-muted {
    font-size: 0.85rem;
    color: var(--gray-text);
}

.invalid-feedback {
    color: var(--orange-dark);
}

.is-invalid {
    border-color: var(--orange-dark) !important;
}

.shadow-lg {
    box-shadow: 0 25px 50px -12px rgba(255, 107, 53, 0.3) !important;
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    console.log("เริ่มโหลดข้อมูลประเทศไทย...");

    Promise.all([
        $.getJSON("/json/src/provinces.json"),
        $.getJSON("/json/src/districts.json"),
        $.getJSON("/json/src/subdistricts.json")
    ])
    .then(([provinces, districts, subdistricts]) => {
        console.log("✅ โหลดข้อมูลสำเร็จ:", {
            provinces: provinces.length,
            districts: districts.length,
            subdistricts: subdistricts.length
        });

        const $province = $('#province');
        const $amphur = $('#amphur');
        const $district = $('#district');
        const $postal = $('#postal_code');

        // โหลดจังหวัด
        $province.empty().append('<option value="">-- เลือกจังหวัด --</option>');
        provinces.forEach(p => {
            $province.append(`<option value="${p.provinceCode}">${p.provinceNameTh}</option>`);
        });

        // เลือกจังหวัด
        $province.on('change', function () {
            const provinceCode = $(this).val();

            $amphur.html('<option value="">-- เลือกอำเภอ --</option>').prop('disabled', true);
            $district.html('<option value="">-- เลือกตำบล --</option>').prop('disabled', true);
            $postal.val('');

            if (provinceCode) {
                const filteredAmphur = districts.filter(d => d.provinceCode == provinceCode);
                filteredAmphur.forEach(a => {
                    $amphur.append(`<option value="${a.districtCode}">${a.districtNameTh}</option>`);
                });
                $amphur.prop('disabled', false);
            }
        });

        // เลือกอำเภอ
        $amphur.on('change', function () {
            const districtCode = $(this).val();

            $district.html('<option value="">-- เลือกตำบล --</option>').prop('disabled', true);
            $postal.val('');

            if (districtCode) {
                const filteredSub = subdistricts.filter(s => s.districtCode == districtCode);
                filteredSub.forEach(s => {
                    $district.append(
                        `<option value="${s.subdistrictCode}" data-postal="${s.postalCode}">${s.subdistrictNameTh}</option>`
                    );
                });
                $district.prop('disabled', false);
            }
        });

        // เลือกตำบล
        $district.on('change', function () {
            const postal = $(this).find(':selected').data('postal');
            $postal.val(postal || '');
        });
    })
    .catch(err => {
        console.error("❌ โหลดข้อมูลไม่สำเร็จ:", err);
        alert("ไม่สามารถโหลดข้อมูลจังหวัดได้ กรุณารีเฟรชหน้าใหม่");
    });
});
</script>
@endsection
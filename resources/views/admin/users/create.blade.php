@extends('layouts.admin')

@section('title', 'เพิ่มผู้ใช้ใหม่')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">เพิ่มผู้ใช้ใหม่</h3>
        <div class="card-tools">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                               id="username" name="username" value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">อีเมล <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="prefix" class="form-label">คำนำหน้า</label>
                        <select class="form-control @error('prefix') is-invalid @enderror" id="prefix" name="prefix">
                            <option value="">เลือกคำนำหน้า</option>
                            <option value="นาย" {{ old('prefix') == 'นาย' ? 'selected' : '' }}>นาย</option>
                            <option value="นาง" {{ old('prefix') == 'นาง' ? 'selected' : '' }}>นาง</option>
                            <option value="นางสาว" {{ old('prefix') == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                            <option value="ดร." {{ old('prefix') == 'ดร.' ? 'selected' : '' }}>ดร.</option>
                        </select>
                        @error('prefix')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">ชื่อ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('firstname') is-invalid @enderror"
                               id="firstname" name="firstname" value="{{ old('firstname') }}" required>
                        @error('firstname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="mb-3">
                        <label for="lastname" class="form-label">นามสกุล <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('lastname') is-invalid @enderror"
                               id="lastname" name="lastname" value="{{ old('lastname') }}" required>
                        @error('lastname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                               id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="role_id" class="form-label">บทบาท <span class="text-danger">*</span></label>
                        <select class="form-control @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                            <option value="">เลือกบทบาท</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
                                    {{ $role->role_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่</label>
                <textarea class="form-control @error('address') is-invalid @enderror"
                          id="address" name="address" rows="3">{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="province" class="form-label">จังหวัด</label>
                        <input type="text" class="form-control @error('province') is-invalid @enderror"
                               id="province" name="province" value="{{ old('province') }}">
                        @error('province')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="district" class="form-label">อำเภอ</label>
                        <input type="text" class="form-control @error('district') is-invalid @enderror"
                               id="district" name="district" value="{{ old('district') }}">
                        @error('district')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="zipcode" class="form-label">รหัสไปรษณีย์</label>
                        <input type="text" class="form-control @error('zipcode') is-invalid @enderror"
                               id="zipcode" name="zipcode" value="{{ old('zipcode') }}">
                        @error('zipcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>บันทึก
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>ยกเลิก
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
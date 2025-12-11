@extends('layouts.admin')

@section('title', 'แก้ไขผู้ใช้ - ' . ($user->member ? $user->member->first_name . ' ' . $user->member->last_name : $user->username))

@section('content')
<div class="page-container">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
            <div class="flex-grow-1">
                <h1 class="title mb-1">แก้ไขผู้ใช้</h1>
                <p class="subtitle mb-0">
                    แก้ไขข้อมูลของ <strong>{{ $user->member ? $user->member->first_name . ' ' . $user->member->last_name : $user->username }}</strong>
                    <span class="badge bg-secondary ms-2">{{ $user->getDisplayId() }}</span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.show', $user->user_id) }}" class="btn btn-outline-info">
                    <i class="bi bi-eye me-1"></i>ดูข้อมูล
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>กลับไปหน้าหลัก
                </a>
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="progress-section">
        <div class="progress-header">
            <h6 class="mb-2">ความคืบหน้าการกรอกข้อมูล</h6>
            <div class="progress">
                <div class="progress-bar" id="formProgress" role="progressbar" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="content-card">
        <!-- Form Header with Avatar -->
        <div class="form-header">
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar-large">
                    @if($user->member && $user->member->photo_path)
                        <img src="{{ asset('storage/' . $user->member->photo_path) }}" alt="{{ $user->username }}" class="avatar-img">
                    @else
                        <div class="avatar-placeholder-large">
                            {{ strtoupper(substr($user->username, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="mb-1">{{ $user->member ? $user->member->first_name . ' ' . $user->member->last_name : $user->username }}</h3>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                    <div class="d-flex gap-2 mt-2">
                        <span class="badge {{ $user->role_id == 1 ? 'bg-primary' : 'bg-success' }}">{{ $user->role?->role_name ?? 'ปกติ' }}</span>
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $user->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabbed Form -->
        <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST" id="userForm">
            @csrf
            @method('PUT')

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="editTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                        <i class="bi bi-person me-2"></i>ข้อมูลพื้นฐาน
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button" role="tab">
                        <i class="bi bi-shield me-2"></i>ข้อมูลบัญชี
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab">
                        <i class="bi bi-geo-alt me-2"></i>ที่อยู่
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                        <i class="bi bi-lock me-2"></i>ความปลอดภัย
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content p-4" id="editTabsContent">
                <!-- Basic Information Tab -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">ID ผู้ใช้</label>
                            <input type="text" class="form-control" value="{{ $user->getDisplayId() }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">คำนำหน้า</label>
                            <select name="prefix" class="form-select">
                                <option value="">เลือก</option>
                                <option value="นาย" {{ old('prefix', $user->member->prefix ?? '') == 'นาย' ? 'selected' : '' }}>นาย</option>
                                <option value="นาง" {{ old('prefix', $user->member->prefix ?? '') == 'นาง' ? 'selected' : '' }}>นาง</option>
                                <option value="นางสาว" {{ old('prefix', $user->member->prefix ?? '') == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                <option value="ดร." {{ old('prefix', $user->member->prefix ?? '') == 'ดร.' ? 'selected' : '' }}>ดร.</option>
                            </select>
                            @error('prefix')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ชื่อ</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->member->first_name ?? '') }}" required>
                            @error('first_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">นามสกุล</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->member->last_name ?? '') }}" required>
                            @error('last_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                            @error('username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">อีเมล</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Account Information Tab -->
                <div class="tab-pane fade" id="account" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">บทบาท</label>
                            <select name="role_id" class="form-select" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}" {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>ข้อมูลสำคัญ:</strong> การเปลี่ยนบทบาทจะส่งผลต่อสิทธิ์การเข้าถึงของผู้ใช้
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information Tab -->
                <div class="tab-pane fade" id="address" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">ที่อยู่</label>
                            <textarea name="address" class="form-control" rows="3">{{ old('address', $user->address ?? $user->member->address ?? '') }}</textarea>
                            @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">จังหวัด</label>
                            <input type="text" name="province" class="form-control" value="{{ old('province', $user->member->province ?? '') }}">
                            @error('province')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">อำเภอ</label>
                            <input type="text" name="district" class="form-control" value="{{ old('district', $user->member->district ?? '') }}">
                            @error('district')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">รหัสไปรษณีย์</label>
                            <input type="text" name="zipcode" class="form-control" value="{{ old('zipcode', $user->member->postal_code ?? '') }}">
                            @error('zipcode')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">รหัสผ่านใหม่</label>
                            <input type="password" name="password" class="form-control" placeholder="เว้นว่างหากไม่ต้องการเปลี่ยน">
                            <div class="form-text">รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร</div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ยืนยันรหัสผ่าน</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="ยืนยันรหัสผ่านใหม่">
                            @error('password_confirmation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>คำเตือน:</strong> การเปลี่ยนรหัสผ่านจะทำให้ผู้ใช้ต้องเข้าสู่ระบบใหม่
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="security-info">
                                <h6>ข้อมูลความปลอดภัย</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="security-item">
                                            <i class="bi bi-calendar-event me-2"></i>
                                            <span>วันที่สร้าง: {{ $user->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="security-item">
                                            <i class="bi bi-clock me-2"></i>
                                            <span>แก้ไขล่าสุด: {{ $user->updated_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Form Actions -->
            <div class="form-actions">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="auto-save-status">
                        <small id="saveStatus" class="text-muted">
                            <i class="bi bi-cloud-check me-1"></i>บันทึกอัตโนมัติ
                        </small>
                    </div>
                    <div class="action-buttons">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>ยกเลิก
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-1"></i>บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
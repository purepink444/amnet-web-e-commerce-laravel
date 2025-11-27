@extends('layouts.admin')

@section('title', 'รายละเอียดผู้ใช้')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">ข้อมูลผู้ใช้</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>แก้ไข
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>กลับ
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID ผู้ใช้:</label>
                            <p class="form-control-plaintext">{{ $user->user_id }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อผู้ใช้:</label>
                            <p class="form-control-plaintext">{{ $user->username }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">อีเมล:</label>
                            <p class="form-control-plaintext">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">บทบาท:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $user->role_id == 1 ? 'primary' : 'success' }}">
                                    {{ $user->role?->role_name ?? 'N/A' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold">คำนำหน้า:</label>
                            <p class="form-control-plaintext">{{ $user->prefix ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อ:</label>
                            <p class="form-control-plaintext">{{ $user->firstname }}</p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label fw-bold">นามสกุล:</label>
                            <p class="form-control-plaintext">{{ $user->lastname }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">เบอร์โทรศัพท์:</label>
                            <p class="form-control-plaintext">{{ $user->phone ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">วันที่สร้าง:</label>
                            <p class="form-control-plaintext">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                @if($user->address)
                <div class="mb-3">
                    <label class="form-label fw-bold">ที่อยู่:</label>
                    <p class="form-control-plaintext">{{ $user->address }}</p>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">จังหวัด:</label>
                            <p class="form-control-plaintext">{{ $user->province ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">อำเภอ:</label>
                            <p class="form-control-plaintext">{{ $user->district ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">รหัสไปรษณีย์:</label>
                            <p class="form-control-plaintext">{{ $user->zipcode ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">สถิติคำสั่งซื้อ</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">จำนวนคำสั่งซื้อทั้งหมด:</label>
                    <h4 class="text-primary">{{ $user->total_orders ?? 0 }}</h4>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">ยอดรวมการสั่งซื้อ:</label>
                    <h4 class="text-success">฿{{ number_format($user->total_spent ?? 0, 2) }}</h4>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">ค่าเฉลี่ยต่อคำสั่งซื้อ:</label>
                    <h4 class="text-info">฿{{ number_format($user->average_order_value ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">คำสั่งซื้อล่าสุด</h3>
            </div>
            <div class="card-body">
                @if($user->orders && $user->orders->count() > 0)
                    @foreach($user->orders as $order)
                        <div class="mb-2 p-2 border rounded">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">คำสั่งซื้อ #{{ $order->order_id }}</small>
                                <small class="text-primary">฿{{ number_format($order->total_amount, 2) }}</small>
                            </div>
                            <small class="text-muted">{{ $order->created_at->format('d/m/Y') }}</small>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">ไม่มีคำสั่งซื้อ</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold">Dashboard</h1>
            <p class="text-muted">ยินดีต้อนรับ, {{ auth()->user()->username }} (Admin)</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-box-seam text-primary fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">สินค้าทั้งหมด</h6>
                            <h3 class="mb-0">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">เมนูด่วน</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.index'^ }}" class="btn btn-outline-primary">
                            <i class="bi bi-box-seam me-2"></i>จัดการสินค้า
                        </a>
                        <a href="{{ route('admin.orders.index'^ }}" class="btn btn-outline-primary">
                            <i class="bi bi-cart me-2"></i>จัดการคำสั่งซื้อ
                        </a>
                        <a href="{{ route('admin.users.index'^ }}" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i>จัดการผู้ใช้
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

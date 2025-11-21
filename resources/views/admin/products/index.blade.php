@extends('layouts.admin')

@section('title', 'จัดการสินค้า')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
                <div class="flex-grow-1">
                    <h2 class="mb-1">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        จัดการสินค้า
                    </h2>
                    <p class="text-muted mb-0 small">จัดการข้อมูลสินค้าทั้งหมดในระบบ</p>
                </div>
                <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>
                    <span class="d-none d-sm-inline">เพิ่มสินค้า</span>
                    <span class="d-inline d-sm-none">+</span>
                </a>
            </div>

            <!-- Products Table -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-semibold text-dark py-3 px-4">ID</th>
                                    <th class="border-0 fw-semibold text-dark py-3 px-4 d-none d-md-table-cell">รูปภาพ</th>
                                    <th class="border-0 fw-semibold text-dark py-3 px-4">ชื่อสินค้า</th>
                                    <th class="border-0 fw-semibold text-dark py-3 px-4 d-none d-lg-table-cell">หมวดหมู่</th>
                                    <th class="border-0 fw-semibold text-dark py-3 px-4 d-none d-lg-table-cell">แบรนด์</th>
                                    <th class="border-0 fw-semibold text-dark py-3 px-4">ราคา</th>
                                    <th class="border-0 fw-semibold text-dark py-3 px-4 d-none d-sm-table-cell">จำนวน</th>
                                    <th class="border-0 fw-semibold text-dark py-3 px-4">สถานะ</th>
                                    <th class="border-0 fw-semibold text-dark py-3 px-4 text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr class="align-middle">
                                        <td class="py-3 px-4">
                                            <span class="fw-semibold text-primary">#{{ $product->product_id }}</span>
                                        </td>
                                        <td class="py-3 px-4 d-none d-md-table-cell">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}"
                                                     alt="{{ $product->product_name }}"
                                                     class="rounded shadow-sm"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                     style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="fw-semibold text-dark">{{ $product->product_name }}</div>
                                            <div class="small text-muted d-md-none">
                                                หมวด: {{ $product->category?->category_name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 d-none d-lg-table-cell">
                                            {{ $product->category?->category_name ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 d-none d-lg-table-cell">
                                            {{ $product->brand?->brand_name ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="fw-semibold text-success">฿{{ number_format($product->price, 2) }}</span>
                                        </td>
                                        <td class="py-3 px-4 d-none d-sm-table-cell">
                                            <span class="badge bg-info">{{ $product->stock_quantity }}</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                <i class="bi {{ $product->status === 'active' ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                                                {{ $product->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.products.edit', $product->product_id) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="แก้ไข">
                                                    <i class="bi bi-pencil"></i>
                                                    <span class="d-none d-sm-inline ms-1">แก้ไข</span>
                                                </a>
                                                <form action="{{ route('admin.products.destroy', $product->product_id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('ต้องการลบสินค้านี้หรือไม่?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="ลบ">
                                                        <i class="bi bi-trash"></i>
                                                        <span class="d-none d-sm-inline ms-1">ลบ</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox display-4 mb-3"></i>
                                                <h5>ไม่มีสินค้า</h5>
                                                <p class="mb-3">ยังไม่มีสินค้าในระบบ</p>
                                                <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                                                    <i class="bi bi-plus-circle me-2"></i>เพิ่มสินค้าแรก
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.table th {
    border-bottom: 2px solid #dee2e6;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    border: none;
    vertical-align: middle;
}

.table tbody tr:hover {
    background-color: rgba(255, 107, 53, 0.02);
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }

    .btn-group {
        flex-direction: column;
        width: 100%;
    }

    .btn-group .btn {
        margin-right: 0;
        margin-bottom: 2px;
        border-radius: 6px !important;
    }

    .table-responsive {
        border-radius: 8px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
}
</style>
@endsection

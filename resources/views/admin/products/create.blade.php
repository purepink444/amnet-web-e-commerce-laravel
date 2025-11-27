@extends('layouts.admin')

@section('title', 'เพิ่มสินค้า')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <!-- Header -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
                <div class="flex-grow-1">
                    <h2 class="mb-1">
                        <i class="bi bi-plus-circle text-success me-2"></i>
                        เพิ่มสินค้าใหม่
                    </h2>
                    <p class="text-muted mb-0 small">กรุณากรอกข้อมูลสินค้าให้ครบถ้วน</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>
                    <span class="d-none d-sm-inline">กลับ</span>
                    <span class="d-inline d-sm-none">←</span>
                </a>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

                        <!-- ชื่อสินค้า -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                ชื่อสินค้า <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control form-control-lg @error('product_name') is-invalid @enderror"
                                   name="product_name"
                                   value="{{ old('product_name') }}"
                                   required
                                   placeholder="กรุณาป้อนชื่อสินค้า">
                            @error('product_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- หมวดหมู่และแบรนด์ -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    หมวดหมู่ <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">แบรนด์</label>
                                <select class="form-select" name="brand_id">
                                    <option value="">-- ไม่ระบุแบรนด์ --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->brand_id }}" {{ old('brand_id') == $brand->brand_id ? 'selected' : '' }}>
                                            {{ $brand->brand_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- คำอธิบาย -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">คำอธิบาย</label>
                            <textarea class="form-control"
                                      name="description"
                                      rows="4"
                                      placeholder="รายละเอียดสินค้า...">{{ old('description') }}</textarea>
                        </div>

                        <!-- ราคาและจำนวน -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    ราคา (บาท) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">฿</span>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           class="form-control @error('price') is-invalid @enderror"
                                           name="price"
                                           value="{{ old('price') }}"
                                           required
                                           placeholder="0.00">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    จำนวนคงเหลือ <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       min="0"
                                       class="form-control @error('stock_quantity') is-invalid @enderror"
                                       name="stock_quantity"
                                       value="{{ old('stock_quantity') }}"
                                       required
                                       placeholder="0">
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- รูปภาพ -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">URL รูปภาพ</label>
                            <input type="url"
                                   class="form-control"
                                   name="image_url"
                                   value="{{ old('image_url') }}"
                                   placeholder="https://example.com/image.jpg">
                        </div>

                        <!-- สถานะ -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">สถานะ</label>
                            <select class="form-select" name="status">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    ✅ ใช้งาน (แสดงในร้าน)
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    ❌ ปิดใช้งาน (ไม่แสดง)
                                </option>
                            </select>
                        </div>

                        <!-- ปุ่ม -->
                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <button type="submit" class="btn btn-success w-100 btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>บันทึกสินค้า
                                </button>
                            </div>
                            <div class="col-12 col-sm-6">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-x-circle me-2"></i>ยกเลิก
                                </a>
                            </div>
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
/* Custom styles for product create form */
.form-label {
    font-weight: 600 !important;
    color: #2d3748 !important;
    margin-bottom: 0.5rem;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.btn-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 233, 123, 0.3);
}

.form-control:focus,
.form-select:focus {
    border-color: #43e97b;
    box-shadow: 0 0 0 0.2rem rgba(67, 233, 123, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }

    .card-body {
        padding: 1rem !important;
    }

    .btn-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }
}
</style>
@endsection

@extends('layouts.default')

@section('title', 'แก้ไขสินค้า')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-pencil-square text-warning"></i> แก้ไขสินค้า</h2>
                    <p class="text-muted mb-0">ID: #{{ $product->product_id }}</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> กลับ
                </a>
            </div>

            <!-- Form Card -->
            <div class="card shadow">
                <div class="card-body p-4">
                    <form action="{{ route('admin.products.update', $product->product_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- ชื่อสินค้า -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">ชื่อสินค้า <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="product_name" 
                                   class="form-control form-control-lg @error('product_name') is-invalid @enderror" 
                                   value="{{ old('product_name', $product->product_name) }}"
                                   required>
                            @error('product_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- หมวดหมู่และแบรนด์ -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">หมวดหมู่ <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                        <option value="">-- เลือกหมวดหมู่ --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->category_id }}" 
                                                    {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">แบรนด์</label>
                                    <select name="brand_id" class="form-select">
                                        <option value="">-- ไม่ระบุแบรนด์ --</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->brand_id }}" 
                                                    {{ old('brand_id', $product->brand_id) == $brand->brand_id ? 'selected' : '' }}>
                                                {{ $brand->brand_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- คำอธิบาย -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">คำอธิบาย</label>
                            <textarea name="description" 
                                      class="form-control" 
                                      rows="4"
                                      placeholder="รายละเอียดสินค้า...">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <!-- ราคาและจำนวน -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">ราคา (บาท) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">฿</span>
                                        <input type="number" 
                                               step="0.01" 
                                               min="0"
                                               name="price" 
                                               class="form-control @error('price') is-invalid @enderror" 
                                               value="{{ old('price', $product->price) }}"
                                               required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">จำนวนคงเหลือ <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           min="0"
                                           name="stock_quantity" 
                                           class="form-control @error('stock_quantity') is-invalid @enderror" 
                                           value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                           required>
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- รูปภาพ -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">URL รูปภาพ</label>
                            <input type="url" 
                                   name="image_url" 
                                   class="form-control" 
                                   value="{{ old('image_url', $product->image_url) }}"
                                   placeholder="https://example.com/image.jpg">
                            @if($product->image_url)
                            <div class="mt-2">
                                <img src="{{ $product->image_url }}" 
                                     alt="{{ $product->product_name }}" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px;">
                            </div>
                            @endif
                        </div>

                        <!-- สถานะ -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">สถานะ</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>
                                    ✅ ใช้งาน (แสดงในร้าน)
                                </option>
                                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>
                                    ❌ ปิดใช้งาน (ไม่แสดง)
                                </option>
                            </select>
                        </div>

                        <!-- ปุ่ม -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="bi bi-save"></i> บันทึกการแก้ไข
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ข้อมูลเพิ่มเติม -->
            <div class="card mt-3">
                <div class="card-body">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        สร้างเมื่อ: {{ $product->created_at->format('d/m/Y H:i') }} | 
                        แก้ไขล่าสุด: {{ $product->updated_at->format('d/m/Y H:i') }}
                    </small>
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
    border-radius: 15px;
}

.form-label.fw-bold {
    color: #333;
    margin-bottom: 8px;
}

.form-control:focus,
.form-select:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    border: none;
    font-weight: 600;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3);
}
</style>
@endsection
@extends('layouts.admin')

@section('title', 'แก้ไขสินค้า')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <!-- Header -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
                <div class="flex-grow-1">
                    <h2 class="mb-1">
                        <i class="bi bi-pencil-square text-warning me-2"></i>
                        แก้ไขสินค้า
                    </h2>
                    <p class="text-muted mb-0 small">ID: #{{ $product->product_id }}</p>
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
                    <form action="{{ route('admin.products.update', $product->product_id) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- ชื่อสินค้า -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                ชื่อสินค้า <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="product_name"
                                   class="form-control form-control-lg @error('product_name') is-invalid @enderror"
                                   value="{{ old('product_name', $product->product_name) }}"
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
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">แบรนด์</label>
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

                        <!-- คำอธิบาย -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">คำอธิบาย</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="4"
                                      placeholder="รายละเอียดสินค้า...">{{ old('description', $product->description) }}</textarea>
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
                                           name="price"
                                           class="form-control @error('price') is-invalid @enderror"
                                           value="{{ old('price', $product->price) }}"
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
                                       name="stock_quantity"
                                       class="form-control @error('stock_quantity') is-invalid @enderror"
                                       value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                       required
                                       placeholder="0">
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- รูปภาพ -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">รูปภาพสินค้า</label>

                            <!-- Existing Images -->
                            @if($product->images && $product->images->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label text-muted">รูปภาพที่มีอยู่</label>
                                    <div class="row g-2">
                                        @foreach($product->images->sortBy('display_order') as $image)
                                            <div class="col-md-3 col-sm-6">
                                                <div class="position-relative">
                                                    <img src="{{ Storage::url($image->image_path) }}"
                                                         alt="{{ $image->alt_text ?? $product->product_name }}"
                                                         class="img-fluid rounded shadow-sm">
                                                    @if($image->is_primary)
                                                        <span class="badge bg-primary position-absolute top-0 start-0">หลัก</span>
                                                    @endif
                                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                                            onclick="deleteImage({{ $image->image_id }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Upload New Images -->
                            <input type="file"
                                   name="photos[]"
                                   class="form-control"
                                   accept="image/*"
                                   multiple>
                            <div class="form-text">เลือกรูปภาพใหม่เพื่อเพิ่ม รองรับไฟล์ jpeg, png, jpg, gif ขนาดไม่เกิน 2MB ต่อไฟล์ สามารถอัพโหลดได้หลายไฟล์</div>
                            @error('photos')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('photos.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- สถานะ -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">สถานะ</label>
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
                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <button type="submit" class="btn btn-warning w-100 btn-lg">
                                    <i class="bi bi-save me-2"></i>บันทึกการแก้ไข
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

            <!-- ข้อมูลเพิ่มเติม -->
            <div class="card mt-3 border-0 bg-light">
                <div class="card-body py-3">
                    <div class="row text-center text-sm-start">
                        <div class="col-12 col-sm-6">
                            <small class="text-muted">
                                <i class="bi bi-calendar-plus me-1"></i>
                                สร้างเมื่อ: {{ $product->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <div class="col-12 col-sm-6">
                            <small class="text-muted">
                                <i class="bi bi-calendar-check me-1"></i>
                                แก้ไขล่าสุด: {{ $product->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function deleteImage(imageId) {
    if (confirm('คุณต้องการลบรูปภาพนี้หรือไม่?')) {
        fetch(`{{ url('admin/products/images') }}/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated images
                location.reload();
            } else {
                alert(data.message || 'เกิดข้อผิดพลาดในการลบรูปภาพ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการลบรูปภาพ');
        });
    }
}
</script>
@endsection

@section('styles')
<style>
/* Custom styles for product edit form */
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

.btn-warning {
    background: linear-gradient(135deg, #ff6b35 0%, #e85d2a 100%);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #e85d2a 0%, #ff6b35 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.form-control:focus,
.form-select:focus {
    border-color: #ff6b35;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.img-fluid {
    border-radius: 8px;
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

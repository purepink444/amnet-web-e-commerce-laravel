@extends('layouts.admin')

@section('title', 'แก้ไขหมวดหมู่สินค้า')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">แก้ไขหมวดหมู่สินค้า</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Category Name -->
                                <div class="mb-3">
                                    <label for="category_name" class="form-label">
                                        ชื่อหมวดหมู่ <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('category_name') is-invalid @enderror"
                                           id="category_name" name="category_name"
                                           value="{{ old('category_name', $category->category_name) }}" required>
                                    @error('category_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">คำอธิบาย</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status"
                                               id="status_active" value="active" {{ old('status', $category->status) == 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_active">
                                            ใช้งาน
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status"
                                               id="status_inactive" value="inactive" {{ old('status', $category->status) == 'inactive' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_inactive">
                                            ไม่ใช้งาน
                                        </label>
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Current Image -->
                                @if($category->category_image)
                                <div class="mb-3">
                                    <label class="form-label">รูปภาพปัจจุบัน</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $category->category_image) }}"
                                             alt="{{ $category->category_name }}"
                                             class="img-thumbnail mb-2" style="max-width: 200px;">
                                    </div>
                                </div>
                                @endif

                                <!-- Category Image -->
                                <div class="mb-3">
                                    <label for="category_image" class="form-label">
                                        {{ $category->category_image ? 'เปลี่ยน' : 'เพิ่ม' }}รูปภาพหมวดหมู่
                                    </label>
                                    <input type="file" class="form-control @error('category_image') is-invalid @enderror"
                                           id="category_image" name="category_image" accept="image/*">
                                    <div class="form-text">รองรับไฟล์ JPG, PNG, GIF ขนาดไม่เกิน 2MB</div>
                                    @error('category_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="mt-3" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> ยกเลิก
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('category_image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });
});
</script>
@endsection
@extends('layouts.admin')

@section('title', 'แก้ไขแบรนด์สินค้า')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">แก้ไขแบรนด์สินค้า</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="brand_name" class="form-label">ชื่อแบรนด์ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="brand_name" name="brand_name"
                                           value="{{ old('brand_name', $brand->brand_name) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">คำอธิบาย</label>
                                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $brand->description) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="active"
                                               {{ old('status', $brand->status) == 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label">ใช้งาน</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="inactive"
                                               {{ old('status', $brand->status) == 'inactive' ? 'checked' : '' }}>
                                        <label class="form-check-label">ไม่ใช้งาน</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                @if($brand->brand_logo)
                                <div class="mb-3">
                                    <label class="form-label">โลโก้ปัจจุบัน</label>
                                    <img src="{{ asset('storage/' . $brand->brand_logo) }}" alt="{{ $brand->brand_name }}"
                                         class="img-thumbnail mb-2" style="max-width: 200px;">
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label for="brand_logo" class="form-label">{{ $brand->brand_logo ? 'เปลี่ยน' : 'เพิ่ม' }}โลโก้แบรนด์</label>
                                    <input type="file" class="form-control" id="brand_logo" name="brand_logo" accept="image/*">
                                    <div class="form-text">รองรับไฟล์ JPG, PNG, GIF ขนาดไม่เกิน 2MB</div>
                                    <div id="logoPreview" class="mt-3" style="display: none;">
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
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
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
document.getElementById('brand_logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('logoPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
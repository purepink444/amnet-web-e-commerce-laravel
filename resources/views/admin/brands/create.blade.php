@extends('layouts.admin')

@section('title', 'เพิ่มแบรนด์สินค้า')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">เพิ่มแบรนด์สินค้า</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="brand_name" class="form-label">
                                        ชื่อแบรนด์ <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('brand_name') is-invalid @enderror"
                                           id="brand_name" name="brand_name" value="{{ old('brand_name') }}" required>
                                    @error('brand_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">คำอธิบาย</label>
                                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="active" checked>
                                        <label class="form-check-label">ใช้งาน</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="inactive">
                                        <label class="form-check-label">ไม่ใช้งาน</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="brand_logo" class="form-label">โลโก้แบรนด์</label>
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
                            <i class="fas fa-save"></i> บันทึก
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
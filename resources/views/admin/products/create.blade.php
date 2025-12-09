@extends('layouts.admin')

@section('title', 'เพิ่มสินค้า')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-plus-circle text-success me-2"></i>
                        เพิ่มสินค้าใหม่
                    </h2>
                    <p class="text-muted mb-0">กรุณากรอกข้อมูลสินค้าให้ครบถ้วน</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>กลับ
                </a>
            </div>

            <!-- Form Card -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">ข้อมูลสินค้า</h5>
                            </div>
                        </div>

                        <!-- SKU -->
                        <div class="mb-3">
                            <label class="form-label">รหัสสินค้า (SKU) <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('sku') is-invalid @enderror"
                                   name="sku"
                                   value="{{ old('sku') }}"
                                   required
                                   placeholder="กรุณาป้อนรหัสสินค้า เช่น PROD-001"
                                   pattern="[A-Za-z0-9\-_]+"
                                   title="รหัสสินค้าต้องประกอบด้วยตัวอักษร ตัวเลข ขีดกลาง และขีดล่างเท่านั้น">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small class="text-muted">รหัสสินค้าต้องไม่ซ้ำกันและประกอบด้วยตัวอักษร ตัวเลข ขีดกลาง และขีดล่างเท่านั้น</small>
                            </div>
                        </div>

                        <!-- ชื่อสินค้า -->
                        <div class="mb-3">
                            <label class="form-label">ชื่อสินค้า <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('product_name') is-invalid @enderror"
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
                                <label class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
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
                                <label class="form-label">แบรนด์</label>
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
                            <label class="form-label">คำอธิบาย</label>
                            <textarea class="form-control"
                                      name="description"
                                      rows="3"
                                      placeholder="รายละเอียดสินค้า...">{{ old('description') }}</textarea>
                        </div>

                        <!-- ราคาและจำนวน -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">ราคา (บาท) <span class="text-danger">*</span></label>
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
                            <div class="col-12 col-md-6">
                                <label class="form-label">จำนวนคงเหลือ <span class="text-danger">*</span></label>
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
                            <label class="form-label">รูปภาพสินค้า</label>
                            <input type="file"
                                   class="form-control"
                                   name="photos[]"
                                   accept="image/*"
                                   multiple>
                            <div class="form-text">
                                <small class="text-muted">รองรับไฟล์ jpeg, png, jpg, gif ขนาดไม่เกิน 2MB ต่อไฟล์</small>
                            </div>
                            @error('photos')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('photos.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- สถานะ -->
                        <div class="mb-3">
                            <label class="form-label">สถานะการขาย</label>
                            <select class="form-select" name="status">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    เปิดใช้งาน
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    ปิดใช้งาน
                                </option>
                            </select>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>บันทึกสินค้า
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>ยกเลิก
                            </a>
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
/* ===== ENHANCED PRODUCT CREATE FORM STYLES ===== */

/* Form Layout */
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

.card-header {
    border-bottom: 1px solid #dee2e6;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* Tab Navigation */
.nav-tabs .nav-link {
    border: none;
    border-radius: 8px 8px 0 0;
    color: #6c757d;
    font-weight: 500;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    background-color: rgba(255, 107, 53, 0.1);
    color: var(--orange-primary);
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    color: white;
    border: none;
}

.nav-tabs .nav-link.active i {
    color: white;
}

/* Progress Bar */
.progress {
    background-color: #e9ecef;
    border-radius: 2px;
}

.progress-bar {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    transition: width 0.3s ease;
}

/* Upload Area */
.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    padding: 3rem 2rem;
    text-align: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f5 100%);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.upload-area:hover,
.upload-area.dragover {
    border-color: var(--orange-primary);
    background: linear-gradient(135deg, rgba(255, 107, 53, 0.05) 0%, rgba(255, 107, 53, 0.1) 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(255, 107, 53, 0.15);
}

.upload-area.dragover {
    border-color: var(--orange-primary);
    background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(255, 107, 53, 0.15) 100%);
}

.upload-content i {
    transition: transform 0.3s ease;
}

.upload-area:hover .upload-content i {
    transform: scale(1.1);
}

/* Image Preview Grid */
.image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.image-preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.image-preview-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.image-preview-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}

.image-preview-item .image-info {
    padding: 0.5rem;
    background: white;
    border-top: 1px solid #f1f5f9;
}

.image-preview-item .image-name {
    font-size: 0.75rem;
    color: #64748b;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.image-preview-item .image-size {
    font-size: 0.7rem;
    color: #94a3b8;
}

.image-preview-item.primary {
    border: 2px solid var(--orange-primary);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.image-preview-item.primary::after {
    content: '⭐';
    position: absolute;
    top: 5px;
    right: 5px;
    background: var(--orange-primary);
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
}

.image-preview-item .remove-btn {
    position: absolute;
    top: 5px;
    left: 5px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.7rem;
}

.image-preview-item .remove-btn:hover {
    background: #dc2626;
    transform: scale(1.1);
}

/* Form Actions */
.form-actions {
    background: #f8f9fa;
    border-radius: 0 0 12px 12px;
    margin: -1.5rem -1.5rem 0 -1.5rem;
    padding: 1.5rem;
}

.btn-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-success:hover {
    background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 233, 123, 0.3);
}

.btn-success::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-success:hover::before {
    left: 100%;
}

.btn-outline-primary {
    border-color: var(--orange-primary);
    color: var(--orange-primary);
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: var(--orange-primary);
    border-color: var(--orange-primary);
    transform: translateY(-1px);
}

/* Save Status */
#saveStatus {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Enhanced Form Controls */
.form-control:focus,
.form-select:focus {
    border-color: var(--orange-primary);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
}

/* Character Counters */
.form-text {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Tooltips */
.tooltip-inner {
    background-color: var(--orange-primary);
    font-size: 0.75rem;
}

/* Alert Styles */
.alert-info {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%);
    border: 1px solid rgba(13, 110, 253, 0.2);
    border-radius: 8px;
}

/* Loading States */
.loading {
    position: relative;
    pointer-events: none;
    opacity: 0.6;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid var(--orange-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .image-preview-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
    }

    .upload-area {
        padding: 2rem 1rem;
    }

    .form-actions {
        padding: 1rem;
    }

    .nav-tabs .nav-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
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

    .image-preview-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .upload-area {
        padding: 1.5rem 1rem;
    }

    .upload-content h5 {
        font-size: 1.1rem;
    }

    .form-actions .row > div {
        margin-bottom: 0.5rem;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .upload-area {
        background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        border-color: #4a5568;
    }

    .card-header {
        background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        border-color: #4a5568;
    }
}

/* Print styles */
@media print {
    .upload-area,
    .form-actions,
    .nav-tabs {
        display: none !important;
    }
}
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Form elements
    const form = document.getElementById('productForm');
    const progressBar = document.getElementById('formProgress');
    const autoSaveBtn = document.getElementById('autoSaveBtn');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const saveStatus = document.getElementById('saveStatus');
    const saveMessage = document.getElementById('saveMessage');
    const previewBtn = document.getElementById('previewBtn');

    // Character counters
    const nameInput = document.querySelector('input[name="product_name"]');
    const descriptionInput = document.querySelector('textarea[name="description"]');
    const nameCounter = document.getElementById('nameCounter');
    const descriptionCounter = document.getElementById('descriptionCounter');

    // Image upload elements
    const uploadArea = document.getElementById('uploadArea');
    const photosInput = document.getElementById('photosInput');
    const selectFilesBtn = document.getElementById('selectFilesBtn');
    const imagePreview = document.getElementById('imagePreview');

    let uploadedFiles = [];
    let autoSaveTimeout;

    // ===== CHARACTER COUNTERS =====
    function updateCounter(input, counter, max) {
        if (input && counter) {
            const length = input.value.length;
            counter.textContent = `${length}/${max}`;
            counter.className = length > max * 0.9 ? 'text-warning' : length === max ? 'text-danger' : 'text-muted';
        }
    }

    if (nameInput && nameCounter) {
        nameInput.addEventListener('input', () => updateCounter(nameInput, nameCounter, 200));
    }
    if (descriptionInput && descriptionCounter) {
        descriptionInput.addEventListener('input', () => updateCounter(descriptionInput, descriptionCounter, 5000));
    }

    // ===== FORM PROGRESS TRACKING =====
    function updateProgress() {
        const requiredFields = ['sku', 'product_name', 'category_id', 'price', 'stock_quantity'];
        let completed = 0;
        let total = requiredFields.length;

        requiredFields.forEach(field => {
            const element = document.querySelector(`[name="${field}"]`);
            if (element && element.value.trim() !== '') {
                completed++;
            }
        });

        // Check if at least one image is uploaded
        if (uploadedFiles.length > 0) {
            completed++;
            total++;
        }

        const percentage = Math.round((completed / total) * 100);
        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
        }

        // Enable/disable preview button
        if (previewBtn) {
            previewBtn.disabled = completed < total;
        }
    }

    // ===== DRAG & DROP FUNCTIONALITY =====
    if (uploadArea && photosInput) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadArea.classList.add('dragover');
        }

        function unhighlight() {
            uploadArea.classList.remove('dragover');
        }

        uploadArea.addEventListener('drop', handleDrop, false);
        uploadArea.addEventListener('click', () => photosInput.click());
    }

    if (selectFilesBtn && photosInput) {
        selectFilesBtn.addEventListener('click', () => photosInput.click());
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    if (photosInput) {
        photosInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }

    function handleFiles(files) {
        [...files].forEach(uploadFile);
        updateProgress();
    }

    function uploadFile(file) {
        // Validate file
        if (!file.type.startsWith('image/')) {
            showNotification('กรุณาอัพโหลดไฟล์รูปภาพเท่านั้น', 'danger');
            return;
        }

        if (file.size > 2 * 1024 * 1024) { // 2MB
            showNotification('ขนาดไฟล์ต้องไม่เกิน 2MB', 'danger');
            return;
        }

        if (uploadedFiles.length >= 10) {
            showNotification('สามารถอัพโหลดได้สูงสุด 10 รูป', 'warning');
            return;
        }

        uploadedFiles.push(file);
        previewFile(file);
    }

    function previewFile(file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = function() {
            const previewItem = createPreviewItem(reader.result, file);
            imagePreview.appendChild(previewItem);
        };
    }

    function createPreviewItem(src, file) {
        const item = document.createElement('div');
        item.className = 'image-preview-item';

        const img = document.createElement('img');
        img.src = src;
        img.alt = file.name;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '×';
        removeBtn.onclick = () => removeImage(item, file);

        const info = document.createElement('div');
        info.className = 'image-info';

        const name = document.createElement('div');
        name.className = 'image-name';
        name.textContent = file.name;

        const size = document.createElement('div');
        size.className = 'image-size';
        size.textContent = formatFileSize(file.size);

        info.appendChild(name);
        info.appendChild(size);

        item.appendChild(img);
        item.appendChild(removeBtn);
        item.appendChild(info);

        return item;
    }

    function removeImage(item, file) {
        item.remove();
        const index = uploadedFiles.indexOf(file);
        if (index > -1) {
            uploadedFiles.splice(index, 1);
        }
        updateProgress();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // ===== AUTO-SAVE FUNCTIONALITY =====
    function autoSave() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            // Save form data to localStorage
            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                if (key !== 'photos[]') { // Don't save files
                    data[key] = value;
                }
            }

            localStorage.setItem('productDraft', JSON.stringify(data));
            localStorage.setItem('productDraftTime', Date.now());

            // Update UI
            autoSaveBtn.disabled = false;
            autoSaveBtn.innerHTML = '<i class="bi bi-cloud-check me-1"></i><span class="d-none d-sm-inline">บันทึกแล้ว</span><span class="d-inline d-sm-none">✓</span>';

            setTimeout(() => {
                autoSaveBtn.innerHTML = '<i class="bi bi-cloud-upload me-1"></i><span class="d-none d-sm-inline">บันทึกอัตโนมัติ</span><span class="d-inline d-sm-none">💾</span>';
            }, 2000);
        }, 2000);
    }

    // Load auto-saved data
    function loadAutoSave() {
        const saved = localStorage.getItem('productDraft');
        const saveTime = localStorage.getItem('productDraftTime');

        if (saved && saveTime) {
            const data = JSON.parse(saved);
            const timeDiff = Date.now() - parseInt(saveTime);

            // Only load if saved within last 24 hours
            if (timeDiff < 24 * 60 * 60 * 1000) {
                Object.keys(data).forEach(key => {
                    const element = document.querySelector(`[name="${key}"]`);
                    if (element) {
                        element.value = data[key];
                    }
                });

                showNotification('โหลดข้อมูลที่บันทึกไว้แล้ว', 'info');
                updateProgress();
                updateCounter(nameInput, nameCounter, 200);
                updateCounter(descriptionInput, descriptionCounter, 5000);
            }
        }
    }

    // ===== FORM VALIDATION AND SUBMISSION =====
    if (form) {
        form.addEventListener('input', function() {
            updateProgress();
            autoSave();
        });

        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                form.classList.add('was-validated');
                showNotification('กรุณากรอกข้อมูลให้ครบถ้วน', 'warning');
                return;
            }

            // Show loading state
            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            }

            // Clear auto-save data on successful submission
            localStorage.removeItem('productDraft');
            localStorage.removeItem('productDraftTime');
        });
    }

    // ===== UTILITY FUNCTIONS =====
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // ===== KEYBOARD SHORTCUTS =====
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (!submitBtn.disabled) {
                submitBtn.click();
            }
        }

        // Ctrl/Cmd + Shift + P to preview
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'P') {
            e.preventDefault();
            if (!previewBtn.disabled) {
                previewBtn.click();
            }
        }
    });

    // ===== PREVIEW FUNCTIONALITY =====
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            // Basic preview functionality - could be expanded
            if (form) {
                const formData = new FormData(form);
                let preview = '=== ตัวอย่างสินค้า ===\n\n';

                preview += `รหัสสินค้า: ${formData.get('sku') || 'ไม่ได้ระบุ'}\n`;
                preview += `ชื่อสินค้า: ${formData.get('product_name') || 'ไม่ได้ระบุ'}\n`;
                preview += `ราคา: ${formData.get('price') || '0'} บาท\n`;
                preview += `จำนวนคงเหลือ: ${formData.get('stock_quantity') || '0'} ชิ้น\n`;
                preview += `รูปภาพ: ${uploadedFiles.length} รูป\n`;

                alert(preview);
            }
        });
    }

    // ===== INITIALIZATION =====
    loadAutoSave();
    updateProgress();
    updateCounter(nameInput, nameCounter, 200);
    updateCounter(descriptionInput, descriptionCounter, 5000);

});
</script>
@endsection

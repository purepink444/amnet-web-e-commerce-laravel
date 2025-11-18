@extends('layouts.default')

@section('title', 'จัดการสินค้า')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-3">
        <div class="col">
            <h2><i class="bi bi-box-seam"></i> จัดการสินค้า</h2>
        </div>
        <div class="col text-end">
            <button id="modalBtn" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> เพิ่มสินค้า
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- ตารางสินค้า -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="80" class="text-center">ID</th>
                            <th>ชื่อสินค้า</th>
                            <th width="150">แบรนด์</th>
                            <th width="150">หมวดหมู่</th>
                            <th width="120" class="text-end">ราคา</th>
                            <th width="100" class="text-center">คงเหลือ</th>
                            <th width="100" class="text-center">สถานะ</th>
                            <th width="150" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary">#{{ $product->product_id }}</span>
                            </td>
                            <td>
                                <strong>{{ $product->product_name }}</strong>
                                @if($product->description)
                                <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $product->brand->brand_name ?? 'ไม่ระบุ' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $product->category->category_name ?? 'ไม่ระบุ' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <strong class="text-primary">฿{{ number_format($product->price, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                @if($product->stock_quantity > 10)
                                    <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                                @elseif($product->stock_quantity > 0)
                                    <span class="badge bg-warning text-dark">{{ $product->stock_quantity }}</span>
                                @else
                                    <span class="badge bg-danger">หมด</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($product->status == 'active')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> พร้อมจำหน่าย
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle"></i> ไม่จำหน่าย
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
    <div class="btn-group btn-group-sm">
        <!-- ปุ่มแก้ไขเร็ว (Modal) -->
        <button class="btn btn-info" 
                onclick="quickEditProduct({{ $product->product_id }})" 
                title="แก้ไขเร็ว">
            <i class="bi bi-lightning-fill"></i>
        </button>
        
        <!-- ปุ่มแก้ไขแบบเต็ม (หน้าใหม่) -->
        <a href="{{ route('admin.products.edit', $product->product_id) }}" 
           class="btn btn-warning" 
           title="แก้ไขแบบเต็ม">
            <i class="bi bi-pencil-square"></i>
        </a>

        <!-- ปุ่มลบ -->
        <form action="{{ route('admin.products.destroy', $product->product_id) }}" 
              method="POST" 
              style="display:inline;"
              onsubmit="return confirm('ต้องการลบสินค้า {{ $product->product_name }} หรือไม่?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" title="ลบ">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted">ยังไม่มีสินค้าในระบบ</h5>
                                <p class="text-muted">เริ่มต้นเพิ่มสินค้าแรกของคุณ</p>
                                <button id="modalBtn2" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> เพิ่มสินค้าแรก
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    แสดง {{ $products->firstItem() }} ถึง {{ $products->lastItem() }} 
                    จากทั้งหมด {{ $products->total() }} รายการ
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal เพิ่มสินค้า -->
<div id="cartModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>เพิ่มสินค้า</h2>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label class="form-label">ชื่อสินค้า <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="product_name" 
                           class="form-control @error('product_name') is-invalid @enderror" 
                           value="{{ old('product_name') }}"
                           placeholder="กรอกชื่อสินค้า"
                           required>
                    @error('product_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        @foreach(\App\Models\Category::all() as $category)
                            <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">แบรนด์</label>
                    <select name="brand_id" class="form-control">
                        <option value="">-- เลือกแบรนด์ (ถ้ามี) --</option>
                        @foreach(\App\Models\Brand::all() as $brand)
                            <option value="{{ $brand->brand_id }}" {{ old('brand_id') == $brand->brand_id ? 'selected' : '' }}>
                                {{ $brand->brand_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">คำอธิบาย</label>
                    <textarea name="description" 
                              class="form-control" 
                              rows="3"
                              placeholder="รายละเอียดสินค้า...">{{ old('description') }}</textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">ราคา (บาท) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   step="0.01" 
                                   min="0"
                                   name="price" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   value="{{ old('price') }}"
                                   placeholder="0.00"
                                   required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">จำนวนสินค้า <span class="text-danger">*</span></label>
                            <input type="number" 
                                   min="0"
                                   name="stock_quantity" 
                                   class="form-control @error('stock_quantity') is-invalid @enderror" 
                                   value="{{ old('stock_quantity', 0) }}"
                                   placeholder="0"
                                   required>
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">URL รูปภาพ</label>
                    <input type="url" 
                           name="image_url" 
                           class="form-control" 
                           value="{{ old('image_url') }}"
                           placeholder="https://example.com/image.jpg">
                    <small class="text-muted">ใส่ลิงก์รูปภาพสินค้า</small>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">สถานะ</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                            พร้อมจำหน่าย (แสดงในร้าน)
                        </option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                            สินค้าหมดแล้ว (ไม่แสดง)
                        </option>
                    </select>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-save"></i> บันทึกสินค้า
                    </button>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary close">
                <i class="bi bi-x-circle"></i> ปิด
            </button>
        </div>
    </div>
</div>
<!-- Modal แก้ไขเร็ว -->
<div id="quickEditModal" class="modal">
    <div class="modal-content">
        <div class="modal-header bg-warning">
            <h2><i class="bi bi-lightning-fill"></i> แก้ไขเร็ว</h2>
            <span class="close-quick">&times;</span>
        </div>
        <div class="modal-body">
            <form id="quickEditForm">
                @csrf
                
                <input type="hidden" id="quick_product_id">
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">ชื่อสินค้า</label>
                        <input type="text" id="quick_product_name" class="form-control" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">ราคา</label>
                        <input type="number" step="0.01" id="quick_price" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">จำนวน</label>
                        <input type="number" id="quick_stock_quantity" class="form-control" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">สถานะ</label>
                    <select id="quick_status" class="form-select">
                        <option value="active">พร้อมจำหน่าย</option>
                        <option value="inactive">สินค้าหมดแล้ว</option>
                    </select>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> แก้ไขเฉพาะข้อมูลพื้นฐาน ต้องการแก้ไขแบบเต็มคลิก "แก้ไขเพิ่มเติม"
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="bi bi-save"></i> บันทึกเร็ว
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="openFullEdit()">
                        <i class="bi bi-pencil-square"></i> แก้ไขเพิ่มเติม
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* The Modal (background) */
.modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0, 0, 0, 0.7); 
    animation: fadeIn 0.3s;
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: 3% auto; 
    padding: 0;
    border-radius: 15px;
    width: 90%; 
    max-width: 700px;
    box-shadow: 0 10px 50px rgba(0,0,0,0.3);
    animation: slideDown 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

/* Animations */
@keyframes slideDown {
    from {
        transform: translateY(-100px) scale(0.8);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Header */
.modal-header {
    padding: 20px 25px;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.modal-header h2 {
    margin: 0;
    font-size: 1.6rem;
    font-weight: 600;
}

/* Close Button */
.close {
    color: white;
    font-size: 32px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    line-height: 1;
    padding: 0;
    background: none;
    border: none;
}

.close:hover {
    color: #ffd700;
    transform: rotate(90deg) scale(1.2);
}

/* Body */
.modal-body {
    padding: 30px;
    max-height: 65vh;
    overflow-y: auto;
}

.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #28a745;
    border-radius: 10px;
}

/* Footer */
.modal-footer {
    padding: 15px 25px;
    background-color: #f8f9fa;
    text-align: right;
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
    border-top: 1px solid #dee2e6;
}

/* Form Styling */
.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.text-danger {
    color: #dc3545 !important;
}

/* Card */
.card {
    border: none;
    border-radius: 12px;
}

/* Table */
.table thead th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    vertical-align: middle;
}

.table tbody td {
    vertical-align: middle;
}

/* Badges */
.badge {
    padding: 6px 12px;
    font-weight: 500;
}

/* Buttons */
.btn-group-sm .btn {
    padding: 4px 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 5% auto;
    }
    
    .modal-body {
        padding: 20px;
    }
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border: none;
    color: white;
}

.btn-info:hover {
    background: linear-gradient(135deg, #138496 0%, #0f6674 100%);
    color: white;
}

.modal-header.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('cartModal');
    const quickModal = document.getElementById('quickEditModal');
    const btn = document.getElementById('modalBtn');
    const btn2 = document.getElementById('modalBtn2');
    const closeBtns = document.querySelectorAll('.close');
    const closeQuickBtns = document.querySelectorAll('.close-quick');

    // ======== Modal เพิ่มสินค้า ========
    if(btn) {
        btn.addEventListener('click', () => {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    }

    if(btn2) {
        btn2.addEventListener('click', () => {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    }

    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    });

    // ======== Modal แก้ไขเร็ว ========
    closeQuickBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            quickModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    });

    // ปิด modal เมื่อคลิกข้างนอก
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        if (e.target === quickModal) {
            quickModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    // ปิด modal ด้วย ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modal.style.display = 'none';
            quickModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    // Auto-dismiss alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
});

// ======== ฟังก์ชันแก้ไขเร็ว (Modal) ========
function quickEditProduct(id) {
    console.log('🔍 Loading product ID:', id);
    
    const quickModal = document.getElementById('quickEditModal');
    const productNameInput = document.getElementById('quick_product_name');
    
    // แสดง Loading
    quickModal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    productNameInput.value = 'กำลังโหลด...';
    productNameInput.disabled = true;
    
    // ดึงข้อมูลสินค้า
    fetch(`/adm_product/${id}/data`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Product data:', data);
        
        const product = data.product;
        
        // เติมข้อมูลในฟอร์ม
        document.getElementById('quick_product_id').value = product.product_id;
        document.getElementById('quick_product_name').value = product.product_name;
        document.getElementById('quick_product_name').disabled = false;
        document.getElementById('quick_price').value = product.price;
        document.getElementById('quick_stock_quantity').value = product.stock_quantity;
        document.getElementById('quick_status').value = product.status;
        
        // เก็บข้อมูลเดิมใน dataset
        const productIdInput = document.getElementById('quick_product_id');
        productIdInput.dataset.categoryId = product.category_id;
        productIdInput.dataset.brandId = product.brand_id || '';
        productIdInput.dataset.description = product.description || '';
    })
    .catch(error => {
        console.error('❌ Error:', error);
        alert('ไม่สามารถโหลดข้อมูลได้: ' + error.message);
        quickModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    });
}

// ======== ส่งฟอร์มแก้ไขเร็ว ========
document.addEventListener('DOMContentLoaded', function() {
    const quickEditForm = document.getElementById('quickEditForm');
    
    quickEditForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('quick_product_id').value;
        const productData = document.getElementById('quick_product_id').dataset;
        
        const formData = {
            product_name: document.getElementById('quick_product_name').value,
            price: parseFloat(document.getElementById('quick_price').value),
            stock_quantity: parseInt(document.getElementById('quick_stock_quantity').value),
            status: document.getElementById('quick_status').value,
            // ส่งค่าเดิมที่ไม่ได้แก้
            description: productData.description || '',
            category_id: parseInt(productData.categoryId),
            brand_id: productData.brandId ? parseInt(productData.brandId) : null,
        };
        
        console.log('📤 Sending data:', formData);
        
        // Disable submit button
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> กำลังบันทึก...';
        submitBtn.disabled = true;
        
        fetch(`/adm_product/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            console.log('📡 Update response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('✅ Update response:', data);
            
            if (data.success) {
                alert('✅ บันทึกสำเร็จ!');
                location.reload();
            } else {
                throw new Error(data.message || 'เกิดข้อผิดพลาด');
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            alert('❌ เกิดข้อผิดพลาด: ' + error.message);
            
            // Restore button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});

// ======== เปิดหน้าแก้ไขเต็ม ========
function openFullEdit() {
    const id = document.getElementById('quick_product_id').value;
    window.location.href = `/adm_product/${id}/edit`;
}
</script>
@endsection
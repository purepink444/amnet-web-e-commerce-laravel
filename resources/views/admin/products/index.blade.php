@extends('layouts.admin')

@section('title', '')

@section('content')
<!-- Page Header -->
<div class="admin-page-header">
    <h1 class="admin-page-title">จัดการสินค้า</h1>
    <p class="admin-page-subtitle">จัดการสินค้าทั้งหมดในระบบของคุณ</p>
</div>

<!-- Filters and Actions -->
<div class="admin-card admin-mb-4">
    <div class="admin-card-body">
        <div class="admin-d-flex admin-justify-between admin-align-center admin-mb-4">
            <div class="admin-d-flex admin-align-center" style="gap: var(--admin-spacing-lg);">
                <form method="GET" action="{{ route('admin.products.index') }}" class="admin-d-flex admin-align-center" style="gap: var(--admin-spacing-sm); flex: 1; max-width: 400px;">
                    <div class="admin-d-flex" style="flex: 1; position: relative;">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาสินค้า..." class="admin-form-control" style="padding-right: 3rem;">
                        <button type="submit" class="admin-btn admin-btn-primary" style="position: absolute; right: 2px; top: 2px; bottom: 2px; padding: var(--admin-spacing-xs) var(--admin-spacing-sm);">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <select name="category" class="admin-form-select" onchange="this.form.submit()" form="filterForm" style="min-width: 140px;">
                    <option value="">ทุกหมวดหมู่</option>
                    @foreach($categories ?? \App\Models\Category::all() as $category)
                        <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>

                <select name="brand" class="admin-form-select" onchange="this.form.submit()" form="filterForm" style="min-width: 120px;">
                    <option value="">ทุกแบรนด์</option>
                    @foreach($brands ?? \App\Models\Brand::all() as $brand)
                        <option value="{{ $brand->brand_id }}" {{ request('brand') == $brand->brand_id ? 'selected' : '' }}>
                            {{ $brand->brand_name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="admin-form-select" onchange="this.form.submit()" form="filterForm" style="min-width: 120px;">
                    <option value="">ทุกสถานะ</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>มีสินค้า</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ไม่มีสินค้า</option>
                </select>

                <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                    <i class="fas fa-times"></i>
                    <span>ล้างตัวกรอง</span>
                </a>
            </div>

            <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn-primary">
                <i class="fas fa-plus"></i>
                <span>เพิ่มสินค้า</span>
            </a>
        </div>

        <form id="filterForm" method="GET" action="{{ route('admin.products.index') }}" style="display: none;">
            @foreach(request()->all() as $key => $value)
                @if($key !== 'category' && $key !== 'brand' && $key !== 'status')
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
        </form>
    </div>
</div>

<!-- Bulk Actions and Table -->
<div class="admin-card">
    <div class="admin-card-body">
        <!-- Bulk Actions -->
        <form id="bulkForm" method="POST" action="{{ route('admin.products.bulk') }}">
            @csrf
            <div class="admin-d-flex admin-justify-between admin-align-center admin-mb-4">
                <div class="admin-d-flex admin-align-center" style="gap: var(--admin-spacing-sm);">
                    <input type="checkbox" id="selectAll" class="admin-form-control" style="width: auto; margin: 0;">
                    <label for="selectAll" style="margin: 0; cursor: pointer; font-weight: 500;">เลือกทั้งหมด</label>
                    <span id="selectedCount" style="font-size: var(--admin-font-size-sm); color: var(--admin-text-muted);">(0 รายการ)</span>
                </div>
                <div id="bulkButtons" class="admin-d-flex admin-align-center" style="gap: var(--admin-spacing-sm); display: none;">
                    <select name="action" class="admin-form-select" style="min-width: 140px;">
                        <option value="">เลือกการดำเนินการ</option>
                        <option value="activate">เปิดใช้งาน</option>
                        <option value="deactivate">ปิดใช้งาน</option>
                        <option value="delete">ลบ</option>
                    </select>
                    <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">
                        <i class="fas fa-bolt"></i>
                        <span>ดำเนินการ</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Products Table -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="headerCheckbox" class="admin-form-control" style="width: auto; margin: 0;">
                        </th>
                        <th style="width: 80px;">รูปภาพ</th>
                        <th>
                            <a href="{{ route('admin.products.index', array_merge(request()->query(), ['sort' => 'product_id', 'direction' => (request('sort') == 'product_id' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: var(--admin-spacing-xs);">
                                ID
                                @if(request('sort') == 'product_id')
                                    <i class="fas fa-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.products.index', array_merge(request()->query(), ['sort' => 'product_name', 'direction' => (request('sort') == 'product_name' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: var(--admin-spacing-xs);">
                                ชื่อสินค้า
                                @if(request('sort') == 'product_name')
                                    <i class="fas fa-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>หมวดหมู่</th>
                        <th>แบรนด์</th>
                        <th>
                            <a href="{{ route('admin.products.index', array_merge(request()->query(), ['sort' => 'price', 'direction' => (request('sort') == 'price' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: var(--admin-spacing-xs);">
                                ราคา
                                @if(request('sort') == 'price')
                                    <i class="fas fa-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.products.index', array_merge(request()->query(), ['sort' => 'stock_quantity', 'direction' => (request('sort') == 'stock_quantity' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: var(--admin-spacing-xs);">
                                สต็อก
                                @if(request('sort') == 'stock_quantity')
                                    <i class="fas fa-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>สถานะ</th>
                        <th style="width: 120px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <input type="checkbox" name="product_ids[]" value="{{ $product->product_id }}" class="admin-form-control product-checkbox" style="width: auto; margin: 0;" form="bulkForm">
                            </td>
                            <td>
                                @if($product->images->where('is_primary', true)->first())
                                    <img src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->image_path) }}"
                                          alt="{{ $product->product_name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--admin-radius-md); border: 2px solid var(--admin-border-accent);">
                                @else
                                    <div style="width: 50px; height: 50px; background: var(--admin-bg-accent); border-radius: var(--admin-radius-md); display: flex; align-items: center; justify-content: center; color: var(--admin-text-muted);">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td style="font-weight: 600; color: var(--admin-primary);">{{ $product->product_id }}</td>
                            <td>
                                <div style="max-width: 200px;">
                                    <div style="font-weight: 600; color: var(--admin-text-primary); margin-bottom: var(--admin-spacing-xs);">
                                        {{ Str::limit($product->product_name, 50) }}
                                    </div>
                                    @if(strlen($product->product_name) > 50)
                                        <div style="font-size: var(--admin-font-size-sm); color: var(--admin-text-muted);">
                                            {{ Str::limit($product->description ?? '', 100) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $product->category->category_name ?? '-' }}</td>
                            <td>{{ $product->brand->brand_name ?? '-' }}</td>
                            <td style="font-weight: 600; color: var(--admin-success);">{{ number_format($product->price, 2) }} ฿</td>
                            <td>
                                <span class="admin-badge {{ $product->stock_quantity > 0 ? 'admin-badge-success' : 'admin-badge-danger' }}">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge {{ $product->status === 'active' ? 'admin-badge-success' : 'admin-badge-danger' }}">
                                    {{ $product->status === 'active' ? 'มีสินค้า' : 'ไม่มีสินค้า' }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-d-flex" style="gap: var(--admin-spacing-xs);">
                                    <a href="{{ route('admin.products.edit', $product->product_id) }}" class="admin-btn admin-btn-primary admin-btn-sm" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.products.show', $product->product_id) }}" class="admin-btn admin-btn-secondary admin-btn-sm" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" class="d-inline" onsubmit="return confirm('ต้องการลบสินค้านี้หรือไม่?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: var(--admin-spacing-2xl);">
                            <div>
                                <i class="fas fa-inbox" style="font-size: 4rem; color: var(--admin-text-muted); margin-bottom: var(--admin-spacing-lg);"></i>
                                <h3 style="color: var(--admin-text-secondary); margin-bottom: var(--admin-spacing-md);">
                                    @if(request()->hasAny(['search', 'category', 'brand', 'status']))
                                        ไม่พบสินค้าที่ตรงกับเงื่อนไขการค้นหา
                                    @else
                                        ยังไม่มีสินค้าในระบบ
                                    @endif
                                </h3>
                                <p style="color: var(--admin-text-muted); margin-bottom: var(--admin-spacing-lg);">
                                    เริ่มเพิ่มสินค้าแรกของคุณเพื่อเริ่มขาย
                                </p>
                                <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn-primary">
                                    <i class="fas fa-plus"></i>
                                    <span>เพิ่มสินค้าแรก</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="admin-pagination">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @endif
</div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bulk actions functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const headerCheckbox = document.getElementById('headerCheckbox');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const bulkButtons = document.querySelector('.bulk-buttons');
    const bulkActionSelect = document.querySelector('.bulk-action-select');

    // Handle select all checkbox
    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        const totalBoxes = productCheckboxes.length;

        selectAllCheckbox.checked = checkedBoxes.length === totalBoxes && totalBoxes > 0;
        headerCheckbox.checked = selectAllCheckbox.checked;

        // Show/hide bulk actions
        if (checkedBoxes.length > 0) {
            bulkButtons.style.display = 'flex';
        } else {
            bulkButtons.style.display = 'none';
            bulkActionSelect.value = '';
        }
    }

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        headerCheckbox.checked = this.checked;
        updateSelectAllState();
    });

    headerCheckbox.addEventListener('change', function() {
        selectAllCheckbox.checked = this.checked;
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectAllState();
    });

    // Individual checkbox changes
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllState);
    });

    // Bulk action form submission
    const bulkForm = document.getElementById('bulkForm');
    bulkForm.addEventListener('submit', function(e) {
        const selectedProducts = document.querySelectorAll('.product-checkbox:checked');
        if (selectedProducts.length === 0) {
            e.preventDefault();
            alert('กรุณาเลือกสินค้าอย่างน้อยหนึ่งรายการ');
            return;
        }

        if (!bulkActionSelect.value) {
            e.preventDefault();
            alert('กรุณาเลือกการดำเนินการ');
            return;
        }

        if (!confirm(`คุณต้องการ${bulkActionSelect.options[bulkActionSelect.selectedIndex].text} ${selectedProducts.length} รายการหรือไม่?`)) {
            e.preventDefault();
        }
    });

    // Clear filters link
    const clearFiltersLink = document.querySelector('.clear-filters');
    if (clearFiltersLink) {
        clearFiltersLink.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(window.location);
            url.searchParams.delete('search');
            url.searchParams.delete('category');
            url.searchParams.delete('brand');
            url.searchParams.delete('status');
            window.location.href = url.toString();
        });
    }
});
</script>
@endsection

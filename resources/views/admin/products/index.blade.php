@extends('layouts.admin')

@section('title', '')

@section('content')
<div class="page-container">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
            <div class="flex-grow-1">
                <h1 class="title mb-1">จัดการสินค้า</h1>
                <p class="subtitle mb-0">จัดการสินค้าทั้งหมดในระบบ</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="btn-add">
                เพิ่มสินค้า
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.products.index') }}" class="filters-form">
            <div class="filters-row">
                <div class="search-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาสินค้า..." class="search-input">
                    <button type="submit" class="search-btn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                <div class="filter-group">
                    <select name="category" class="filter-select">
                        <option value="">ทุกหมวดหมู่</option>
                        @foreach(\App\Models\Category::all() as $category)
                            <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="brand" class="filter-select">
                        <option value="">ทุกแบรนด์</option>
                        @foreach(\App\Models\Brand::all() as $brand)
                            <option value="{{ $brand->brand_id }}" {{ request('brand') == $brand->brand_id ? 'selected' : '' }}>
                                {{ $brand->brand_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="filter-select">
                        <option value="">ทุกสถานะ</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>มีสินค้า</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ไม่มีสินค้า</option>
                    </select>

                    <a href="{{ route('admin.products.index') }}" class="clear-filters">
                        <i class="bi bi-x-circle"></i> ล้างตัวกรอง
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Content Area -->
    <div class="page-content">
        <!-- Bulk Actions -->
        <form id="bulkForm" method="POST" action="{{ route('admin.products.bulk') }}">
            @csrf
            <div class="bulk-actions">
                <div class="bulk-select">
                    <input type="checkbox" id="selectAll" class="bulk-checkbox">
                    <label for="selectAll">เลือกทั้งหมด</label>
                </div>
                <div class="bulk-buttons" style="display: none;">
                    <select name="action" class="bulk-action-select">
                        <option value="">เลือกการดำเนินการ</option>
                        <option value="activate">เปิดใช้งาน</option>
                        <option value="deactivate">ปิดใช้งาน</option>
                        <option value="delete">ลบ</option>
                    </select>
                    <button type="submit" class="bulk-submit">ดำเนินการ</button>
                </div>
            </div>
        </form>

        <!-- Products Table -->
        <div class="content-card">
            <div class="table-responsive-wrapper">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th class="checkbox-col">
                                <input type="checkbox" id="headerCheckbox" class="bulk-checkbox">
                            </th>
                            <th class="image-col">รูปภาพ</th>
                            <th>
                                <a href="{{ route('admin.products.index', array_merge(request()->query(), ['sort' => 'product_id', 'direction' => (request('sort') == 'product_id' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" class="sort-link">
                                    ID
                                    @if(request('sort') == 'product_id')
                                        <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('admin.products.index', array_merge(request()->query(), ['sort' => 'product_name', 'direction' => (request('sort') == 'product_name' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" class="sort-link">
                                    ชื่อสินค้า
                                    @if(request('sort') == 'product_name')
                                        <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>หมวดหมู่</th>
                            <th>แบรนด์</th>
                            <th>
                                <a href="{{ route('admin.products.index', array_merge(request()->query(), ['sort' => 'price', 'direction' => (request('sort') == 'price' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" class="sort-link">
                                    ราคา
                                    @if(request('sort') == 'price')
                                        <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('admin.products.index', array_merge(request()->query(), ['sort' => 'stock_quantity', 'direction' => (request('sort') == 'stock_quantity' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" class="sort-link">
                                    สต็อก
                                    @if(request('sort') == 'stock_quantity')
                                        <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="checkbox-col">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->product_id }}" class="bulk-checkbox product-checkbox" form="bulkForm">
                                </td>
                                <td class="image-col">
                                    @if($product->images->where('is_primary', true)->first())
                                        <img src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->image_path) }}"
                                             alt="{{ $product->product_name }}" class="product-thumb">
                                    @else
                                        <div class="no-image">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $product->product_id }}</td>
                                <td>
                                    <div class="product-name-cell">
                                        <strong>{{ Str::limit($product->product_name, 50) }}</strong>
                                        @if(strlen($product->product_name) > 50)
                                            <div class="product-description">{{ Str::limit($product->description ?? '', 100) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $product->category->category_name ?? '-' }}</td>
                                <td>{{ $product->brand->brand_name ?? '-' }}</td>
                                <td>{{ number_format($product->price, 2) }} ฿</td>
                                <td>
                                    <span class="stock-badge {{ $product->stock_quantity > 0 ? 'in-stock' : 'out-of-stock' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status {{ $product->status }}">
                                        {{ $product->status === 'active' ? 'มีสินค้า' : 'ไม่มีสินค้า' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn-edit" title="แก้ไข">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin.products.show', $product->product_id) }}" class="btn-view" title="ดูรายละเอียด">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->product_id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('ต้องการลบสินค้านี้หรือไม่?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete" title="ลบ">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="empty-state">
                                    <div class="text-center py-5">
                                        <i class="bi bi-inbox display-4 mb-3 text-muted"></i>
                                        <h5 class="text-muted">ไม่มีสินค้า</h5>
                                        <p class="text-muted mb-3">
                                            @if(request()->hasAny(['search', 'category', 'brand', 'status']))
                                                ไม่พบสินค้าที่ตรงกับเงื่อนไขการค้นหา
                                            @else
                                                ยังไม่มีสินค้าในระบบ
                                            @endif
                                        </p>
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

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="pagination-wrapper">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    /* ===== CONSISTENT PAGE LAYOUT ===== */
    .page-container {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .page-header {
        flex-shrink: 0;
        padding: 1.5rem 0;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 1.5rem;
    }

    .page-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .content-card {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .table-responsive-wrapper {
        flex: 1;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* ===== HEADER STYLES ===== */
    .title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .subtitle {
        font-size: 1.125rem;
        color: #718096;
        margin-bottom: 0;
    }

    .btn-add {
        background: #00d621;
        color: #fff;
        padding: 0.75rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        border: 0;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 214, 33, 0.2);
    }

    .btn-add:hover {
        background: #00b81d;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 214, 33, 0.3);
        color: #fff;
    }

    /* ===== TABLE STYLES ===== */
    .products-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
        margin: 0;
    }

    .products-table thead tr {
        background: linear-gradient(135deg, #ff6b35 0%, #e85d2a 100%);
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .products-table thead th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid rgba(255,255,255,0.2);
    }

    .products-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .products-table tbody tr:hover {
        background-color: rgba(255, 107, 53, 0.02);
    }

    .products-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ===== STATUS AND BUTTONS ===== */
    .status {
        background: #10b981;
        color: white;
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-edit, .btn-delete {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        margin: 0 0.25rem;
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-edit {
        background: #3b82f6;
        color: white;
    }

    .btn-edit:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
    }

    .btn-delete {
        background: #ef4444;
        color: white;
    }

    .btn-delete:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state .display-4 {
        opacity: 0.5;
    }

    /* ===== FILTERS SECTION ===== */
    .filters-section {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }

    .filters-form {
        width: 100%;
    }

    .filters-row {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .search-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 250px;
        flex: 1;
    }

    .search-input {
        flex: 1;
        padding: 0.5rem 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: border-color 0.2s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--orange-primary);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .search-btn {
        padding: 0.5rem 0.75rem;
        background: var(--orange-primary);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .search-btn:hover {
        background: var(--orange-dark);
    }

    .filter-group {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .filter-select {
        padding: 0.5rem 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.9rem;
        min-width: 120px;
        transition: border-color 0.2s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--orange-primary);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .clear-filters {
        color: #64748b;
        text-decoration: none;
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .clear-filters:hover {
        color: var(--orange-primary);
        background: rgba(255, 107, 53, 0.05);
    }

    /* ===== BULK ACTIONS ===== */
    .bulk-actions {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }

    .bulk-select {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #64748b;
    }

    .bulk-checkbox {
        width: 16px;
        height: 16px;
        accent-color: var(--orange-primary);
    }

    .bulk-buttons {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .bulk-action-select {
        padding: 0.375rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 0.85rem;
        min-width: 140px;
    }

    .bulk-submit {
        padding: 0.375rem 0.75rem;
        background: var(--orange-primary);
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .bulk-submit:hover {
        background: var(--orange-dark);
    }

    /* ===== TABLE ENHANCEMENTS ===== */
    .checkbox-col {
        width: 40px;
        text-align: center;
    }

    .image-col {
        width: 80px;
        text-align: center;
    }

    .product-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        border: 2px solid #f1f5f9;
        transition: transform 0.2s ease;
    }

    .product-thumb:hover {
        transform: scale(1.1);
    }

    .no-image {
        width: 50px;
        height: 50px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 1.25rem;
    }

    .sort-link {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-weight: 600;
    }

    .sort-link:hover {
        color: rgba(255,255,255,0.9);
    }

    .product-name-cell {
        max-width: 200px;
    }

    .product-description {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    .stock-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        min-width: 40px;
        display: inline-block;
    }

    .stock-badge.in-stock {
        background: #dcfce7;
        color: #166534;
    }

    .stock-badge.out-of-stock {
        background: #fef2f2;
        color: #dc2626;
    }

    .status.active {
        background: #dcfce7;
        color: #166534;
    }

    .status.inactive {
        background: #fef2f2;
        color: #dc2626;
    }

    .action-buttons {
        display: flex;
        gap: 0.375rem;
        justify-content: center;
    }

    .btn-view {
        padding: 0.5rem;
        background: #0ea5e9;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .btn-view:hover {
        background: #0284c7;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(14, 165, 233, 0.3);
    }

    /* ===== PAGINATION ===== */
    .pagination-wrapper {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper .pagination {
        margin: 0;
    }

    .pagination-wrapper .page-link {
        color: var(--orange-primary);
        border-color: #dee2e6;
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 6px !important;
        transition: all 0.2s ease;
    }

    .pagination-wrapper .page-link:hover {
        color: var(--orange-dark);
        background: rgba(255, 107, 53, 0.05);
        border-color: var(--orange-primary);
    }

    .pagination-wrapper .page-item.active .page-link {
        background: var(--orange-primary);
        border-color: var(--orange-primary);
        color: white;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
        .filters-row {
            flex-direction: column;
            align-items: stretch;
        }

        .search-group {
            min-width: auto;
        }

        .filter-group {
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .filter-select {
            min-width: 100px;
        }
    }

    @media (max-width: 768px) {
        .filters-section {
            padding: 1rem;
        }

        .filters-row {
            gap: 0.75rem;
        }

        .search-group {
            flex-direction: column;
            gap: 0.5rem;
        }

        .search-input {
            width: 100%;
        }

        .filter-group {
            flex-direction: column;
            gap: 0.5rem;
            align-items: stretch;
        }

        .filter-select {
            width: 100%;
            min-width: auto;
        }

        .clear-filters {
            justify-content: center;
        }

        .bulk-actions {
            padding: 1rem;
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .bulk-buttons {
            justify-content: center;
        }

        .title {
            font-size: 2rem;
        }

        .subtitle {
            font-size: 1rem;
        }

        .btn-add {
            width: 100%;
            justify-content: center;
            margin-top: 1rem;
        }

        .products-table {
            font-size: 0.875rem;
        }

        .products-table thead th,
        .products-table tbody td {
            padding: 0.75rem 0.5rem;
        }

        .products-table thead th:not(.checkbox-col):not(.image-col) {
            display: none;
        }

        .products-table tbody td:not(.checkbox-col):not(.image-col) {
            display: none;
        }

        .products-table tbody td:nth-child(3),
        .products-table tbody td:nth-child(4),
        .products-table tbody td:nth-child(10) {
            display: table-cell;
        }

        .products-table thead th:nth-child(3),
        .products-table thead th:nth-child(4),
        .products-table thead th:nth-child(10) {
            display: table-cell;
        }

        .btn-edit, .btn-delete, .btn-view {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
            margin: 0.125rem;
            justify-content: center;
        }

        .action-buttons {
            flex-direction: column;
            gap: 0.25rem;
        }

        .product-name-cell {
            max-width: none;
        }

        .product-description {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .page-header {
            padding: 1rem 0;
        }

        .title {
            font-size: 1.75rem;
        }

        .filters-section {
            padding: 0.75rem;
        }

        .bulk-actions {
            padding: 0.75rem;
        }

        .products-table {
            font-size: 0.8rem;
        }

        .products-table thead th,
        .products-table tbody td {
            padding: 0.5rem 0.25rem;
        }

        .btn-edit, .btn-delete, .btn-view {
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
        }

        .product-thumb, .no-image {
            width: 40px;
            height: 40px;
        }
    }
</style>

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

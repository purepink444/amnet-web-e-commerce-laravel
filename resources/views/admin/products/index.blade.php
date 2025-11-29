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

    <!-- Content Area -->
    <div class="page-content">
        <!-- Products Table -->
        <div class="content-card">
            <div class="table-responsive-wrapper">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อสินค้า</th>
                            <th>ราคา</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->product_id }}</td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td>
                                    <span class="status">
                                        {{ $product->status === 'active' ? 'มีสินค้า' : 'ไม่มีสินค้า' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn-edit">
                                        แก้ไขสินค้า
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->product_id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('ต้องการลบสินค้านี้หรือไม่?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">
                                            ลบสินค้า
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="text-center py-5">
                                        <i class="bi bi-inbox display-4 mb-3 text-muted"></i>
                                        <h5 class="text-muted">ไม่มีสินค้า</h5>
                                        <p class="text-muted mb-3">ยังไม่มีสินค้าในระบบ</p>
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

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
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

        .btn-edit, .btn-delete {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
            margin: 0.125rem;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .page-header {
            padding: 1rem 0;
        }

        .title {
            font-size: 1.75rem;
        }

        .products-table {
            font-size: 0.8rem;
        }

        .products-table thead th,
        .products-table tbody td {
            padding: 0.5rem 0.25rem;
        }

        .btn-edit, .btn-delete {
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>
@endsection

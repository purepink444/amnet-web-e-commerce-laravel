@extends('layouts.default')

@section('title', 'สินค้าที่ชอบ')

@section('content')

<style>
    /* ===== Wireframe-like Style (เหมือน orders.blade.php) ===== */
    .wf-sidebar-card {
        background: #e6e6e6;
        border: none;
        border-radius: 10px;
        padding: 14px;
    }

    .wf-sidebar-item {
        background: #f7f7f7;
        border-radius: 6px;
        padding: 10px 12px;
        margin-bottom: 10px;
        font-size: 14px;
        display: flex;
        align-items: center;
        color: #333;
        text-decoration: none;
    }
    .wf-sidebar-item.active {
        background: #ffffff;
        border: 2px solid #cfcfcf;
    }

    .wf-main-header {
        background: #f7f7f7;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .wf-main-panel {
        background: #dcdcdc;
        border-radius: 10px;
        padding: 20px;
        min-height: 450px;
    }

    .wf-separator {
        height: 40px;
        background: #0b0b0b;
        border-radius: 4px;
        margin: 40px 0;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .product-item {
        background: white;
        border-radius: 8px;
        padding: 15px;
        border: 1px solid #ddd;
    }

    .product-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .product-item h6 {
        font-size: 14px;
        margin-bottom: 8px;
    }

    .product-item .price {
        font-weight: bold;
        color: #ff6b35;
        margin-bottom: 10px;
    }

    .btn-small {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 4px;
    }
</style>


<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">

            <!-- Layout Row -->
            <div class="row g-4">

                <!-- Sidebar (เหมือน wireframe) -->
                <div class="col-lg-3">
                    <div class="wf-sidebar-card">

                        <a href="{{ route('account.profile') }}"
                           class="wf-sidebar-item {{ request()->routeIs('account.profile') ? 'active' : '' }}">
                            <i class="bi bi-person me-2"></i> โปรไฟล์
                        </a>

                        <a href="{{ route('account.orders.index') }}"
                           class="wf-sidebar-item {{ request()->routeIs('account.orders.index') ? 'active' : '' }}">
                            <i class="bi bi-bag-check me-2"></i> คำสั่งซื้อ
                        </a>

                        <a href="{{ route('account.wishlist') }}"
                           class="wf-sidebar-item {{ request()->routeIs('account.wishlist') ? 'active' : '' }}">
                            <i class="bi bi-heart me-2"></i> สินค้าที่ชอบ
                        </a>

                        <a href="{{ route('account.settings') }}"
                           class="wf-sidebar-item {{ request()->routeIs('account.settings') ? 'active' : '' }}">
                            <i class="bi bi-gear me-2"></i> ตั้งค่า
                        </a>

                    </div>
                </div>

                <!-- Main Panel -->
                <div class="col-lg-9">

                    <div class="wf-main-header">
                        รายการสินค้าที่ชอบ
                    </div>

                    <div class="wf-main-panel">

                        @if($wishlist->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-heart display-1 text-muted mb-3"></i>
                                <h5 class="text-muted">ยังไม่มีสินค้าในรายการโปรด</h5>
                                <p class="text-muted mb-4">เริ่มเพิ่มสินค้าที่คุณชอบไว้ในรายการนี้</p>
                                <a href="{{ url('/product') }}" class="btn btn-primary">
                                    <i class="bi bi-shop me-2"></i>เลือกซื้อสินค้า
                                </a>
                            </div>
                        @else
                            <div class="product-grid">
                                @foreach($wishlist as $item)
                                    <div class="product-item">
                                        <img src="{{ $item->product->image ?? 'https://via.placeholder.com/300x200' }}"
                                             alt="{{ $item->product->name }}">
                                        <h6>{{ $item->product->name }}</h6>
                                        <div class="price">฿{{ number_format($item->product->price, 2) }}</div>
                                        <div class="d-flex gap-2">
                                            @if($item->product->stock > 0)
                                                <form action="{{ route('account.cart.add', $item->product_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-small">
                                                        <i class="bi bi-cart-plus me-1"></i>เพิ่ม
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('account.wishlist.toggle', $item->product_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-small"
                                                        onclick="return confirm('ต้องการลบสินค้านี้ออกจากรายการโปรดหรือไม่?')">
                                                    <i class="bi bi-trash me-1"></i>ลบ
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $wishlist->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Black separator (เหมือนภาพ) -->
            <div class="wf-separator"></div>

        </div>
    </div>
</div>

@endsection

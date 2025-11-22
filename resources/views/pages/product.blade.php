@extends('layouts.default')

@section('title', 'สินค้า')

@section('content')
<!-- Hero Section -->
<div class="product-hero">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold text-white mb-3">
                    <i class="bi bi-bag-check me-3"></i>สินค้าของเรา
                </h1>
                <p class="lead text-white-50">
                    เลือกซื้อสินค้าคุณภาพ พร้อมบริการหลังการขายที่ดีที่สุด
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="search-box">
                    <input type="text" class="form-control form-control-lg" placeholder="🔍 ค้นหาสินค้า...">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <!-- Category Dropdown -->
    <div class="row mb-4">
        <div class="col-lg-3 mb-3">
            <div class="category-card">
                <div class="dropdown">
                    <button class="btn btn-category w-100 dropdown-toggle" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid-3x3-gap me-2"></i>หมวดหมู่สินค้า
                    </button>
                    <ul class="dropdown-menu w-100 shadow-lg" aria-labelledby="categoryDropdown">
                        <li><h6 class="dropdown-header"><i class="bi bi-camera-video text-primary"></i> ระบบกล้อง</h6></li>
                        <li><a class="dropdown-item" href="/category/ipcam"><i class="bi bi-camera me-2"></i>กล้องวงจรปิด (IP Camera)</a></li>
                        <li><a class="dropdown-item" href="/category/ptzcam"><i class="bi bi-arrow-left-right me-2"></i>PTZ Camera</a></li>
                        <li><a class="dropdown-item" href="/category/ai-smartcam"><i class="bi bi-robot me-2"></i>AI/Smart Camera</a></li>
                        <li><a class="dropdown-item" href="/category/nvrdvr"><i class="bi bi-hdd-rack me-2"></i>เครื่องบันทึกภาพ (NVR/DVR)</a></li>
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        <li><h6 class="dropdown-header"><i class="bi bi-router text-success"></i> อุปกรณ์เครือข่าย</h6></li>
                        <li><a class="dropdown-item" href="/category/switches"><i class="bi bi-diagram-3 me-2"></i>Switches</a></li>
                        <li><a class="dropdown-item" href="/category/router"><i class="bi bi-wifi me-2"></i>Router</a></li>
                        <li><a class="dropdown-item" href="/category/wireless"><i class="bi bi-broadcast me-2"></i>Wireless/Access Point</a></li>
                        <li><a class="dropdown-item" href="/category/firewall"><i class="bi bi-shield-check me-2"></i>Firewall</a></li>
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        <li><h6 class="dropdown-header"><i class="bi bi-cpu text-warning"></i> IoT & AI</h6></li>
                        <li><a class="dropdown-item" href="/category/iot"><i class="bi bi-boxes me-2"></i>AIoT All-in-one</a></li>
                        <li><a class="dropdown-item" href="/category/face"><i class="bi bi-person-badge me-2"></i>Face Recognition</a></li>
                        <li><a class="dropdown-item" href="/category/ai"><i class="bi bi-box me-2"></i>AI BOX</a></li>
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        <li><h6 class="dropdown-header"><i class="bi bi-tools text-secondary"></i> อื่นๆ</h6></li>
                        <li><a class="dropdown-item" href="/category/vms"><i class="bi bi-display me-2"></i>VMS</a></li>
                        <li><a class="dropdown-item" href="/category/accessories"><i class="bi bi-plug me-2"></i>อุปกรณ์เสริม</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Filter & Sort -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center filter-bar">
                <div class="filter-info">
                    <span class="badge bg-orange">{{ $products->count() }}</span>
                    <span class="ms-2 text-muted">รายการสินค้า</span>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>เรียงตาม: แนะนำ</option>
                        <option>ราคา: ต่ำ-สูง</option>
                        <option>ราคา: สูง-ต่ำ</option>
                        <option>ชื่อ: A-Z</option>
                    </select>
                    <button class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-grid-3x3"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        @foreach($products as $product)
            @php
                $imageUrl = $product->image_url;
                if (!preg_match('/^https?:\/\//', $imageUrl)) {
                    $imageUrl = asset('storage/' . $imageUrl);
                }
            @endphp

            <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
                <div class="product-card">
                    @if($product->stock_quantity <= 0)
                        <div class="stock-badge out-of-stock">
                            <span class="badge bg-danger">สินค้าหมด</span>
                        </div>
                    @elseif($product->stock_quantity <= 5)
                        <div class="stock-badge low-stock">
                            <span class="badge bg-warning text-dark">เหลือน้อย</span>
                        </div>
                    @endif

                    <div class="product-image">
                        <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}" onerror="this.src='https://via.placeholder.com/300x300?text=No+Image';">
                        <div class="product-overlay">
                            <a href="{{ route('client.products.show', $product->getKey()) }}" class="btn btn-white btn-sm">
                                <i class="bi bi-eye me-1"></i>ดูรายละเอียด
                            </a>
                        </div>
                    </div>

                    <div class="product-body">
                        <h5 class="product-title">{{ \Illuminate\Support\Str::limit($product->product_name, 45) }}</h5>
                        <p class="product-description">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>

                        <div class="product-meta">
                            <small class="text-muted">
                                <i class="bi bi-box-seam me-1"></i>เหลือ {{ $product->stock_quantity }} ชิ้น
                            </small>
                        </div>

                        <div class="product-footer">
                            <div class="product-price">
                                <span class="price">฿{{ number_format($product->price, 2) }}</span>
                            </div>
                            <div class="product-actions">
                                @if($product->stock_quantity > 0)
                                    <a href="{{ route('client.products.show', $product->getKey()) }}" class="btn btn-green btn-sm">
                                        <i class="bi bi-bag-check me-1"></i>ซื้อสินค้า
                                    </a>
                                    <button class="btn btn-orange btn-sm add-to-cart" onclick="addToCart({{ $product->getKey() }})">
                                        <i class="bi bi-cart-plus me-1"></i>เพิ่มสินค้า
                                    </button>
                                @else
                                    <button class="btn btn-secondary btn-sm w-100" disabled>
                                        <i class="bi bi-x-circle me-1"></i>สินค้าหมด
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($products->isEmpty())
        <div class="empty-state">
            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
            <h4 class="text-muted">ไม่พบสินค้าในขณะนี้</h4>
            <p class="text-muted">กรุณาลองค้นหาด้วยคำอื่น หรือกลับมาใหม่ภายหลัง</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function addToCart(productId) {
    // Basic add to cart functionality - you can enhance this
    fetch('/account/cart/add/' + productId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart counter
            const counter = document.getElementById('cart-counter');
            if (counter) {
                counter.textContent = data.cart_count || '0';
            }
            // Show success message
            alert('เพิ่มสินค้าในตะกร้าแล้ว!');
        } else {
            alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถเพิ่มสินค้าได้'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
    });
}
</script>
@endsection

@section('styles')
<style>
:root {
    --orange-primary: #ff6b35;
    --orange-dark: #e85d2a;
    --black-primary: #1a1a1a;
    --black-secondary: #2d2d2d;
}

/* Hero Section */
.product-hero {
    background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 50%, var(--orange-dark) 100%);
    position: relative;
    overflow: hidden;
}

.product-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff6b35' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.search-box input {
    border-radius: 50px;
    padding: 0.8rem 1.5rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.9);
}

.search-box input:focus {
    border-color: var(--orange-primary);
    box-shadow: 0 0 0 0.25rem rgba(255, 107, 53, 0.25);
}

/* Category Card */
.category-card {
    background: white;
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-category {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    color: white;
    border: none;
    padding: 0.8rem 1.2rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-category:hover {
    background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-primary) 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
    color: white;
}

.dropdown-menu {
    border: none;
    border-radius: 12px;
    padding: 0.5rem;
}

.dropdown-header {
    font-weight: 700;
    padding: 0.8rem 1rem;
    font-size: 0.9rem;
}

.dropdown-item {
    padding: 0.6rem 1rem;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #fff3f0 0%, #ffe8e0 100%);
    padding-left: 1.5rem;
}

/* Filter Bar */
.filter-bar {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.bg-orange {
    background: var(--orange-primary) !important;
}

/* Product Card */
.product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 25px rgba(255, 107, 53, 0.2);
}

.stock-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}

.product-image {
    position: relative;
    width: 100%;
    height: 250px;
    overflow: hidden;
    background: #f8f9fa;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.1);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(26, 26, 26, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.btn-white {
    background: white;
    color: var(--orange-primary);
    border: none;
    font-weight: 600;
}

.btn-white:hover {
    background: var(--orange-primary);
    color: white;
}

.product-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--black-primary);
    margin-bottom: 0.5rem;
    height: 2.4em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-description {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 0.8rem;
    flex: 1;
}

.product-meta {
    margin-bottom: 1rem;
    padding: 0.4rem 0;
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
}

.product-meta small {
    font-size: 0.8rem;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.2rem;
    border-top: 1px solid #e9ecef;
    margin-top: auto;
}

.product-actions {
    display: flex;
    gap: 0.8rem;
}

.product-price .price {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--orange-primary);
}

.btn-orange {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 0.6rem;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.btn-orange:hover {
    background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-primary) 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 10px rgba(255, 107, 53, 0.3);
    color: white;
}

.btn-green {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 0.6rem;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-green:hover {
    background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 10px rgba(40, 167, 69, 0.3);
    color: white;
    text-decoration: none;
}

.add-to-cart {
    font-size: 1.1rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .product-hero h1 {
        font-size: 2rem;
    }
    
    .filter-bar {ฤ
        flex-direction: column;
        gap: 1rem;
    }
    
    .product-image {
        height: 200px;
    }
}
</style>
@endsection

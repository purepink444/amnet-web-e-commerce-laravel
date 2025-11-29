@extends('layouts.default')

@section('title', $product->product_name)

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">หน้าหลัก</a></li>
                <li class="breadcrumb-item"><a href="{{ route('client.products.index') }}">สินค้า</a></li>
                <li class="breadcrumb-item active">{{ $product->product_name }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Product Image -->
            <div class="col-lg-6">
                <div class="product-image-wrapper sticky-top" style="top: 100px;">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="position-relative">
                            <!-- Status Badge -->
                            @if($product->stock_quantity > 0)
                                <span class="badge bg-success position-absolute top-0 start-0 m-3 px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i>พร้อมส่ง
                                </span>
                            @else
                                <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i>สินค้าหมด
                                </span>
                            @endif

                            <!-- Main Image -->
                            <img src="{{ $product->image_url ?: 'https://via.placeholder.com/600x500' }}" 
                                 alt="{{ $product->product_name }}" 
                                 class="img-fluid w-100 product-main-image"
                                 onerror="this.src='https://via.placeholder.com/600x500'">
                            
                            <!-- Zoom Icon -->
                            <button class="btn btn-light btn-floating position-absolute bottom-0 end-0 m-3" 
                                    onclick="zoomImage()">
                                <i class="bi bi-zoom-in"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Thumbnail Gallery -->
                    <div class="row g-2 mt-3">
                        <div class="col-3">
                            <div class="card border-2 border-primary rounded-3 overflow-hidden">
                                <img src="{{ $product->image_url ?: 'https://via.placeholder.com/150' }}" 
                                     class="img-fluid" alt="Thumbnail 1">
                            </div>
                        </div>
                        @for($i = 0; $i < 3; $i++)
                        <div class="col-3">
                            <div class="card border rounded-3 overflow-hidden opacity-50">
                                <img src="https://via.placeholder.com/150" class="img-fluid" alt="Thumbnail {{ $i+2 }}">
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <!-- Brand -->
                    @if($product->brand)
                    <div class="mb-3">
                        <span class="badge bg-light text-dark px-3 py-2">
                            <i class="bi bi-award me-1"></i>{{ $product->brand->brand_name }}
                        </span>
                    </div>
                    @endif

                    <!-- Product Name -->
                    <h1 class="display-6 fw-bold mb-3">{{ $product->product_name }}</h1>

                    <!-- Rating -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="text-warning me-2">
                            {!! $product->rating_stars !!}
                        </div>
                        <span class="text-muted">
                            ({{ number_format($product->average_rating, 1) }}/5 จาก {{ $product->total_reviews }} รีวิว)
                        </span>
                        @if($product->hasReviews())
                            <a href="{{ route('products.reviews.index', $product->product_id) }}" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="bi bi-chat-dots me-1"></i>ดูรีวิวทั้งหมด
                            </a>
                        @endif
                    </div>

                    <!-- Rating Distribution -->
                    @if($product->hasReviews())
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-3">การให้คะแนน</h6>
                        <div class="row g-2">
                            @foreach($product->rating_distribution as $stars => $count)
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-2" style="min-width: 30px;">{{ $stars }}★</span>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-warning"
                                             style="width: {{ $product->total_reviews > 0 ? ($count / $product->total_reviews) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                    <span class="text-muted ms-2" style="min-width: 30px;">{{ $count }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Price -->
                    <div class="price-section mb-4">
                        <div class="d-flex align-items-baseline gap-3">
                            <h2 class="text-danger fw-bold mb-0">
                                ฿{{ number_format($product->price, 2) }}
                            </h2>
                            <span class="text-muted text-decoration-line-through">
                                ฿{{ number_format($product->price * 1.2, 2) }}
                            </span>
                            <span class="badge bg-danger">-20%</span>
                        </div>
                        <small class="text-success">
                            <i class="bi bi-truck me-1"></i>ส่งฟรี! สำหรับคำสั่งซื้อเกิน ฿2,000
                        </small>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-3">
                            <i class="bi bi-info-circle text-primary me-2"></i>รายละเอียดสินค้า
                        </h5>
                        <p class="text-muted">{{ $product->description ?: 'ไม่มีรายละเอียด' }}</p>
                    </div>

                    <!-- Specifications -->
                    @if($product->specifications)
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-3">
                            <i class="bi bi-gear text-primary me-2"></i>คุณสมบัติ
                        </h5>
                        <div class="card bg-light border-0 p-3">
                            <ul class="list-unstyled mb-0">
                                @foreach(json_decode($product->specifications, true) ?? [] as $key => $value)
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    <strong>{{ $key }}:</strong> {{ $value }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    <!-- Reviews Section -->
                    @if($product->hasReviews())
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-3">
                            <i class="bi bi-chat-dots text-primary me-2"></i>รีวิวล่าสุด
                        </h5>
                        <div class="card bg-light border-0 p-3">
                            @foreach($product->getLatestReviews(3) as $review)
                            <div class="d-flex mb-3 pb-3 border-bottom">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; font-weight: bold;">
                                        {{ substr($review->member->first_name, 0, 1) . substr($review->member->last_name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="me-2">{{ $review->member->full_name }}</strong>
                                        <div class="text-warning">
                                            {!! $review->rating_stars !!}
                                        </div>
                                        <small class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">{{ Str::limit($review->comment, 150) }}</p>
                                </div>
                            </div>
                            @endforeach
                            <div class="text-center">
                                <a href="{{ route('products.reviews.index', $product->product_id) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>ดูรีวิวทั้งหมด ({{ $product->total_reviews }})
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Write Review Button -->
                    @auth
                        @php
                            $member = auth()->user()->member;
                            $canReview = $member && $member->canReviewProduct($product->product_id) && !$member->hasReviewedProduct($product->product_id);
                        @endphp
                        @if($canReview)
                        <div class="mb-4">
                            <a href="{{ route('products.reviews.create', $product->product_id) }}" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-pencil-square me-2"></i>เขียนรีวิวสำหรับสินค้านี้
                            </a>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i>คุณสามารถเขียนรีวิวได้หลังจากได้รับสินค้าแล้ว
                            </small>
                        </div>
                        @elseif($member && $member->hasReviewedProduct($product->product_id))
                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="bi bi-check-circle me-2"></i>คุณได้เขียนรีวิวสำหรับสินค้านี้แล้ว
                                <a href="{{ route('products.reviews.index', $product->product_id) }}" class="alert-link">ดูรีวิวของคุณ</a>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="mb-4">
                            <button class="btn btn-outline-success btn-lg w-100" onclick="showLoginModal()">
                                <i class="bi bi-pencil-square me-2"></i>เข้าสู่ระบบเพื่อเขียนรีวิว
                            </button>
                        </div>
                    @endauth

                    <!-- Quantity Selector -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-3">จำนวน</label>
                        <div class="input-group input-group-lg" style="max-width: 200px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="form-control text-center" value="1" min="1" id="quantity">
                            <button class="btn btn-outline-secondary" type="button" onclick="increaseQty()">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-box-seam me-1"></i>มีสินค้าเหลือ {{ $product->stock_quantity }} ชิ้น
                        </small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        @auth
                            <button class="btn btn-primary btn-lg py-3 fw-semibold" onclick="addToCart({{ $product->product_id }})">
                                <i class="bi bi-cart-plus me-2"></i>เพิ่มลงตะกร้า
                            </button>
                            <a href="{{ route('account.checkout.index') }}" class="btn btn-outline-danger btn-lg py-3 fw-semibold">
                                <i class="bi bi-lightning-fill me-2"></i>ซื้อเลย
                            </a>
                            <button class="btn {{ $isInWishlist ? 'btn-danger' : 'btn-outline-secondary' }}"
                                    onclick="addToWishlist({{ $product->product_id }})">
                                <i class="bi {{ $isInWishlist ? 'bi-heart-fill text-white' : 'bi-heart' }} me-2"></i>
                                {{ $isInWishlist ? 'อยู่ในรายการโปรด' : 'เพิ่มในรายการโปรด' }}
                            </button>
                        @else
                            <button class="btn btn-primary btn-lg py-3 fw-semibold" onclick="showLoginModal()">
                                <i class="bi bi-cart-plus me-2"></i>เพิ่มลงตะกร้า
                            </button>
                            <button class="btn btn-outline-danger btn-lg py-3 fw-semibold" onclick="showLoginModal()">
                                <i class="bi bi-lightning-fill me-2"></i>ซื้อเลย
                            </button>
                            <button class="btn btn-outline-secondary" onclick="showLoginModal()">
                                <i class="bi bi-heart me-2"></i>เพิ่มในรายการโปรด
                            </button>
                        @endauth
                    </div>

                    <!-- Features -->
                    <div class="row g-3 mt-3">
                        <div class="col-6">
                            <div class="card border-0 bg-light p-3 text-center">
                                <i class="bi bi-shield-check text-primary fs-4 mb-2"></i>
                                <small>รับประกัน 1 ปี</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 bg-light p-3 text-center">
                                <i class="bi bi-arrow-repeat text-success fs-4 mb-2"></i>
                                <small>คืนสินค้าใน 7 วัน</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.product-main-image {
    transition: transform 0.3s ease;
    cursor: zoom-in;
}

.product-main-image:hover {
    transform: scale(1.02);
}

.btn-floating {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.price-section {
    background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
    padding: 1.5rem;
    border-radius: 12px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}
</style>
@endsection

@section('scripts')
<script>
function increaseQty() {
    const input = document.getElementById('quantity');
    input.value = parseInt(input.value) + 1;
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function zoomImage() {
    alert('Zoom feature coming soon!');
}

function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;

    fetch(`/account/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart counter if exists
            const cartCounter = document.getElementById('cart-counter');
            if (cartCounter) {
                cartCounter.textContent = data.cart_count;
            }

            // Show success message
            showToast('เพิ่มสินค้าลงตะกร้าแล้ว!', 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง', 'error');
    });
}

function addToWishlist(productId) {
    fetch(`/account/wishlist/toggle/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button text/icon based on action
            const button = document.querySelector('button[onclick*="addToWishlist"]');
            if (data.action === 'added') {
                button.innerHTML = '<i class="bi bi-heart-fill text-danger me-2"></i>อยู่ในรายการโปรด';
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-danger');
            } else {
                button.innerHTML = '<i class="bi bi-heart me-2"></i>เพิ่มในรายการโปรด';
                button.classList.remove('btn-danger');
                button.classList.add('btn-outline-secondary');
            }

            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง', 'error');
    });
}

function showLoginModal() {
    // Redirect to login page
    window.location.href = '{{ route("login") }}';
}

function showToast(message, type) {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    document.body.appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}
</script>
@endsection
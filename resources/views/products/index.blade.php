@extends('layouts.default')

@section('title', 'สินค้าทั้งหมด')

@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-funnel me-2"></i>กรองสินค้า
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Category Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">หมวดหมู่</label>
                        <select class="form-select border-0 shadow-sm" id="categoryFilter">
                            <option value="">ทั้งหมด</option>
                            <option value="electronics">อุปกรณ์อิเล็กทรอนิกส์</option>
                            <option value="clothing">เสื้อผ้า</option>
                            <option value="books">หนังสือ</option>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">ช่วงราคา</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" class="form-control border-0 shadow-sm" placeholder="ต่ำสุด" id="minPrice">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control border-0 shadow-sm" placeholder="สูงสุด" id="maxPrice">
                            </div>
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">คะแนน</label>
                        <div class="rating-filter">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="rating4">
                                <label class="form-check-label fw-medium" for="rating4">
                                    <span class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star text-warning"></i>
                                        </div>
                                        <span>4 ดาวขึ้นไป</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100 py-2 fw-bold" id="applyFilters">
                        <i class="bi bi-search me-2"></i>ค้นหา
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9 col-md-8">
            <div class="row mb-4 align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0">สินค้าทั้งหมด</h1>
                    <p class="text-muted mt-1">พบ 24 สินค้า</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <select class="form-select d-inline-block w-auto" id="sortSelect">
                        <option value="newest">ใหม่ล่าสุด</option>
                        <option value="price-low">ราคาต่ำไปสูง</option>
                        <option value="price-high">ราคาสูงไปต่ำ</option>
                        <option value="rating">คะแนนสูงสุด</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row g-3 g-lg-4" id="productsGrid">
                <!-- Product Card 1 -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200?text=Product+1" class="card-img-top" alt="สินค้าตัวอย่าง 1">
                            <div class="product-badge bg-success">ใหม่</div>
                            <div class="product-wishlist">
                                <button class="btn" title="เพิ่มในรายการโปรด">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">สินค้าตัวอย่าง 1</h6>
                            <div class="rating mb-2">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-half text-warning"></i>
                                <small class="text-muted ms-1">(4.5)</small>
                            </div>
                            <p class="card-text flex-grow-1">คำอธิบายสินค้าสั้นๆ ที่น่าสนใจและมีคุณภาพสูง</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0 text-primary fw-bold">฿1,299</span>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="bi bi-cart-plus me-1"></i>เพิ่ม
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Card 2 -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200?text=Product+2" class="card-img-top" alt="สินค้าตัวอย่าง 2">
                            <div class="product-badge bg-danger">ลดราคา</div>
                            <div class="product-wishlist">
                                <button class="btn" title="เพิ่มในรายการโปรด">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">สินค้าตัวอย่าง 2</h6>
                            <div class="rating mb-2">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star text-warning"></i>
                                <small class="text-muted ms-1">(4.0)</small>
                            </div>
                            <p class="card-text flex-grow-1">สินค้าคุณภาพดีพร้อมการรับประกัน</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0 text-primary fw-bold">฿899</span>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="bi bi-cart-plus me-1"></i>เพิ่ม
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Card 3 -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200?text=Product+3" class="card-img-top" alt="สินค้าตัวอย่าง 3">
                            <div class="product-wishlist">
                                <button class="btn" title="เพิ่มในรายการโปรด">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">สินค้าตัวอย่าง 3</h6>
                            <div class="rating mb-2">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <small class="text-muted ms-1">(5.0)</small>
                            </div>
                            <p class="card-text flex-grow-1">สินค้าพรีเมี่ยมคุณภาพระดับโลก</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0 text-primary fw-bold">฿2,499</span>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="bi bi-cart-plus me-1"></i>เพิ่ม
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Card 4 -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200?text=Product+4" class="card-img-top" alt="สินค้าตัวอย่าง 4">
                            <div class="product-wishlist">
                                <button class="btn" title="เพิ่มในรายการโปรด">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">สินค้าตัวอย่าง 4</h6>
                            <div class="rating mb-2">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-half text-warning"></i>
                                <i class="bi bi-star text-warning"></i>
                                <small class="text-muted ms-1">(3.5)</small>
                            </div>
                            <p class="card-text flex-grow-1">สินค้าที่คุ้มค่ากับราคา</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0 text-primary fw-bold">฿599</span>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="bi bi-cart-plus me-1"></i>เพิ่ม
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Products pagination" class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">ก่อนหน้า</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">ถัดไป</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const applyFiltersBtn = document.getElementById('applyFilters');
    const categoryFilter = document.getElementById('categoryFilter');
    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    const sortSelect = document.getElementById('sortSelect');

    applyFiltersBtn.addEventListener('click', function() {
        // Implement filter logic here
        console.log('Applying filters...');
    });

    sortSelect.addEventListener('change', function() {
        // Implement sorting logic here
        console.log('Sorting by:', this.value);
    });

    // Add to cart functionality
    document.querySelectorAll('.btn-primary').forEach(btn => {
        if (btn.textContent.includes('เพิ่ม')) {
            btn.addEventListener('click', function() {
                // Implement add to cart logic
                alert('เพิ่มสินค้าในตะกร้าแล้ว!');
            });
        }
    });
});
</script>
@endsection
@endsection

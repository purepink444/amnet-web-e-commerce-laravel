@extends('layouts.default')

@section('title', 'เขียนรีวิว - ' . $product->product_name)

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ route('client.products.index') }}">สินค้า</a></li>
            <li class="breadcrumb-item"><a href="{{ route('client.products.show', $product->product_id) }}">{{ $product->product_name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.reviews.index', $product->product_id) }}">รีวิว</a></li>
            <li class="breadcrumb-item active">เขียนรีวิว</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Product Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="{{ $product->image_url ?: 'https://via.placeholder.com/80' }}"
                             alt="{{ $product->product_name }}"
                             class="rounded me-3"
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <h5 class="mb-1">{{ $product->product_name }}</h5>
                            <p class="text-muted mb-0">฿{{ number_format($product->price, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>เขียนรีวิวสินค้า
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.reviews.store', $product->product_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Rating -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">ให้คะแนนสินค้า</label>
                            <div class="rating-stars mb-2">
                                <input type="hidden" name="rating" id="rating" value="5" required>
                                @for($i = 5; $i >= 1; $i--)
                                <i class="bi bi-star-fill star-rating fs-3 me-1"
                                   data-rating="{{ $i }}"
                                   onclick="setRating({{ $i }})"
                                   onmouseover="hoverRating({{ $i }})"
                                   onmouseout="resetRating()"></i>
                                @endfor
                            </div>
                            <div id="rating-text" class="text-muted">ดีมาก (5 ดาว)</div>
                        </div>

                        <!-- Comment -->
                        <div class="mb-4">
                            <label for="comment" class="form-label fw-semibold">ความคิดเห็น</label>
                            <textarea class="form-control" id="comment" name="comment" rows="6"
                                      placeholder="แบ่งปันประสบการณ์ของคุณกับสินค้านี้... (อย่างน้อย 10 ตัวอักษร)"
                                      required minlength="10" maxlength="1000"></textarea>
                            <div class="form-text">
                                <span id="char-count">0</span>/1000 ตัวอักษร
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">รูปภาพ (ไม่บังคับ)</label>
                            <input type="file" class="form-control" id="review_images" name="review_images[]" multiple accept="image/*">
                            <div class="form-text">
                                สามารถอัปโหลดได้สูงสุด 5 รูปภาพ (ไฟล์ละไม่เกิน 2MB)
                            </div>
                            <div id="image-preview" class="row g-2 mt-3"></div>
                        </div>

                        <!-- Guidelines -->
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>คำแนะนำในการเขียนรีวิว</h6>
                            <ul class="mb-0">
                                <li>รีวิวควรเป็นความคิดเห็นที่ตรงไปตรงมาและมีประโยชน์</li>
                                <li>หลีกเลี่ยงการใช้ภาษาที่ไม่สุภาพหรือเนื้อหาที่ไม่เหมาะสม</li>
                                <li>รีวิวจะช่วยให้ผู้ซื้อคนอื่นตัดสินใจได้ดีขึ้น</li>
                            </ul>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('products.reviews.index', $product->product_id) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>ส่งรีวิว
                            </button>
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
.star-rating {
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.star-rating.active,
.star-rating:hover {
    color: #ffc107;
}

.star-rating:hover ~ .star-rating {
    color: #ddd;
}
</style>
@endsection

@section('scripts')
<script>
let currentRating = 5;
const ratingTexts = {
    1: 'แย่มาก (1 ดาว)',
    2: 'แย่ (2 ดาว)',
    3: 'ปานกลาง (3 ดาว)',
    4: 'ดี (4 ดาว)',
    5: 'ดีมาก (5 ดาว)'
};

function setRating(rating) {
    currentRating = rating;
    document.getElementById('rating').value = rating;
    document.getElementById('rating-text').textContent = ratingTexts[rating];
    updateStars();
}

function hoverRating(rating) {
    updateStars(rating);
}

function resetRating() {
    updateStars();
}

function updateStars(hoverRating = null) {
    const stars = document.querySelectorAll('.star-rating');
    const activeRating = hoverRating || currentRating;

    stars.forEach((star, index) => {
        const starRating = 5 - index; // Reverse order
        if (starRating <= activeRating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

// Initialize stars
updateStars();

// Character counter
document.getElementById('comment').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('char-count').textContent = count;

    if (count > 900) {
        document.getElementById('char-count').className = 'text-danger';
    } else if (count > 800) {
        document.getElementById('char-count').className = 'text-warning';
    } else {
        document.getElementById('char-count').className = '';
    }
});

// Image preview
document.getElementById('review_images').addEventListener('change', function(e) {
    const files = e.target.files;
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';

    if (files.length > 5) {
        alert('สามารถอัปโหลดได้สูงสุด 5 รูปภาพ');
        e.target.value = '';
        return;
    }

    Array.from(files).forEach((file, index) => {
        if (file.size > 2 * 1024 * 1024) { // 2MB
            alert(`ไฟล์ ${file.name} มีขนาดใหญ่เกินไป (สูงสุด 2MB)`);
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-md-3 col-sm-4';
            col.innerHTML = `
                <div class="position-relative">
                    <img src="${e.target.result}" class="img-fluid rounded border" alt="Preview ${index + 1}">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                            onclick="removeImage(${index})">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
            preview.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
});

function removeImage(index) {
    const input = document.getElementById('review_images');
    const files = Array.from(input.files);
    files.splice(index, 1);

    // Create new FileList
    const dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));
    input.files = dt.files;

    // Trigger change event to update preview
    input.dispatchEvent(new Event('change'));
}
</script>
@endsection
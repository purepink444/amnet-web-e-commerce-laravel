@extends('layouts.default')

@section('title', 'รีวิวสินค้า - ' . $product->product_name)

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ route('client.products.index') }}">สินค้า</a></li>
            <li class="breadcrumb-item"><a href="{{ route('client.products.show', $product->product_id) }}">{{ $product->product_name }}</a></li>
            <li class="breadcrumb-item active">รีวิว</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Info Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <img src="{{ $product->image_url ?: 'https://via.placeholder.com/200' }}"
                         alt="{{ $product->product_name }}"
                         class="img-fluid rounded mb-3"
                         style="max-height: 200px;">
                    <h5 class="card-title">{{ $product->product_name }}</h5>
                    <div class="text-warning mb-2">
                        {!! $product->rating_stars !!}
                    </div>
                    <p class="text-muted mb-2">{{ number_format($product->average_rating, 1) }}/5 จาก {{ $product->total_reviews }} รีวิว</p>
                    <h4 class="text-primary">฿{{ number_format($product->price, 2) }}</h4>
                    <a href="{{ route('client.products.show', $product->product_id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-eye me-1"></i>ดูสินค้า
                    </a>
                </div>
            </div>

            <!-- Rating Statistics -->
            @if($product->hasReviews())
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">สถิติการให้คะแนน</h6>
                </div>
                <div class="card-body">
                    @foreach($ratingStats['rating_distribution'] as $stars => $count)
                    <div class="d-flex align-items-center mb-2">
                        <span class="text-muted me-2" style="min-width: 30px;">{{ $stars }}★</span>
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-warning"
                                 style="width: {{ $ratingStats['total_reviews'] > 0 ? ($count / $ratingStats['total_reviews']) * 100 : 0 }}%">
                            </div>
                        </div>
                        <span class="text-muted ms-2" style="min-width: 30px;">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Reviews List -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>รีวิวสินค้า ({{ $ratingStats['total_reviews'] }})</h3>
                @auth
                    @php
                        $member = auth()->user()->member;
                        $canReview = $member && $member->canReviewProduct($product->product_id) && !$member->hasReviewedProduct($product->product_id);
                    @endphp
                    @if($canReview)
                        <a href="{{ route('products.reviews.create', $product->product_id) }}" class="btn btn-success">
                            <i class="bi bi-pencil-square me-1"></i>เขียนรีวิว
                        </a>
                    @endif
                @endauth
            </div>

            @if($reviews->count() > 0)
                @foreach($reviews as $review)
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <!-- Avatar -->
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px; font-weight: bold; font-size: 18px;">
                                    {{ substr($review->member->user->firstname, 0, 1) . substr($review->member->user->lastname, 0, 1) }}
                                </div>
                            </div>

                            <!-- Review Content -->
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $review->member->full_name }}</h6>
                                        <div class="text-warning mb-1">
                                            {!! $review->rating_stars !!}
                                            <small class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>

                                    <!-- Review Actions (for owner) -->
                                    @auth
                                        @if(auth()->user()->member_id === $review->member_id)
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('products.reviews.edit', [$product->product_id, $review->review_id]) }}">
                                                    <i class="bi bi-pencil me-1"></i>แก้ไข
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $review->review_id }})">
                                                    <i class="bi bi-trash me-1"></i>ลบ
                                                </a></li>
                                            </ul>
                                        </div>
                                        @endif
                                    @endauth
                                </div>

                                <!-- Comment -->
                                <p class="mb-3">{{ nl2br(e($review->comment)) }}</p>

                                <!-- Images -->
                                @if($review->hasImages())
                                <div class="row g-2 mb-3">
                                    @foreach($review->getImages() as $image)
                                    <div class="col-auto">
                                        <img src="{{ asset('storage/reviews/' . $image) }}"
                                             alt="Review Image"
                                             class="rounded border"
                                             style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                             onclick="showImageModal('{{ asset('storage/reviews/' . $image) }}')">
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Helpful/Report buttons -->
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-success" onclick="markHelpful({{ $review->review_id }})">
                                        <i class="bi bi-hand-thumbs-up me-1"></i>มีประโยชน์
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="reportReview({{ $review->review_id }})">
                                        <i class="bi bi-flag me-1"></i>รายงาน
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-chat-dots text-muted fs-1 mb-3"></i>
                        <h5 class="text-muted">ยังไม่มีรีวิว</h5>
                        <p class="text-muted">เป็นคนแรกที่เขียนรีวิวสำหรับสินค้านี้!</p>
                        @auth
                            @php
                                $member = auth()->user()->member;
                                $canReview = $member && $member->canReviewProduct($product->product_id);
                            @endphp
                            @if($canReview)
                                <a href="{{ route('products.reviews.create', $product->product_id) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil-square me-1"></i>เขียนรีวิวแรก
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" alt="Review Image" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ยืนยันการลบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                คุณต้องการลบรีวิวนี้หรือไม่? การกระทำนี้ไม่สามารถยกเลิกได้
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">ลบรีวิว</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function confirmDelete(reviewId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `{{ route('products.reviews.destroy', [$product->product_id, '']) }}/${reviewId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function markHelpful(reviewId) {
    // TODO: Implement helpful marking
    alert('ฟีเจอร์นี้กำลังพัฒนา');
}

function reportReview(reviewId) {
    // TODO: Implement review reporting
    alert('ฟีเจอร์นี้กำลังพัฒนา');
}
</script>
@endsection
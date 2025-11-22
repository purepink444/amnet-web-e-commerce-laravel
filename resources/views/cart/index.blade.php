@extends('layouts.default')

@section('title', 'ตะกร้าสินค้า')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-gradient-orange text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-cart3 me-2"></i>ตะกร้าสินค้า
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($cart->items->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">ตะกร้าของคุณว่างเปล่า</h5>
                            <p class="text-muted">เพิ่มสินค้าลงในตะกร้าก่อนทำการสั่งซื้อ</p>
                            <a href="{{ route('client.products.index') }}" class="btn btn-orange">
                                <i class="bi bi-bag me-2"></i>เลือกซื้อสินค้า
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>สินค้า</th>
                                        <th class="text-center">ราคา</th>
                                        <th class="text-center" style="width: 120px;">จำนวน</th>
                                        <th class="text-center">รวม</th>
                                        <th class="text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->image_url ? asset('storage/' . $item->product->image_url) : 'https://via.placeholder.com/60x60?text=No+Image' }}"
                                                     alt="{{ $item->product->product_name }}"
                                                     class="rounded me-3"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-1">{{ $item->product->product_name }}</h6>
                                                    <small class="text-muted">คงเหลือ: {{ $item->product->stock_quantity }} ชิ้น</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            ฿{{ number_format($item->product->price, 2) }}
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="input-group input-group-sm" style="width: 100px; margin: 0 auto;">
                                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                                        onclick="updateQuantity({{ $item->product_id }}, {{ $item->quantity - 1 }})"
                                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                                                <input type="number" class="form-control text-center"
                                                       value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}"
                                                       onchange="updateQuantity({{ $item->product_id }}, this.value)">
                                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                                        onclick="updateQuantity({{ $item->product_id }}, {{ $item->quantity + 1 }})"
                                                        {{ $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}>+</button>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle fw-bold">
                                            ฿{{ number_format($item->subtotal, 2) }}
                                        </td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-outline-danger btn-sm"
                                                    onclick="removeItem({{ $item->product_id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(!$cart->items->isEmpty())
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-gradient-orange text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>สรุปคำสั่งซื้อ
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>จำนวนสินค้า:</span>
                        <span class="fw-bold">{{ $cart->total_items }} ชิ้น</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>ราคารวม:</span>
                        <span class="fw-bold text-orange">฿{{ number_format($cart->total_price, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 mb-0">รวมทั้งสิ้น:</span>
                        <span class="h5 mb-0 text-orange fw-bold">฿{{ number_format($cart->total_price, 2) }}</span>
                    </div>

                    <a href="{{ route('account.checkout.index') }}" class="btn btn-success btn-lg w-100 mb-3">
                        <i class="bi bi-credit-card me-2"></i>ดำเนินการชำระเงิน
                    </a>

                    <a href="{{ route('client.products.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left me-2"></i>เลือกซื้อสินค้าเพิ่ม
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateQuantity(productId, quantity) {
    if (quantity < 1) return;

    fetch('/account/cart/update', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ product_id: productId, quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
    });
}

function removeItem(productId) {
    if (!confirm('คุณต้องการลบสินค้านี้ออกจากตะกร้าหรือไม่?')) return;

    fetch('/account/cart/remove', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
    });
}
</script>
@endsection
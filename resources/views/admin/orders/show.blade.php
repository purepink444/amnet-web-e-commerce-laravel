@extends('layouts.admin')

@section('title', 'รายละเอียดคำสั่งซื้อ')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">รายละเอียดคำสั่งซื้อ #{{ $order->order_id }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>กลับ
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">รหัสคำสั่งซื้อ:</label>
                            <p class="form-control-plaintext">{{ $order->order_id }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">วันที่สั่งซื้อ:</label>
                            <p class="form-control-plaintext">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ลูกค้า:</label>
                            <p class="form-control-plaintext">
                                {{ $order->member ? $order->member->first_name . ' ' . $order->member->last_name : 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">อีเมล:</label>
                            <p class="form-control-plaintext">{{ $order->user ? $order->user->email : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">สถานะ:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $order->order_status == 'delivered' ? 'success' : ($order->order_status == 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ $order->order_status == 'pending' ? 'รอดำเนินการ' :
                                       ($order->order_status == 'paid' ? 'ชำระเงินแล้ว' :
                                       ($order->order_status == 'shipped' ? 'จัดส่งแล้ว' :
                                       ($order->order_status == 'delivered' ? 'ส่งถึงแล้ว' : 'ยกเลิก'))) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ยอดรวม:</label>
                            <p class="form-control-plaintext fs-5 text-primary">฿{{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">รายการสินค้า</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>สินค้า</th>
                                <th>ราคา</th>
                                <th>จำนวน</th>
                                <th>รวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->image_url)
                                                <img src="{{ asset('storage/' . $item->product->image_url) }}"
                                                     alt="{{ $item->product->product_name }}"
                                                     class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $item->product ? $item->product->product_name : 'สินค้าที่ถูกลบ' }}</strong>
                                                @if($item->product)
                                                    <br><small class="text-muted">{{ $item->product->category ? $item->product->category->category_name : '' }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>฿{{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>฿{{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">ไม่มีรายการสินค้า</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">ยอดรวม:</th>
                                <th>฿{{ number_format($order->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">จัดการคำสั่งซื้อ</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update-status', $order->order_id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="status" class="form-label">อัปเดตสถานะ</label>
                        <select name="status" class="form-control" id="status">
                            <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                            <option value="paid" {{ $order->order_status == 'paid' ? 'selected' : '' }}>ชำระเงินแล้ว</option>
                            <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                            <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>ส่งถึงแล้ว</option>
                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i>อัปเดตสถานะ
                    </button>
                </form>

                <hr>

                <form action="{{ route('admin.orders.destroy', $order->order_id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100"
                            onclick="return confirm('ต้องการลบคำสั่งซื้อนี้หรือไม่? การกระทำนี้ไม่สามารถยกเลิกได้!')">
                        <i class="fas fa-trash me-1"></i>ลบคำสั่งซื้อ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

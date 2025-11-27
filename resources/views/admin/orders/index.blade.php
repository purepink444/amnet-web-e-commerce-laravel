@extends('layouts.admin')

@section('title', 'จัดการคำสั่งซื้อ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">จัดการคำสั่งซื้อ</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>รหัสคำสั่งซื้อ</th>
                        <th>ลูกค้า</th>
                        <th>อีเมล</th>
                        <th>ยอดรวม</th>
                        <th>สถานะ</th>
                        <th>วันที่สั่งซื้อ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_id }}</td>
                            <td>{{ $order->user ? $order->user->firstname . ' ' . $order->user->lastname : 'N/A' }}</td>
                            <td>{{ $order->user ? $order->user->email : 'N/A' }}</td>
                            <td>฿{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->order_status == 'delivered' ? 'success' : ($order->order_status == 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ $order->order_status == 'pending' ? 'รอดำเนินการ' :
                                       ($order->order_status == 'paid' ? 'ชำระเงินแล้ว' :
                                       ($order->order_status == 'shipped' ? 'จัดส่งแล้ว' :
                                       ($order->order_status == 'delivered' ? 'ส่งถึงแล้ว' : 'ยกเลิก'))) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.orders.update-status', $order->order_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto me-1" onchange="this.form.submit()">
                                            <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                                            <option value="paid" {{ $order->order_status == 'paid' ? 'selected' : '' }}>ชำระเงินแล้ว</option>
                                            <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                                            <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>ส่งถึงแล้ว</option>
                                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                        </select>
                                    </form>
                                    <form action="{{ route('admin.orders.destroy', $order->order_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('ต้องการลบคำสั่งซื้อนี้หรือไม่?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">ไม่มีคำสั่งซื้อ</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'รายงานการขาย')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">รายงานการขาย</h3>
        <div class="card-tools">
            <a href="{{ route('admin.reports.export', ['type' => 'sales'] + request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-download me-1"></i>ส่งออก Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                           value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                           value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">สถานะ</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">ทั้งหมด</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>ส่งถึงแล้ว</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>ค้นหา
                        </button>
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>ล้าง
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($salesSummary['total_sales'], 2) }}</h3>
                        <p>ยอดขายรวม (บาท)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $salesSummary['total_orders'] }}</h3>
                        <p>จำนวนคำสั่งซื้อ</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($salesSummary['average_order_value'], 2) }}</h3>
                        <p>ค่าเฉลี่ยต่อคำสั่งซื้อ</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $orders->total() }}</h3>
                        <p>จำนวนรายการที่แสดง</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
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
                            <td>{{ $order->member ? $order->member->first_name . ' ' . $order->member->last_name : 'N/A' }}</td>
                            <td>{{ $order->user ? $order->user->email : 'N/A' }}</td>
                            <td>฿{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ $order->status == 'pending' ? 'รอดำเนินการ' :
                                       ($order->status == 'processing' ? 'กำลังดำเนินการ' :
                                       ($order->status == 'shipped' ? 'จัดส่งแล้ว' :
                                       ($order->status == 'delivered' ? 'ส่งถึงแล้ว' : 'ยกเลิก'))) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">ไม่พบข้อมูลคำสั่งซื้อ</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="d-flex justify-content-center">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
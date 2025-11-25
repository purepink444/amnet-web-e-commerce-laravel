@extends('layouts.admin')

@section('title', 'รายงานลูกค้า')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">รายงานลูกค้า</h3>
        <div class="card-tools">
            <a href="{{ route('admin.reports.export', ['type' => 'customers'] + request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-download me-1"></i>ส่งออก Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <label for="search" class="form-label">ค้นหาลูกค้า</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="ชื่อ, นามสกุล หรืออีเมล...">
                </div>
                <div class="col-md-6">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>ค้นหา
                        </button>
                        <a href="{{ route('admin.reports.customers') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>ล้าง
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Customers Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>อีเมล</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>จำนวนคำสั่งซื้อ</th>
                        <th>ยอดรวมการสั่งซื้อ</th>
                        <th>ค่าเฉลี่ยต่อคำสั่งซื้อ</th>
                        <th>วันที่สมัคร</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->user_id }}</td>
                            <td>{{ $customer->firstname }} {{ $customer->lastname }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td>{{ $customer->total_orders ?? 0 }}</td>
                            <td>฿{{ number_format($customer->total_spent ?? 0, 2) }}</td>
                            <td>฿{{ number_format($customer->average_order_value ?? 0, 2) }}</td>
                            <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $customer->user_id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $customer->user_id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">ไม่พบข้อมูลลูกค้า</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="d-flex justify-content-center">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
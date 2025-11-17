@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <!-- Info Boxes -->
    @php
        $infoBoxes = [
            ['icon' => 'fas fa-shopping-cart', 'text' => 'คำสั่งซื้อทั้งหมด', 'number' => $totalOrders, 'growth' => $ordersGrowth, 'bg' => 'bg-gradient-orange'],
            ['icon' => 'fas fa-dollar-sign', 'text' => 'ยอดขาย', 'number' => $totalRevenue, 'growth' => $revenueGrowth, 'bg' => 'bg-gradient-success', 'prefix' => '฿'],
            ['icon' => 'fas fa-box', 'text' => 'สินค้าทั้งหมด', 'number' => $totalProducts, 'bg' => 'bg-gradient-warning'],
            ['icon' => 'fas fa-users', 'text' => 'สมาชิก', 'number' => $totalUsers, 'growth' => $usersGrowth, 'bg' => 'bg-gradient-info'],
        ];
    @endphp
    @foreach($infoBoxes as $box)
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon {{ $box['bg'] }} elevation-1">
                    <i class="{{ $box['icon'] }}"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ $box['text'] }}</span>
                    <span class="info-box-number">
                        {{ isset($box['prefix']) ? $box['prefix'] : '' }}{{ number_format($box['number']) }}
                        @if(isset($box['growth']) && $box['growth'] != 0)
                            <small class="{{ $box['growth'] > 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $box['growth'] > 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($box['growth']), 1) }}%
                            </small>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Charts & Recent Orders -->
<div class="row mt-4">
    <!-- Sales Chart -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header border-0 d-flex justify-content-between">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2 text-orange"></i>
                    สถิติยอดขาย
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="position-relative" style="height: 300px;">
                    <canvas id="sales-chart"></canvas>
                </div>
                <div class="d-flex flex-row justify-content-end mt-3">
                    <span class="mr-2"><i class="fas fa-square text-orange"></i> ปีนี้</span>
                    <span><i class="fas fa-square text-gray"></i> ปีที่แล้ว</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Summary -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-shopping-bag mr-2 text-orange"></i> สรุปสถานะคำสั่งซื้อ</h3>
            </div>
            <div class="card-body">
                @php
                    $statusList = [
                        'completed' => ['class'=>'success', 'icon'=>'fas fa-check-circle', 'text'=>'สำเร็จ'],
                        'pending' => ['class'=>'warning', 'icon'=>'fas fa-clock', 'text'=>'รอดำเนินการ'],
                        'shipping' => ['class'=>'info', 'icon'=>'fas fa-shipping-fast', 'text'=>'กำลังจัดส่ง'],
                        'cancelled' => ['class'=>'danger', 'icon'=>'fas fa-times-circle', 'text'=>'ยกเลิก'],
                    ];
                @endphp
                @foreach($statusList as $key => $status)
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                        <p class="text-{{ $status['class'] }} text-xl"><i class="{{ $status['icon'] }}"></i></p>
                        <p class="d-flex flex-column text-right mb-0">
                            <span class="font-weight-bold">{{ $status['text'] }}</span>
                            <span class="text-muted">{{ $ordersByStatus[$key] ?? 0 }} คำสั่งซื้อ</span>
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title"><i class="fas fa-list mr-2 text-orange"></i> คำสั่งซื้อล่าสุด</h3>
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>เลขที่</th>
                                <th>ลูกค้า</th>
                                <th>สินค้า</th>
                                <th>ยอดรวม</th>
                                <th>สถานะ</th>
                                <th>วันที่</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>#{{ $order->order_number ?? 'ORD-' . str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $order->user->name ?? 'ไม่ระบุ' }}</td>
                                    <td>
                                        @if($order->orderItems->count())
                                            {{ $order->orderItems->first()->product->name ?? 'สินค้า' }}
                                            @if($order->orderItems->count() > 1)
                                                <small class="text-muted">+{{ $order->orderItems->count()-1 }} รายการ</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>฿{{ number_format($order->total_amount,2) }}</td>
                                    @php
                                        $s = $statusList[$order->status] ?? ['class'=>'secondary','text'=>$order->status];
                                    @endphp
                                    <td><span class="badge badge-{{ $s['class'] }}">{{ $s['text'] }}</span></td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>ยังไม่มีคำสั่งซื้อ</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products & Quick Actions -->
<div class="row mt-4">
    <!-- Top Products -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-fire text-orange mr-2"></i> สินค้าขายดี</h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    @forelse($topProducts as $product)
                        <li class="item d-flex align-items-center">
                            <div class="product-img mr-3">
                                <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/50' }}" alt="{{ $product->name }}">
                            </div>
                            <div class="product-info flex-grow-1">
                                <a href="{{ route('admin.products.show', $product->id) }}" class="product-title">
                                    {{ Str::limit($product->name, 30) }}
                                    @php
                                        $badgeColors = ['warning','info','danger','success'];
                                        $badgeColor = $badgeColors[$loop->index % 4];
                                    @endphp
                                    <span class="badge badge-{{ $badgeColor }} float-right">฿{{ number_format($product->price) }}</span>
                                </a>
                                <span class="product-description">ขายแล้ว {{ number_format($product->total_sold ?? 0) }} ชิ้น</span>
                            </div>
                        </li>
                    @empty
                        <li class="item text-center text-muted py-3">
                            <i class="fas fa-box-open"></i>
                            <p class="mb-0">ยังไม่มีข้อมูลสินค้าขายดี</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt text-warning mr-2"></i> เมนูด่วน</h3>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="row w-100">
                    <div class="col-6 mb-3">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-app bg-orange w-100 py-4">
                            <i class="fas fa-plus fa-2x mb-2"></i><br>เพิ่มสินค้า
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="/orders" class="btn btn-app bg-success w-100 py-4">
                            <i class="fas fa-list fa-2x mb-2"></i><br>คำสั่งซื้อ
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="/customers" class="btn btn-app bg-info w-100 py-4">
                            <i class="fas fa-users fa-2x mb-2"></i><br>ลูกค้า
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="/reports" class="btn btn-app bg-warning w-100 py-4">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i><br>รายงาน
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
:root {
    --orange-primary: #ff6b35;
    --orange-dark: #e85d2a;
    --black-primary: #1a1a1a;
    --black-secondary: #2d2d2d;
}

.bg-gradient-orange {
    background: linear-gradient(135deg, var(--orange-primary), var(--orange-dark)) !important;
}
.bg-gradient-success { background: linear-gradient(135deg,#28a745,#20c997)!important; }
.bg-gradient-warning { background: linear-gradient(135deg,#ffc107,#ff9800)!important; }
.bg-gradient-info { background: linear-gradient(135deg,#17a2b8,#00bcd4)!important; }

.info-box { border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08); transition:all 0.3s ease; }
.info-box:hover { transform:translateY(-5px); box-shadow:0 8px 20px rgba(255,107,53,0.15); }
.info-box-icon { border-radius:12px 0 0 12px; display:flex; justify-content:center; align-items:center; }

.card { border-radius:15px; box-shadow:0 4px 20px rgba(0,0,0,0.05); border:none; transition:all 0.3s ease; }
.card:hover { transform:translateY(-5px); box-shadow:0 10px 25px rgba(0,0,0,0.1); }
.card-header { border-bottom:2px solid var(--orange-primary); border-radius:15px 15px 0 0!important; }

.table tbody tr:hover { background: linear-gradient(90deg,#fff8f5,#fff); transform: scale(1.01); }
.product-img img { border-radius:8px; width:50px;height:50px;object-fit:cover; transition:all 0.3s ease; }
.product-img img:hover { transform:scale(1.05); box-shadow:0 4px 15px rgba(0,0,0,0.08); }

.btn-app { border-radius:12px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:130px; font-weight:600; transition:all 0.3s ease;}
.btn-app:hover { transform:translateY(-5px) scale(1.05); box-shadow:0 8px 20px rgba(0,0,0,0.15); }

.content-wrapper { padding:2rem; background:linear-gradient(135deg,#f8f9fa 0%,#ffffff 100%); }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function(){
    const ctx = document.getElementById('sales-chart').getContext('2d');
    new Chart(ctx, {
        type:'line',
        data:{
            labels:['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'],
            datasets:[
                {label:'ปีนี้', backgroundColor:'rgba(255,107,53,0.2)', borderColor:'#ff6b35', pointBackgroundColor:'#ff6b35', data:@json($salesData['currentYear'])},
                {label:'ปีที่แล้ว', backgroundColor:'rgba(210,214,222,0.2)', borderColor:'rgba(210,214,222,1)', pointBackgroundColor:'rgba(210,214,222,1)', data:@json($salesData['lastYear'])}
            ]
        },
        options:{
            maintainAspectRatio:false,
            responsive:true,
            plugins:{legend:{display:false}, tooltip:{callbacks:{label:function(c){return '฿'+c.raw.toLocaleString();}}}},
            scales:{y:{beginAtZero:false,ticks:{callback:function(v){return '฿'+v.toLocaleString();}},grid:{color:'rgba(0,0,0,0.05)'}}, x:{grid:{display:false}}},
            elements:{line:{tension:0.4}, point:{radius:4,hitRadius:10,hoverRadius:6}}
        }
    });
});
</script>
@endsection

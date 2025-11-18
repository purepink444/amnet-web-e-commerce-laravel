@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('styles')
<style>
/* === Design tokens (from your theme) === */
:root {
    --orange-primary: #ff6b35;
    --orange-dark: #e85d2a;
    --orange-light: #ff8c5f;

    --black-primary: #1a1a1a;
    --black-secondary: #2d2d2d;
    --black-tertiary: #3d3d3d;

    --gray-text: #6c757d;
    --gray-light: #f8f9fa;

    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;

    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 15px;

    --transition: 0.3s cubic-bezier(0.4,0,0.2,1);

    --shadow-sm: 0 2px 4px rgba(0,0,0,0.08);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
    --shadow-lg: 0 20px 40px -12px rgba(255,107,53,0.25);
}

/* === Utilities (reusable classes) === */
.text-orange { color: var(--orange-primary) !important; }
.bg-orange { background: var(--orange-primary) !important; color: #fff !important; }
.bg-orange-dark { background: var(--orange-dark) !important; color: #fff !important; }
.border-orange { border-color: var(--orange-primary) !important; }
.btn-orange { background: var(--orange-primary); color: #fff; border: none; }
.badge-orange { background: var(--orange-primary); color:#fff; }

/* === Sidebar (AdminLTE 4 targeted overrides) === */
.main-sidebar { background: linear-gradient(180deg,var(--orange-primary),var(--orange-dark)); }
.main-sidebar .nav-link { color: rgba(255,255,255,0.95); }
.main-sidebar .nav-link.active { background: rgba(255,255,255,0.08); color: #fff; }
.main-sidebar .brand-link { background: transparent; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.06); }

/* make icons visible on orange bg */
.main-sidebar .nav-icon { color: rgba(255,255,255,0.95); }

/* === Content & cards === */
.content-wrapper { background: var(--gray-light); font-family: var(--font-sans); padding: 1.6rem; }
.card { border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: none; }
.card-header { background:#fff; border-bottom: 3px solid var(--orange-primary); border-radius: var(--radius-lg) var(--radius-lg) 0 0; }

/* Info boxes - adapted from AdminLTE small-box / info-box */
.info-box { display:flex; align-items:center; gap:1rem; padding: 1rem; border-radius: var(--radius-md); background:#fff; box-shadow: var(--shadow-sm); }
.info-box .info-box-icon { width:70px; height:70px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; }
.info-box .info-box-content { flex:1; }
.info-box .info-box-text { color:var(--gray-text); display:block; }
.info-box .info-box-number { font-size:1.25rem; font-weight:700; }

.bg-gradient-orange { background: linear-gradient(135deg,var(--orange-primary),var(--orange-dark)) !important; color:#fff; }

/* Table */
.table thead th { background:#fff; }
.table tbody tr:hover { background: #fff3ef; }

/* Quick actions */
.btn-app { border-radius: 12px; border:2px solid var(--orange-primary); background:#fff; color:var(--black-primary); padding:1rem; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.btn-app:hover { background: var(--orange-primary); color:#fff; }

/* Modal (theming) */
.modal-content { border-radius: 12px; border:none; box-shadow: var(--shadow-lg); }
.modal-header { border-bottom:none; }
.modal-footer { border-top:none; }

/* Toast / Notification (simple) */
.toast-custom { background: linear-gradient(90deg,var(--orange-primary),var(--orange-dark)); color:#fff; padding:0.75rem 1rem; border-radius:10px; box-shadow:var(--shadow-md); }

/* Dark mode overrides */
body.dark-mode {
    --gray-light: #0f1720;
    --black-primary: #e6eef6;
    --black-secondary: #c7d7e8;
}
body.dark-mode .content-wrapper { background: #071019; }
body.dark-mode .card { background: #0b1a24; color:var(--black-primary); }
body.dark-mode .info-box { background: #07131a; }
body.dark-mode .table tbody tr:hover { background: rgba(255,107,53,0.06); }
body.dark-mode .main-sidebar { filter: brightness(1.05); }

/* Responsive tweaks */
@media (max-width: 768px) {
    .info-box { flex-direction:row; }
}
</style>
@endsection

@section('content')

<!-- Dark mode toggle small control -->
<div class="d-flex justify-content-end mb-2">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="darkModeToggle">
        <label class="form-check-label small text-muted" for="darkModeToggle">Dark mode</label>
    </div>
</div>

<div class="row">
    <!-- Info Boxes -->
    @php
        $infoBoxes = [
            ['icon' => 'fas fa-shopping-cart', 'text' => 'คำสั่งซื้อทั้งหมด', 'number' => $totalOrders, 'bg' => 'bg-gradient-orange'],
            ['icon' => 'fas fa-dollar-sign', 'text' => 'ยอดขาย', 'number' => $totalRevenue, 'bg' => 'bg-gradient-orange', 'prefix' => '฿'],
            ['icon' => 'fas fa-box', 'text' => 'สินค้าทั้งหมด', 'number' => $totalProducts, 'bg' => 'bg-gradient-orange'],
            ['icon' => 'fas fa-users', 'text' => 'สมาชิก', 'number' => $totalUsers, 'bg' => 'bg-gradient-orange'],
        ];
    @endphp

    @foreach($infoBoxes as $box)
    <div class="col-12 col-sm-6 col-md-3 mb-3">
        <div class="info-box">
            <span class="info-box-icon {{ $box['bg'] }}"><i class="{{ $box['icon'] }}"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ $box['text'] }}</span>
                <span class="info-box-number">{{ $box['prefix'] ?? '' }}{{ number_format($box['number']) }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Charts & Order status -->
<div class="row mt-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-chart-line text-orange mr-2"></i> สถิติยอดขาย</h3>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-sm btn-outline-secondary" id="yearPrev">Prev</button>
                    <button class="btn btn-sm btn-outline-secondary" id="yearNext">Next</button>
                </div>
            </div>
            <div class="card-body">
                <div style="height:320px;">
                    <canvas id="sales-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shopping-bag text-orange mr-2"></i> สรุปสถานะคำสั่งซื้อ</h3>
            </div>
            <div class="card-body">
                @php
                    $statusList = [
                        'completed' => ['icon'=>'fas fa-check-circle','text'=>'สำเร็จ'],
                        'pending' => ['icon'=>'fas fa-clock','text'=>'รอดำเนินการ'],
                        'shipping' => ['icon'=>'fas fa-shipping-fast','text'=>'กำลังจัดส่ง'],
                        'cancelled' => ['icon'=>'fas fa-times-circle','text'=>'ยกเลิก'],
                    ];
                @endphp
                @foreach($statusList as $key => $status)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="{{ $status['icon'] }} text-orange"></i>
                        <span>{{ $status['text'] }}</span>
                    </div>
                    <span class="badge badge-orange">{{ $ordersByStatus[$key] ?? 0 }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-list text-orange mr-2"></i> คำสั่งซื้อล่าสุด</h3>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
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
                            @php $s = $statusList[$order->status] ?? ['icon'=>'fas fa-question','text'=>$order->status]; @endphp
                            <tr>
                                <td>#{{ $order->order_number ?? 'ORD-'.str_pad($order->id,3,'0',STR_PAD_LEFT) }}</td>
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
                                <td><span class="badge badge-orange">{{ $s['text'] }}</span></td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">ยังไม่มีคำสั่งซื้อ</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top products + Quick actions condensed (optional) -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">สินค้าขายดี</h3></div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($topProducts as $product)
                    <li class="list-group-item d-flex align-items-center">
                        <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/50' }}" width="50" class="rounded me-3" alt="{{ $product->name }}">
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ Str::limit($product->name, 40) }}</div>
                            <div class="text-muted small">ขายแล้ว {{ number_format($product->total_sold ?? 0) }} ชิ้น</div>
                        </div>
                        <div class="ms-3"><span class="badge badge-orange">฿{{ number_format($product->price) }}</span></div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted">ไม่มีข้อมูล</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h3 class="card-title">เมนูด่วน</h3></div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="row w-100 g-2">
                    <div class="col-6"><a href="{{ route('admin.products.index') }}" class="btn btn-app w-100"><i class="fas fa-plus fa-2x mb-1"></i><div>เพิ่มสินค้า</div></a></div>
                    <div class="col-6"><a href="/orders" class="btn btn-app w-100"><i class="fas fa-list fa-2x mb-1"></i><div>คำสั่งซื้อ</div></a></div>
                    <div class="col-6"><a href="/customers" class="btn btn-app w-100"><i class="fas fa-users fa-2x mb-1"></i><div>ลูกค้า</div></a></div>
                    <div class="col-6"><a href="/reports" class="btn btn-app w-100"><i class="fas fa-chart-bar fa-2x mb-1"></i><div>รายงาน</div></a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">กรองคำสั่งซื้อ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="filterForm">
            <div class="mb-3">
                <label for="statusFilter" class="form-label">สถานะ</label>
                <select id="statusFilter" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="completed">สำเร็จ</option>
                    <option value="pending">รอดำเนินการ</option>
                    <option value="shipping">กำลังจัดส่ง</option>
                    <option value="cancelled">ยกเลิก</option>
                </select>
            </div>
            <div class="row g-2">
                <div class="col-6"><label class="form-label">จากวันที่</label><input type="date" class="form-control" id="fromDate"></div>
                <div class="col-6"><label class="form-label">ถึงวันที่</label><input type="date" class="form-control" id="toDate"></div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="button" id="applyFilter" class="btn btn-orange">นำกรอง</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ---------- Dark mode toggle (localStorage + body class) ----------
(function(){
    const toggle = document.getElementById('darkModeToggle');
    const darkKey = 'admin_dark_mode_v1';
    const apply = (isDark) => document.body.classList.toggle('dark-mode', isDark);

    // init state
    const saved = localStorage.getItem(darkKey);
    const isDark = saved === '1' ? true : false;
    apply(isDark);
    if(toggle) toggle.checked = isDark;

    toggle?.addEventListener('change', (e)=>{
        const val = e.target.checked;
        apply(val);
        localStorage.setItem(darkKey, val ? '1' : '0');
    });
})();

// ---------- Chart.js with theme-aware colors ----------
(function(){
    const ctx = document.getElementById('sales-chart');
    if(!ctx) return;

    const getColors = () => {
        const dark = document.body.classList.contains('dark-mode');
        return {
            primary: dark ? '#ff8c5f' : '#ff6b35',
            primaryAlpha: dark ? 'rgba(255,140,95,0.15)' : 'rgba(255,107,53,0.18)',
            grid: dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)',
            text: dark ? '#e6eef6' : '#222'
        }
    }

    const cfg = (colors) => ({
        type:'line',
        data: {
            labels: ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'],
            datasets: [
                { label:'ปีนี้', data: @json($salesData['currentYear'] ?? array_fill(0,12,0)), borderColor: colors.primary, backgroundColor: colors.primaryAlpha, tension:0.35, pointRadius:3, fill:true },
                { label:'ปีที่แล้ว', data: @json($salesData['lastYear'] ?? array_fill(0,12,0)), borderColor: '#cfd6db', backgroundColor:'rgba(207,214,219,0.12)', tension:0.35, pointRadius:3, fill:true }
            ]
        },
        options: {
            responsive:true,
            maintainAspectRatio:false,
            plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ label:function(ctx){ return '฿'+Number(ctx.raw).toLocaleString(); } } } },
            scales:{ x:{ grid:{ color: colors.grid }, ticks:{ color: colors.text } }, y:{ grid:{ color: colors.grid }, ticks:{ color: colors.text, callback:function(v){ return '฿'+Number(v).toLocaleString(); } } } }
        }
    });

    let chart = new Chart(ctx, cfg(getColors()));

    // re-render on dark-mode toggle
    const observer = new MutationObserver(()=>{
        const colors = getColors();
        chart.options.scales.x.ticks.color = colors.text;
        chart.options.scales.y.ticks.color = colors.text;
        chart.data.datasets[0].borderColor = colors.primary;
        chart.data.datasets[0].backgroundColor = colors.primaryAlpha;
        chart.options.scales.x.grid.color = colors.grid;
        chart.options.scales.y.grid.color = colors.grid;
        chart.update();
    });
    observer.observe(document.body, { attributes:true, attributeFilter:['class'] });
})();

// ---------- Modal filter apply (example stub) ----------
document.getElementById('applyFilter')?.addEventListener('click', function(){
    const status = document.getElementById('statusFilter')?.value;
    const from = document.getElementById('fromDate')?.value;
    const to = document.getElementById('toDate')?.value;
    // TODO: call backend with fetch/AJAX to filter. For now just close modal.
    const bsModal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
    bsModal?.hide();
    // Example: show toast
    showToast('ตัวกรองถูกนำไปใช้');
});

// ---------- Simple toast (notification) ----------
function showToast(message){
    const t = document.createElement('div');
    t.className = 'toast-custom';
    t.style.position = 'fixed';
    t.style.right = '20px';
    t.style.bottom = '20px';
    t.style.zIndex = 2000;
    t.innerText = message;
    document.body.appendChild(t);
    setTimeout(()=>{ t.style.opacity=0; t.style.transition='opacity 400ms'; setTimeout(()=>t.remove(),500); }, 3000);
}

// ---------- Lightweight helpers for sidebar theme (if AdminLTE toggles classes) ----------
// (Keep for future use)
</script>
@endsection

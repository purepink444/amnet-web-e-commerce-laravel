{{-- resources/views/partials/admin-header.blade.php --}}

<nav class="navbar navbar-expand-lg navbar-dark admin-navbar">
    <div class="container-fluid px-4">
        <!-- Logo & Brand -->
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-shield-alt me-2"></i>
            <span class="brand-text">Admin Panel</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNavbar">
            <!-- Main Navigation -->
            <ul class="navbar-nav me-auto ms-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-chart-line me-1"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" 
                       href="{{ route('admin.products.index') }}">
                        <i class="fas fa-box me-1"></i>
                        <span>สินค้า</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" 
                       href="{{ Route::has('admin.orders.index') ? route('admin.orders.index') : '#' }}">
                        <i class="fas fa-shopping-cart me-1"></i>
                        <span>คำสั่งซื้อ</span>
                        @if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                            <span class="badge bg-danger ms-1">{{ $pendingOrdersCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                       href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}">
                        <i class="fas fa-users me-1"></i>
                        <span>ลูกค้า</span>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="reportsMenu" data-bs-toggle="dropdown">
                        <i class="fas fa-chart-bar me-1"></i>
                        <span>รายงาน</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li>
                            <a class="dropdown-item" href="{{ Route::has('admin.reports.sales') ? route('admin.reports.sales') : '#' }}">
                                <i class="fas fa-dollar-sign me-2"></i>รายงานยอดขาย
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ Route::has('admin.reports.products') ? route('admin.reports.products') : '#' }}">
                                <i class="fas fa-box-open me-2"></i>รายงานสินค้า
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ Route::has('admin.reports.customers') ? route('admin.reports.customers') : '#' }}">
                                <i class="fas fa-user-chart me-2"></i>รายงานลูกค้า
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- Right Side Navigation -->
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <!-- Quick Add Product -->
                <li class="nav-item">
                    <a class="btn btn-sm btn-light text-dark fw-semibold px-3 me-2" 
                       href="{{ route('admin.products.create') }}">
                        <i class="fas fa-plus me-1"></i>เพิ่มสินค้า
                    </a>
                </li>

                <!-- Notifications -->
                <li class="nav-item dropdown me-2">
                    <a class="nav-link position-relative" href="#" id="notifMenu" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fs-5"></i>
                        @if(isset($notificationsCount) && $notificationsCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $notificationsCount > 9 ? '9+' : $notificationsCount }}
                            </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark notifications-dropdown">
                        <li class="dropdown-header">
                            <i class="fas fa-bell me-2"></i>การแจ้งเตือน
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-shopping-cart text-warning me-2"></i>
                                <div class="d-inline-block">
                                    <div class="fw-semibold">คำสั่งซื้อใหม่</div>
                                    <small class="text-muted">5 นาทีที่แล้ว</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-box text-info me-2"></i>
                                <div class="d-inline-block">
                                    <div class="fw-semibold">สินค้าใกล้หมด</div>
                                    <small class="text-muted">1 ชั่วโมงที่แล้ว</small>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center text-primary" href="#">
                                ดูทั้งหมด
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- View Site -->
                <li class="nav-item me-2">
                    <a class="nav-link" href="{{ route('home') }}" target="_blank" title="ดูหน้าเว็บไซต์">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </li>

                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="d-none d-lg-block">
                            <div class="fw-semibold">{{ auth()->user()->username ?? 'Admin' }}</div>
                            <small class="text-light opacity-75">{{ auth()->user()->role?->role_name ?? 'Administrator' }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                        <li class="dropdown-header">
                            <i class="fas fa-user-circle me-2"></i>
                            {{ auth()->user()->email }}
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('account.profile') }}">
                                <i class="fas fa-user me-2"></i>โปรไฟล์
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('account.settings') }}">
                                <i class="fas fa-cog me-2"></i>ตั้งค่า
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">
                                    <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
:root {
    --admin-orange: #ff6b35;
    --admin-orange-dark: #e85d2a;
    --admin-black: #1a1a1a;
    --admin-black-light: #2d2d2d;
}

.admin-navbar {
    background: linear-gradient(135deg, var(--admin-black) 0%, var(--admin-black-light) 100%);
    box-shadow: 0 2px 15px rgba(0,0,0,0.3);
    padding: 0.8rem 0;
    border-bottom: 3px solid var(--admin-orange);
}

.navbar-brand {
    color: #fff !important;
    transition: all 0.3s ease;
}

.navbar-brand:hover {
    transform: scale(1.05);
}

.brand-text {
    background: linear-gradient(135deg, var(--admin-orange), #ffa366);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.nav-link {
    color: rgba(255,255,255,0.85) !important;
    transition: all 0.3s ease;
    border-radius: 8px;
    padding: 0.5rem 1rem !important;
    margin: 0 2px;
    position: relative;
}

.nav-link:hover {
    background-color: rgba(255,107,53,0.15);
    color: #fff !important;
    transform: translateY(-2px);
}

.nav-link.active {
    background-color: var(--admin-orange);
    color: #fff !important;
    font-weight: 600;
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -0.8rem;
    left: 50%;
    transform: translateX(-50%);
    width: 6px;
    height: 6px;
    background: var(--admin-orange);
    border-radius: 50%;
}

.user-avatar {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, var(--admin-orange), var(--admin-orange-dark));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.dropdown-menu-dark {
    background-color: var(--admin-black-light);
    border: 1px solid rgba(255,107,53,0.3);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.dropdown-menu-dark .dropdown-item {
    color: rgba(255,255,255,0.85);
    transition: all 0.2s ease;
}

.dropdown-menu-dark .dropdown-item:hover {
    background-color: rgba(255,107,53,0.2);
    color: #fff;
}

.dropdown-menu-dark .dropdown-item.text-danger:hover {
    background-color: rgba(220,53,69,0.2);
}

.notifications-dropdown {
    min-width: 320px;
    max-height: 400px;
    overflow-y: auto;
}

.notifications-dropdown .dropdown-item {
    padding: 0.8rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.badge {
    font-size: 0.7rem;
    padding: 0.3rem 0.5rem;
}

/* Responsive */
@media (max-width: 991px) {
    .admin-navbar {
        padding: 1rem 0;
    }
    
    .navbar-nav {
        padding: 1rem 0;
    }
    
    .nav-link {
        padding: 0.7rem 1rem !important;
        margin: 0.2rem 0;
    }
    
    .user-avatar {
        width: 30px;
        height: 30px;
    }
}

/* Scrollbar for notifications */
.notifications-dropdown::-webkit-scrollbar {
    width: 6px;
}

.notifications-dropdown::-webkit-scrollbar-track {
    background: var(--admin-black);
}

.notifications-dropdown::-webkit-scrollbar-thumb {
    background: var(--admin-orange);
    border-radius: 3px;
}
</style>
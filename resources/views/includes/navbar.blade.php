<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #ff6b35 0%, #e85d2a 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold fs-4" href="{{ url('/') }}">
            <i class="bi bi-shop me-2"></i>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">

                <!-- Public Links -->
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="{{ url('/') }}">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="{{ url('/product') }}">
                        <i class="bi bi-bag me-1"></i>Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="{{ url('/news') }}">
                        <i class="bi bi-newspaper me-1"></i>News
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="{{ url('/support') }}">
                        <i class="bi bi-headset me-1"></i>Support
                    </a>
                </li>

                <!-- Guest Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold px-3 btn-register" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold px-3" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                @endguest

                <!-- Authenticated Links -->
                @auth
                    @php $user = auth()->user(); @endphp

                    <!-- Admin Menu -->
                    @if($user->role_id == 1)
                        <li class="nav-item">
                            <a class="nav-link text-white fw-semibold px-3" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i>Admin Dashboard
                            </a>
                        </li>
                    @endif

                    <!-- Member/User Menu -->
                    @if($user->role_id == 2)
                        <li class="nav-item">
                            <a class="nav-link text-white fw-semibold px-3" href="{{ route('account.orders.index') }}">
                                <i class="bi bi-bag-check me-1"></i>My Orders
                            </a>
                        </li>
                    @endif
                @endauth

                <!-- Language -->
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="{{ url('/language') }}">
                        <i class="bi bi-globe me-1"></i>ภาษา
                    </a>
                </li>

            </ul>
        </div>


        <!-- Cart Icon (for authenticated users) -->
        @auth
            <ul class="navbar-nav me-3">
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3 position-relative" href="{{ route('account.cart.index') }}">
                        <i class="bi bi-cart3 me-1"></i>
                        <span class="visually-hidden">ตะกร้าสินค้า</span>
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle" id="cart-counter">
                            @php
                                $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
                                echo $cartCount ?: 0;
                            @endphp
                        </span>
                    </a>
                </li>
            </ul>
        @endauth

        <!-- User Menu (always visible) -->
        <ul class="navbar-nav">
            @auth
                @php $user = auth()->user(); @endphp
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white fw-semibold px-3" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>{{ $user->username }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        @if($user->role_id == 1)
                            <!-- Admin Options -->
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.products.index') }}"><i class="bi bi-box-seam me-2"></i>จัดการสินค้า</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>จัดการผู้ใช้</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i class="bi bi-receipt me-2"></i>จัดการคำสั่งซื้อ</a></li>
                        @else
                            <!-- Member Options -->
                            <li><a class="dropdown-item" href="{{ route('account.profile') }}"><i class="bi bi-person me-2"></i>โปรไฟล์</a></li>
                            <li><a class="dropdown-item" href="{{ route('account.orders.index') }}"><i class="bi bi-bag-check me-2"></i>คำสั่งซื้อของฉัน</a></li>
                            <li><a class="dropdown-item" href="{{ route('account.wishlist') }}"><i class="bi bi-heart me-2"></i>สินค้าที่ชอบ</a></li>
                            <li><a class="dropdown-item" href="{{ route('account.settings') }}"><i class="bi bi-gear me-2"></i>ตั้งค่า</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('account.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</button>
                            </form>
                        </li>
                    </ul>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="{{ route('login') }}">
                        <i class="bi bi-person-circle me-1"></i>เข้าสู่ระบบ
                    </a>
                </li>
            @endauth
        </ul>
    </div>
</nav>

<style>
.navbar { padding: 0.8rem 0; margin: 0 !important; width: 100%; }
.navbar-brand { color: #fff !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.2); transition: all 0.3s ease; }
.navbar-brand:hover { transform: scale(1.05); }
.nav-link { transition: all 0.3s ease; border-radius: 8px; margin: 0 2px; }
.nav-link:hover { background-color: rgba(26,26,26,0.3); transform: translateY(-2px); }
.btn-register { background-color: rgba(26,26,26,0.4); border-radius: 8px; }
.btn-register:hover { background-color: #1a1a1a; }
.dropdown-menu {
    z-index: 1050;
}
@media (max-width: 991px) {
    .navbar-nav { padding: 1rem 0; }
    .nav-link { padding: 0.5rem 1rem !important; margin: 0.2rem 0; }
}
</style>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE CSS removed - using custom styling -->

    <!-- Google Fonts - Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')

</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Mobile sidebar backdrop -->
    <div class="sidebar-backdrop"></div>

    <!-- Mobile Quick Menu -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
    <div class="mobile-quick-menu" id="mobileQuickMenu">
        <div class="menu-header">
            <h6 class="mb-0">
                <i class="fas fa-bars me-2"></i>เมนูหลัก
            </h6>
        </div>
        <div class="menu-body">
            <a href="{{ route('admin.dashboard') }}" class="quick-link">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="quick-link">
                <i class="fas fa-box-seam"></i>จัดการสินค้า
            </a>
            <a href="{{ route('admin.categories.index') }}" class="quick-link">
                <i class="fas fa-tags"></i>จัดการหมวดหมู่
            </a>
            <a href="{{ route('admin.brands.index') }}" class="quick-link">
                <i class="fas fa-copyright"></i>จัดการแบรนด์
            </a>
            <a href="{{ route('admin.orders.index') }}" class="quick-link">
                <i class="fas fa-receipt"></i>จัดการคำสั่งซื้อ
            </a>
            <a href="{{ route('admin.users.index') }}" class="quick-link">
                <i class="fas fa-users"></i>จัดการผู้ใช้
            </a>
            <a href="{{ route('admin.reports.index') }}" class="quick-link">
                <i class="fas fa-chart-line"></i>รายงาน
            </a>
            <hr class="my-2">
            <a href="{{ route('home') }}" class="quick-link">
                <i class="fas fa-home"></i>กลับหน้าหลัก
            </a>
            <form action="{{ route('account.logout') }}" method="POST" class="d-inline w-100">
                @csrf
                <button type="submit" class="quick-link w-100 text-start border-0 bg-transparent">
                    <i class="fas fa-sign-out-alt"></i>ออกจากระบบ
                </button>
            </form>
        </div>
    </div>

    <div class="wrapper">
        {{-- Navbar - Mobile Optimized --}}
        <nav class="main-header navbar navbar-expand">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="เปิดเมนู">
                        <i class="fas fa-bars text-white"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <!-- Mobile Menu Toggle for Small Screens -->
                <li class="nav-item d-lg-none">
                    <a class="nav-link" href="#" onclick="toggleMobileMenu()" aria-label="เมนูหลัก">
                        <i class="fas fa-ellipsis-v text-white"></i>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="userDropdown"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="เมนูผู้ใช้">
                        <i class="fas fa-user me-1"></i>
                        <span class="d-none d-sm-inline">{{ auth()->user()->username }}</span>
                        <span class="d-sm-none">ผู้ใช้</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box-seam me-2"></i>จัดการสินค้า
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users me-2"></i>จัดการผู้ใช้
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-receipt me-2"></i>จัดการคำสั่งซื้อ
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('account.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="dropdown-item" type="submit">
                                    <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        {{-- Sidebar - Mobile Optimized --}}
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('admin.dashboard') }}" class="brand-link d-flex align-items-center">
                <span class="brand-text font-weight-light ms-2">Admin Panel</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p class="mb-0">Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-box-seam"></i>
                                <p class="mb-0">จัดการสินค้า</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p class="mb-0">จัดการหมวดหมู่</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-copyright"></i>
                                <p class="mb-0">จัดการแบรนด์</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p class="mb-0">จัดการคำสั่งซื้อ</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p class="mb-0">จัดการผู้ใช้</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p class="mb-0">รายงาน</p>
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a href="{{ route('home') }}" class="nav-link">
                                <i class="nav-icon fas fa-home"></i>
                                <p class="mb-0">กลับหน้าหลัก</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Content Wrapper --}}
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title', 'Dashboard')</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            {{ session('error') }}
                        </div>
                    @endif
                    @yield('content')
                </div>
            </section>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS removed - using custom sidebar functionality -->

    {{-- SweetAlert2 Integration --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check for SweetAlert configuration from server
        @if(session('sweetalert'))
            const config = @json(session('sweetalert'));

            // Handle auto-redirect
            if (config.redirect) {
                Swal.fire(config).then(() => {
                    window.location.href = config.redirect;
                });
            } else {
                Swal.fire(config);
            }
        @endif

        // Enhanced delete confirmations
        document.querySelectorAll('[data-confirm-delete]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const title = this.getAttribute('data-confirm-title') || 'คุณแน่ใจหรือไม่?';
                const text = this.getAttribute('data-confirm-text') || 'การดำเนินการนี้ไม่สามารถยกเลิกได้';
                const form = this.closest('form');

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed && form) {
                        form.submit();
                    }
                });
            });
        });

        // Auto-hide Bootstrap alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            if (!alert.classList.contains('alert-permanent')) {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            }
        });

        // Custom Sidebar Toggle Functionality
        function toggleSidebar() {
            const body = document.body;
            const backdrop = document.querySelector('.sidebar-backdrop');

            if (body.classList.contains('sidebar-open')) {
                // Close sidebar
                body.classList.remove('sidebar-open');
                if (backdrop) backdrop.classList.remove('show');
                console.log('Sidebar closed');
            } else {
                // Open sidebar
                body.classList.add('sidebar-open');
                if ($(window).width() <= 768 && backdrop) {
                    backdrop.classList.add('show');
                }
                console.log('Sidebar opened');
            }
        }

        // Handle hamburger menu click - multiple binding methods for reliability
        $('[data-widget="pushmenu"]').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Hamburger clicked (jQuery)!');
            toggleSidebar();
            return false;
        });

        // Direct binding as backup
        document.querySelector('[data-widget="pushmenu"]').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Hamburger clicked (direct)!');
            toggleSidebar();
        });

        // Global click handler as final backup
        document.addEventListener('click', function(e) {
            const hamburgerBtn = e.target.closest('[data-widget="pushmenu"]');
            if (hamburgerBtn) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Hamburger clicked (global)!');
                toggleSidebar();
            }
        });

        // Handle mobile backdrop clicks
        $('.sidebar-backdrop').on('click', function() {
            if ($(window).width() <= 768) {
                toggleSidebar();
            }
        });

        // Handle window resize
        $(window).on('resize', function() {
            if ($(window).width() > 768) {
                // Reset mobile state on desktop
                $('body').removeClass('sidebar-open');
                $('.sidebar-backdrop').removeClass('show');
                closeMobileMenu();
            }
        });

        // Mobile Menu Functions
        function toggleMobileMenu() {
            const overlay = document.getElementById('mobileMenuOverlay');
            const menu = document.getElementById('mobileQuickMenu');

            if (menu.classList.contains('show')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        }

        function openMobileMenu() {
            const overlay = document.getElementById('mobileMenuOverlay');
            const menu = document.getElementById('mobileQuickMenu');

            overlay.classList.add('show');
            menu.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            const overlay = document.getElementById('mobileMenuOverlay');
            const menu = document.getElementById('mobileQuickMenu');

            overlay.classList.remove('show');
            menu.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Close mobile menu when clicking overlay
        document.getElementById('mobileMenuOverlay').addEventListener('click', closeMobileMenu);

        // Close mobile menu when clicking menu links
        document.querySelectorAll('.mobile-quick-menu .quick-link').forEach(link => {
            link.addEventListener('click', function() {
                // Only close if it's not a form submit button
                if (!this.hasAttribute('type') || this.getAttribute('type') !== 'submit') {
                    closeMobileMenu();
                }
            });
        });

        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });
    });
</script>

    @yield('scripts')
</body>
</html>

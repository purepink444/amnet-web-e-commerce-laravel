<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')

    <style>
        /* Aggressive reset for AdminLTE */
        * {
            box-sizing: border-box;
        }

        html, body, #app, .app, .wrapper, .layout-fixed {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            min-height: 100vh;
        }

        /* AdminLTE specific resets */
        .hold-transition, .sidebar-mini, .layout-fixed {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Custom AdminLTE Orange Theme */
        :root {
            --admin-orange: #ff6b35;
            --admin-orange-dark: #e85d2a;
            --admin-orange-light: #ff8c5f;
        }

        /* Navbar - Completely Stable */
        .main-header.navbar {
            background: linear-gradient(135deg, var(--admin-orange) 0%, var(--admin-orange-dark) 100%) !important;
            box-shadow: 0 2px 10px rgba(255, 107, 53, 0.3);
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 1035 !important;
            height: 57px !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;
            transition: none !important;
            will-change: auto !important;
            backface-visibility: hidden !important;
        }

        /* Ensure navbar stays above sidebar */
        .main-header.navbar {
            z-index: 1035 !important;
        }

        /* Prevent any layout shifts that could affect navbar */
        .main-header.navbar * {
            transform: none !important;
            transition: none !important;
        }

        .main-header .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            padding: 0.5rem 1rem;
        }

        .main-header .navbar-nav .nav-link:hover {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
        }

        .main-header .navbar-nav .nav-link i {
            font-size: 1.1rem;
        }

        /* Sidebar */
        .main-sidebar {
            background: linear-gradient(180deg, var(--admin-orange-dark) 0%, var(--admin-orange) 100%);
            box-shadow: 2px 0 10px rgba(255, 107, 53, 0.2);
            position: fixed !important;
            top: 57px !important;
            left: 0 !important;
            height: calc(100vh - 57px) !important;
            width: 250px !important;
            z-index: 1000 !important;
            transition: transform 0.3s ease !important;
            /* Default hidden state for hover-to-show */
            transform: translateX(-250px) !important;
        }

        /* Expanded state on hover */
        .main-sidebar.sidebar-expanded {
            transform: translateX(0) !important;
        }

        /* Ensure sidebar stays below navbar */
        .main-sidebar {
            z-index: 1000 !important;
        }

        .brand-link {
            background: var(--admin-orange) !important;
            color: #fff !important;
            border-bottom: none !important;
        }

        .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            border-radius: 8px;
            margin: 2px 8px;
            transition: all 0.3s ease;
        }

        .nav-sidebar .nav-link:hover {
            background-color: rgba(255, 107, 53, 0.2) !important;
            color: #fff !important;
            transform: translateX(5px);
        }

        .nav-sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--admin-orange) 0%, var(--admin-orange-dark) 100%) !important;
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
        }

        /* Wrapper and body fixes */
        .wrapper {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            overflow-x: hidden;
        }

        /* Content */
        .content-wrapper {
            background-color: #f4f6f9;
            margin-top: 57px !important;
            min-height: calc(100vh - 57px);
            margin-left: 0 !important;
            transition: margin-left 0.3s ease !important;
            position: relative !important;
            z-index: 800 !important;
        }

        /* Content stays in place for hover-to-show (no shifting) */
        .content-wrapper {
            margin-left: 0 !important;
        }

        .content-header {
            padding: 1.5rem 0 1rem 0;
        }

        .content-header h1 {
            color: #2d3748;
            font-weight: 700;
            margin: 0;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-bottom: 2px solid var(--admin-orange);
            border-radius: 15px 15px 0 0 !important;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--admin-orange) 0%, var(--admin-orange-dark) 100%);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--admin-orange-dark) 0%, var(--admin-orange) 100%);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }

        /* Small boxes */
        .small-box {
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .small-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .small-box .inner h3 {
            font-size: 2.5rem;
            font-weight: 700;
        }

        /* Tables */
        .table thead th {
            background: linear-gradient(135deg, var(--admin-orange) 0%, var(--admin-orange-dark) 100%);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr:hover {
            background-color: rgba(255, 107, 53, 0.05);
        }

        /* Alerts */
        .alert-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            border: none;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ff6b35 0%, #e85d2a 100%);
            color: white;
            border: none;
        }

        /* Form controls */
        .form-control:focus {
            border-color: var(--admin-orange);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }

        /* Hover Sidebar Functionality */
        .sidebar-hover-zone {
            position: fixed;
            top: 57px;
            left: 0;
            width: 15px;
            height: calc(100vh - 57px);
            z-index: 999;
            background: transparent;
            cursor: pointer;
        }

        /* Mobile sidebar backdrop */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        /* Ensure proper z-index hierarchy */
        .sidebar-backdrop {
            z-index: 1040 !important;
        }

        .sidebar-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile override */
        @media (max-width: 768px) {
            .sidebar-mini.sidebar-collapse .main-sidebar {
                transform: translateX(-100%);
            }

            .sidebar-mini.sidebar-collapse .main-sidebar.sidebar-open {
                transform: translateX(0);
            }
        }

        /* AdminLTE overrides */
        .layout-fixed .wrapper,
        .layout-fixed .wrapper > .content-wrapper,
        .layout-fixed .wrapper > .content-wrapper > .content,
        .layout-fixed .wrapper > .content-wrapper > .content > .container-fluid {
            margin: 0 !important;
            padding: 0 !important;
        }

        body.hold-transition {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Content area specific */
        .content {
            padding: 0 !important;
            margin: 0 !important;
        }

        .content-header {
            padding: 1.5rem 1rem 1rem 1rem !important;
            margin: 0 !important;
        }

        /* Bootstrap container overrides */
        .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            margin: 0 !important;
        }

        /* Page content padding */
        .content > .container-fluid {
            padding: 1rem !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar-hover-zone {
                display: none;
            }

            /* Mobile sidebar behavior - let AdminLTE handle it */
            .sidebar-open .main-sidebar {
                transform: translateX(0) !important;
            }

            /* Ensure proper overlay on mobile */
            .main-sidebar {
                box-shadow: 2px 0 20px rgba(0,0,0,0.3) !important;
                z-index: 1050 !important;
            }

            .content-wrapper {
                z-index: 800 !important;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="sidebar-hover-zone"></div>
    <!-- Mobile sidebar backdrop -->
    <div class="sidebar-backdrop"></div>
    <div class="wrapper">
        {{-- Navbar --}}
        <nav class="main-header navbar navbar-expand">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars text-white"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user me-1"></i>{{ auth()->user()->username }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.products.index') }}"><i class="fas fa-box-seam me-2"></i>จัดการสินค้า</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.users.index') }}"><i class="fas fa-people me-2"></i>จัดการผู้ใช้</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i class="fas fa-receipt me-2"></i>จัดการคำสั่งซื้อ</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="dropdown-item" type="submit">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        {{-- Sidebar --}}
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">Admin Panel</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-box-seam"></i>
                                <p>จัดการสินค้า</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>จัดการหมวดหมู่</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-copyright"></i>
                                <p>จัดการแบรนด์</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p>จัดการคำสั่งซื้อ</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>จัดการผู้ใช้</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>รายงาน</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link">
                                <i class="nav-icon fas fa-home"></i>
                                <p>กลับหน้าหลัก</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.main-sidebar');
            const hoverZone = document.querySelector('.sidebar-hover-zone');
            const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
            const body = document.body;
            let hoverTimeout;

            // Hover-to-show functionality (desktop only)
            if (window.innerWidth > 768) {
                function expandSidebar() {
                    clearTimeout(hoverTimeout);
                    sidebar.classList.add('sidebar-expanded');
                    body.classList.add('sidebar-expanded');
                }

                function collapseSidebar() {
                    hoverTimeout = setTimeout(() => {
                        if (!sidebar.matches(':hover') && !hoverZone.matches(':hover')) {
                            sidebar.classList.remove('sidebar-expanded');
                            body.classList.remove('sidebar-expanded');
                        }
                    }, 300);
                }

                // Hover zone events
                if (hoverZone) {
                    hoverZone.addEventListener('mouseenter', expandSidebar);
                    hoverZone.addEventListener('mouseleave', collapseSidebar);
                }

                // Sidebar events
                if (sidebar) {
                    sidebar.addEventListener('mouseenter', expandSidebar);
                    sidebar.addEventListener('mouseleave', collapseSidebar);
                }
            }

            // Handle mobile backdrop clicks
            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', function() {
                    // Close sidebar by triggering AdminLTE's close mechanism
                    const pushMenuBtn = document.querySelector('[data-widget="pushmenu"]');
                    if (pushMenuBtn && window.innerWidth <= 768) {
                        // Simulate click to close
                        document.body.classList.remove('sidebar-open');
                        sidebarBackdrop.classList.remove('show');
                    }
                });
            }

            // Listen for AdminLTE sidebar events and manage expanded state
            $(document).on('collapsed.lte.pushmenu', function() {
                body.classList.remove('sidebar-expanded');
                sidebarBackdrop.classList.remove('show');
            });

            $(document).on('shown.lte.pushmenu', function() {
                body.classList.add('sidebar-expanded');
                if (window.innerWidth <= 768) {
                    sidebarBackdrop.classList.add('show');
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    // Reset mobile state on desktop
                    document.body.classList.remove('sidebar-open');
                    sidebarBackdrop.classList.remove('show');
                    // Re-enable hover functionality
                    location.reload(); // Simple way to re-initialize hover listeners
                } else {
                    // Disable hover on mobile
                    sidebar.classList.remove('sidebar-expanded');
                    body.classList.remove('sidebar-expanded');
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>

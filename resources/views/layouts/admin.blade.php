<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'ระบบจัดการร้านค้าออนไลน์')">
    <meta name="keywords" content="@yield('meta_keywords', 'admin, dashboard, shop management')">
    <meta name="author" content="Shop Admin">
    <meta name="robots" content="noindex, nofollow">
    
    <title>@yield('title', 'Dashboard') - Admin Panel</title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --orange-primary: #ff6b35;
            --orange-dark: #e85d2a;
            --orange-light: #ff8c5e;
            --black-primary: #1a1a1a;
            --black-secondary: #2d2d2d;
            --black-tertiary: #3d3d3d;
            --transition-speed: 0.3s;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 4.6rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            scroll-behavior: smooth;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }
        
        /* ==================== NAVBAR ==================== */
        .main-header {
            background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%) !important;
            border-bottom: 3px solid var(--black-primary);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .main-header .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            transition: all var(--transition-speed) ease;
        }
        
        .main-header .navbar-nav .nav-link:hover {
            color: #fff !important;
            transform: translateY(-2px);
        }
        
        .navbar-badge {
            font-size: 0.65rem;
            padding: 0.25rem 0.4rem;
        }
        
        /* ==================== SIDEBAR ==================== */
        .main-sidebar {
            background: var(--black-primary) !important;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            transition: width var(--transition-speed) ease, margin var(--transition-speed) ease;
        }
        
        .brand-link {
            background: var(--black-secondary) !important;
            border-bottom: 2px solid var(--orange-primary) !important;
            transition: all var(--transition-speed) ease;
        }
        
        .brand-link:hover {
            background: var(--black-tertiary) !important;
        }
        
        .brand-text {
            color: #fff !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px;
        }
        
        /* User Panel */
        .user-panel {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0.5rem !important;
        }
        
        .user-panel .image i {
            color: var(--orange-primary);
            font-size: 2.2rem;
            transition: all var(--transition-speed) ease;
        }
        
        .user-panel:hover .image i {
            color: var(--orange-light);
            transform: scale(1.1);
        }
        
        .user-panel .info a {
            color: #fff !important;
            font-weight: 500;
        }
        
        /* Sidebar Menu */
        .nav-sidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            border-radius: 8px;
            margin: 0.2rem 0.5rem;
            padding: 0.7rem 1rem;
            transition: all var(--transition-speed) ease;
        }
        
        .nav-sidebar .nav-item .nav-link:hover {
            background-color: var(--black-secondary) !important;
            color: #fff !important;
            transform: translateX(5px);
        }
        
        .nav-sidebar .nav-item .nav-link.active {
            background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%) !important;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(255, 107, 53, 0.3);
        }
        
        .nav-sidebar .nav-item .nav-link .nav-icon {
            margin-right: 0.5rem;
        }
        
        .nav-header {
            color: rgba(255, 255, 255, 0.5) !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 1rem 1rem 0.5rem 1rem;
        }
        
        /* Treeview */
        .nav-treeview .nav-link {
            padding-left: 2.5rem !important;
        }
        
        .nav-treeview .nav-link .nav-icon {
            font-size: 0.7rem;
        }
        
        /* Sidebar Collapse Animation */
        .sidebar-mini-md.sidebar-collapse .main-sidebar {
            width: var(--sidebar-collapsed-width) !important;
        }
        
        .sidebar-mini-md.sidebar-collapse .main-sidebar:hover {
            width: var(--sidebar-width) !important;
        }
        
        .sidebar-mini-md.sidebar-collapse .content-wrapper,
        .sidebar-mini-md.sidebar-collapse .main-footer {
            margin-left: var(--sidebar-collapsed-width) !important;
            transition: margin-left var(--transition-speed) ease;
        }
        
        .sidebar-mini-md.sidebar-collapse .main-sidebar:hover ~ .content-wrapper,
        .sidebar-mini-md.sidebar-collapse .main-sidebar:hover ~ .main-footer {
            margin-left: var(--sidebar-width) !important;
        }
        
        /* ==================== CONTENT WRAPPER ==================== */
        .content-wrapper {
            background: #f4f6f9;
            min-height: calc(100vh - 57px - 60px);
            transition: margin-left var(--transition-speed) ease;
        }
        
        .content-header {
            padding: 1.5rem 1rem;
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 1.5rem;
        }
        
        .content-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--black-primary);
        }
        
        .breadcrumb {
            background: transparent;
            margin-bottom: 0;
            padding: 0;
        }
        
        .breadcrumb-item a {
            color: var(--orange-primary);
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: var(--orange-dark);
            text-decoration: underline;
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
        }
        
        /* ==================== CARDS ==================== */
        .card {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border: none;
            border-radius: 10px;
            transition: all var(--transition-speed) ease;
        }
        
        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        .card-primary.card-outline {
            border-top: 3px solid var(--orange-primary);
        }
        
        /* Info Box */
        .info-box {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }
        
        .info-box-icon {
            background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%) !important;
            border-radius: 10px 0 0 10px;
        }
        
        /* ==================== BUTTONS ==================== */
        .btn-primary {
            background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all var(--transition-speed) ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        /* ==================== DROPDOWN MENU ==================== */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .dropdown-item {
            padding: 0.7rem 1.5rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: var(--orange-primary);
            padding-left: 1.8rem;
        }
        
        .dropdown-item i {
            width: 20px;
            text-align: center;
        }
        
        /* ==================== FOOTER ==================== */
        .main-footer {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #3d3d3d 100%);
            border-top: 3px solid var(--orange-primary);
            margin: 0 !important;
            padding: 0;
            margin-top: auto;
            transition: margin-left var(--transition-speed) ease;
        }
        
        .footer-content {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem 0;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .footer-logo i {
            font-size: 1.5rem;
            color: var(--orange-primary);
        }
        
        .footer-logo h6 {
            margin: 0;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .footer-description {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            margin: 0;
        }
        
        .footer-section-title {
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all var(--transition-speed) ease;
            display: block;
            padding: 0.3rem 0;
        }
        
        .footer-link:hover {
            color: var(--orange-primary);
            padding-left: 0.5rem;
            text-decoration: none;
        }
        
        .footer-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }
        
        .footer-bottom {
            background-color: rgba(0, 0, 0, 0.3);
            padding: 1rem 0;
        }
        
        .footer-bottom-text {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            margin: 0;
        }
        
        .footer-bottom-text a {
            color: var(--orange-primary);
            text-decoration: none;
        }
        
        .footer-bottom-text a:hover {
            color: var(--orange-light);
            text-decoration: underline;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .social-link {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.7);
            transition: all var(--transition-speed) ease;
            text-decoration: none;
        }
        
        .social-link:hover {
            background: var(--orange-primary);
            color: #fff;
            transform: translateY(-3px);
        }
        
        /* ==================== ALERTS & FLASH MESSAGES ==================== */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #fff;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: #fff;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #fff;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: #fff;
        }
        
        /* ==================== LOADING OVERLAY ==================== */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity var(--transition-speed) ease;
        }
        
        #page-loader.fade-out {
            opacity: 0;
            pointer-events: none;
        }
        
        .loader-content {
            text-align: center;
        }
        
        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--orange-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        .loader-text {
            color: var(--black-primary);
            font-weight: 500;
            font-size: 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 768px) {
            .content-header h1 {
                font-size: 1.5rem;
            }
            
            .footer-content {
                padding: 1.5rem 0;
            }
            
            .footer-section-title {
                margin-top: 1rem;
            }
            
            .card:hover {
                transform: none;
            }
        }
        
        /* ==================== UTILITY CLASSES ==================== */
        .text-orange {
            color: var(--orange-primary) !important;
        }
        
        .bg-orange {
            background: var(--orange-primary) !important;
        }
        
        .border-orange {
            border-color: var(--orange-primary) !important;
        }
    </style>
    
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini-md layout-fixed">

<!-- Loading Overlay -->
<div id="page-loader">
    <div class="loader-content">
        <div class="loader-spinner"></div>
        <p class="loader-text">กำลังโหลด...</p>
    </div>
</div>

<div class="wrapper">
    <!-- ==================== NAVBAR ==================== -->
    <nav class="main-header navbar navbar-expand navbar-dark">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/" class="nav-link">
                    <i class="bi bi-house me-1"></i>หน้าแรก
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/dashboard" class="nav-link">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                </a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Search -->
            <li class="nav-item">
                <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                    <i class="fas fa-search"></i>
                </a>
                <div class="navbar-search-block">
                    <form class="form-inline">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar" type="search" placeholder="ค้นหา..." aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-navbar" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>
            
            <!-- Notifications -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">3 การแจ้งเตือน</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> ข้อความใหม่ 1 รายการ
                        <span class="float-right text-muted text-sm">3 นาที</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> คำสั่งซื้อใหม่ 2 รายการ
                        <span class="float-right text-muted text-sm">12 นาที</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">ดูทั้งหมด</a>
                </div>
            </li>
            
            <!-- User Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    <span class="d-none d-md-inline ml-1">{{ Auth::user()->firstname }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <div class="dropdown-divider"></div>
                    <a href="/profile" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> โปรไฟล์
                    </a>
                    <a href="/settings" class="dropdown-item">
                        <i class="fas fa-cog mr-2"></i> ตั้งค่า
                    </a>
                    <a href="/help" class="dropdown-item">
                        <i class="fas fa-question-circle mr-2"></i> ช่วยเหลือ
                    </a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i> ออกจากระบบ
                        </button>
                    </form>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="/dashboard" class="brand-link">
            <i class="bi bi-shop brand-image ml-3" style="font-size: 2rem;"></i>
            <span class="brand-text">........</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- User Panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="info">
                    <a href="/profile" class="d-block">
                        {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                    </a>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    
                    <!-- Profile -->
                    <li class="nav-item">
                        <a href="/profile" class="nav-link {{ Request::is('profile') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>โปรไฟล์</p>
                        </a>
                    </li>
                    
                    <!-- Orders -->
                    <li class="nav-item">
                        <a href="/orders" class="nav-link {{ Request::is('orders*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                คำสั่งซื้อ
                                <span class="badge badge-info right">5</span>
                            </p>
                        </a>
                    </li>
                    
                    <!-- Products -->
                    <li class="nav-item {{ Request::is('products*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('products*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-box"></i>
                            <p>
                                สินค้า
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/products" class="nav-link {{ Request::is('products') && !Request::is('products/create') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>รายการสินค้า</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/products/create" class="nav-link {{ Request::is('products/create') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>เพิ่มสินค้า</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/products/categories" class="nav-link {{ Request::is('products/categories') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>หมวดหมู่</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Customers -->
                    <li class="nav-item">
                        <a href="/customers" class="nav-link {{ Request::is('customers*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>ลูกค้า</p>
                        </a>
                    </li>
                    
                    <!-- Reports -->
                    <li class="nav-item">
                        <a href="/reports" class="nav-link {{ Request::is('reports*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>รายงาน</p>
                        </a>
                    </li>
                    
                    <!-- Settings -->
                    <li class="nav-item">
                        <a href="/settings" class="nav-link {{ Request::is('settings*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>ตั้งค่า</p>
                        </a>
                    </li>
                    
                    <li class="nav-header">ระบบ</li>
                    
                    <!-- Help -->
                    <li class="nav-item">
                        <a href="/help" class="nav-link">
                            <i class="nav-icon fas fa-question-circle"></i>
                            <p>ช่วยเหลือ</p>
                        </a>
                    </li>
                    
                    <!-- Logout -->
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-left w-100 text-danger">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>ออกจากระบบ</p>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- ==================== CONTENT WRAPPER ==================== -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/dashboard"><i class="bi bi-house-door"></i> Home</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>สำเร็จ!</strong> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>ผิดพลาด!</strong> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <strong>คำเตือน!</strong> {{ session('warning') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>ข้อมูล:</strong> {{ session('info') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>กรุณาแก้ไขข้อผิดพลาดต่อไปนี้:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <!-- ==================== FOOTER ==================== -->
    <footer class="main-footer">
        <!-- Footer Content -->
        <div class="footer-content">
            <div class="container-fluid px-4">
                <div class="row">
                    <!-- Logo & Description -->
                    <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                        <div class="footer-logo">
                            <i class="bi bi-shop"></i>
                            <h6>Shop Admin</h6>
                        </div>
                        <p class="footer-description">
                            ระบบจัดการร้านค้าออนไลน์ครบวงจร<br>
                            สำหรับผู้ประกอบการยุคใหม่
                        </p>
                        
                        <!-- Social Links -->
                        <div class="social-links mt-3">
                            <a href="#" class="social-link" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="social-link" title="Twitter">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="#" class="social-link" title="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="#" class="social-link" title="Line">
                                <i class="bi bi-line"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                        <h6 class="footer-section-title">เมนูหลัก</h6>
                        <a href="/dashboard" class="footer-link">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a href="/products" class="footer-link">
                            <i class="bi bi-box"></i> สินค้า
                        </a>
                        <a href="/orders" class="footer-link">
                            <i class="bi bi-cart"></i> คำสั่งซื้อ
                        </a>
                        <a href="/customers" class="footer-link">
                            <i class="bi bi-people"></i> ลูกค้า
                        </a>
                        <a href="/reports" class="footer-link">
                            <i class="bi bi-graph-up"></i> รายงาน
                        </a>
                    </div>
                    
                    <!-- Support Links -->
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <h6 class="footer-section-title">การสนับสนุน</h6>
                        <a href="/help" class="footer-link">
                            <i class="bi bi-question-circle"></i> ศูนย์ช่วยเหลือ
                        </a>
                        <a href="/documentation" class="footer-link">
                            <i class="bi bi-file-text"></i> เอกสารคู่มือ
                        </a>
                        <a href="/contact" class="footer-link">
                            <i class="bi bi-envelope"></i> ติดต่อเรา
                        </a>
                        <a href="/faq" class="footer-link">
                            <i class="bi bi-chat-dots"></i> คำถามที่พบบ่อย
                        </a>
                        <a href="/terms" class="footer-link">
                            <i class="bi bi-file-earmark-text"></i> ข้อตกลง
                        </a>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="col-lg-3 col-md-6">
                        <h6 class="footer-section-title">ติดต่อเรา</h6>
                        <div class="footer-link">
                            <i class="bi bi-geo-alt"></i> 
                        </div>
                        <div class="footer-link">
                            <i class="bi bi-telephone"></i> 
                        </div>
                        <div class="footer-link">
                            <i class="bi bi-envelope"></i> 
                        </div>
                        <div class="footer-link">
                            <i class="bi bi-clock"></i> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container-fluid px-4">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="footer-bottom-text">
                            <i class="bi bi-c-circle me-1"></i>2025 <strong>Shop Admin</strong>. All Rights Reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="footer-bottom-text">
                            Made with <i class="bi bi-heart-fill text-danger"></i> in Thailand | 
                            <a href="/privacy">Privacy Policy</a> | 
                            <strong>Version</strong> 1.0.0
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- Custom Admin Scripts -->
<script>
    $(document).ready(function() {
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Hide page loader
        setTimeout(function() {
            $('#page-loader').addClass('fade-out');
            setTimeout(function() {
                $('#page-loader').hide();
            }, 300);
        }, 500);
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
        
        // Tooltip initialization
        $('[data-toggle="tooltip"]').tooltip();
        
        // Popover initialization
        $('[data-toggle="popover"]').popover();
        
        // Sidebar menu state persistence
        var activeMenu = localStorage.getItem('activeMenu');
        if (activeMenu) {
            $('.nav-sidebar .nav-link').removeClass('active');
            $('[href="' + activeMenu + '"]').addClass('active');
            $('[href="' + activeMenu + '"]').closest('.nav-item').addClass('menu-open');
        }
        
        $('.nav-sidebar .nav-link').on('click', function() {
            localStorage.setItem('activeMenu', $(this).attr('href'));
        });
        
        // Confirm before logout
        $('form[action*="logout"] button').on('click', function(e) {
            if (!confirm('คุณต้องการออกจากระบบใช่หรือไม่?')) {
                e.preventDefault();
            }
        });
        
        // Sidebar collapse state persistence
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            $('body').addClass('sidebar-collapse');
        }
        
        $('[data-widget="pushmenu"]').on('collapsed.lte.pushmenu', function() {
            localStorage.setItem('sidebarCollapsed', 'true');
        });
        
        $('[data-widget="pushmenu"]').on('shown.lte.pushmenu', function() {
            localStorage.setItem('sidebarCollapsed', 'false');
        });
    });
    
    // Notification count update (example function)
    function updateNotificationCount() {
        // You can implement AJAX call here to get real notification count
        // Example:
        /*
        $.ajax({
            url: '/api/notifications/count',
            method: 'GET',
            success: function(data) {
                if (data.count > 0) {
                    $('.navbar-badge').text(data.count).show();
                } else {
                    $('.navbar-badge').hide();
                }
            }
        });
        */
    }
    
    // Call notification update every 60 seconds
    setInterval(updateNotificationCount, 60000);
    
    // Prevent multiple form submissions
    $('form').on('submit', function() {
        $(this).find('button[type="submit"]').prop('disabled', true);
        $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> กำลังประมวลผล...');
    });
    
    // Add smooth scrolling to all links
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
    
    // Print functionality
    function printContent() {
        window.print();
    }
    
    // Export to PDF (requires additional library)
    function exportToPDF() {
        alert('Export to PDF functionality - Please implement with jsPDF or similar library');
    }
    
    // Console warning for developers
    console.log('%cShop Admin Panel', 'color: #ff6b35; font-size: 20px; font-weight: bold;');
    console.log('%cVersion 1.0.0', 'color: #1a1a1a; font-size: 12px;');
    console.log('%c⚠️ Warning: Do not paste any code here unless you understand what it does!', 'color: red; font-size: 14px; font-weight: bold;');
</script>

<!-- Page Specific Scripts -->
@yield('scripts')

<!-- Additional Scripts Stack -->
@stack('page-scripts')

</body>
</html>
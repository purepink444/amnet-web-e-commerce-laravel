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
<body>
    <div class="admin-wrapper" id="adminWrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                    <div class="admin-brand-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <span>Admin Panel</span>
                </a>
            </div>

            <nav class="admin-sidebar-nav">
                <div class="admin-nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="admin-nav-icon fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="admin-nav-item">
                    <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="admin-nav-icon fas fa-box-seam"></i>
                        <span>จัดการสินค้า</span>
                    </a>
                </div>
                <div class="admin-nav-item">
                    <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="admin-nav-icon fas fa-tags"></i>
                        <span>จัดการหมวดหมู่</span>
                    </a>
                </div>
                <div class="admin-nav-item">
                    <a href="{{ route('admin.brands.index') }}" class="admin-nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <i class="admin-nav-icon fas fa-copyright"></i>
                        <span>จัดการแบรนด์</span>
                    </a>
                </div>
                <div class="admin-nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="admin-nav-icon fas fa-receipt"></i>
                        <span>จัดการคำสั่งซื้อ</span>
                    </a>
                </div>
                <div class="admin-nav-item">
                    <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="admin-nav-icon fas fa-users"></i>
                        <span>จัดการผู้ใช้</span>
                    </a>
                </div>
                <div class="admin-nav-item">
                    <a href="{{ route('admin.reports.index') }}" class="admin-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="admin-nav-icon fas fa-chart-line"></i>
                        <span>รายงาน</span>
                    </a>
                </div>
                <div class="admin-nav-item" style="margin-top: auto;">
                    <a href="{{ route('home') }}" class="admin-nav-link">
                        <i class="admin-nav-icon fas fa-home"></i>
                        <span>กลับหน้าหลัก</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Topbar -->
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button class="admin-menu-toggle" id="sidebarToggle" aria-label="เปิดเมนู">
                        <i class="fas fa-bars"></i>
                    </button>

                    <nav class="admin-breadcrumb">
                        <span class="admin-breadcrumb-item">Admin Panel</span>
                        <span class="admin-breadcrumb-item active">@yield('title', 'Dashboard')</span>
                    </nav>
                </div>

                <div class="admin-topbar-right">
                    <button class="theme-toggle" id="themeToggle" aria-label="เปลี่ยนธีม">
                        <i class="fas fa-moon"></i>
                    </button>

                    <div class="admin-user-menu">
                        <button class="admin-user-trigger" id="userMenuTrigger">
                            <div class="admin-user-avatar">
                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                            </div>
                            <div class="admin-user-info">
                                <div class="admin-user-name">{{ auth()->user()->username }}</div>
                                <div class="admin-user-role">Administrator</div>
                            </div>
                            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
                        </button>

                        <div class="admin-user-dropdown" id="userDropdown">
                            <a href="{{ route('admin.dashboard') }}" class="admin-dropdown-item">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="admin-dropdown-item">
                                <i class="fas fa-box-seam"></i>
                                <span>จัดการสินค้า</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="admin-dropdown-item">
                                <i class="fas fa-users"></i>
                                <span>จัดการผู้ใช้</span>
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="admin-dropdown-item">
                                <i class="fas fa-receipt"></i>
                                <span>จัดการคำสั่งซื้อ</span>
                            </a>
                            <div class="admin-dropdown-divider"></div>
                            <form action="{{ route('account.logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="admin-dropdown-item danger" style="width: 100%; border: none; background: none; text-align: left;">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>ออกจากระบบ</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="admin-content">
                <!-- Page Header -->
                <div class="admin-page-header">
                    <h1 class="admin-page-title">@yield('title', 'Dashboard')</h1>
                    <p class="admin-page-subtitle">@yield('subtitle', 'จัดการระบบหลังบ้าน')</p>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="admin-alert admin-alert-success admin-animate-fade-in">
                        <i class="admin-alert-icon fas fa-check-circle"></i>
                        <div>
                            <strong>สำเร็จ!</strong> {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="admin-alert admin-alert-danger admin-animate-fade-in">
                        <i class="admin-alert-icon fas fa-exclamation-circle"></i>
                        <div>
                            <strong>เกิดข้อผิดพลาด!</strong> {{ session('error') }}
                        </div>
                    </div>
                @endif

                <!-- Main Content -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- SweetAlert2 Integration --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Theme Management
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;

        // Load saved theme
        const savedTheme = localStorage.getItem('admin-theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);

        // Theme toggle handler
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('admin-theme', newTheme);
            updateThemeIcon(newTheme);
        });

        function updateThemeIcon(theme) {
            const icon = themeToggle.querySelector('i');
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }

        // Sidebar Management
        const sidebar = document.getElementById('adminSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const main = document.querySelector('.admin-main');

        // Load sidebar state
        const sidebarCollapsed = localStorage.getItem('admin-sidebar-collapsed') === 'true';
        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            main.classList.add('sidebar-collapsed');
        }

        // Sidebar toggle handler
        sidebarToggle.addEventListener('click', function() {
            const isCollapsed = sidebar.classList.contains('collapsed');

            if (isCollapsed) {
                sidebar.classList.remove('collapsed');
                main.classList.remove('sidebar-collapsed');
                localStorage.setItem('admin-sidebar-collapsed', 'false');
            } else {
                sidebar.classList.add('collapsed');
                main.classList.add('sidebar-collapsed');
                localStorage.setItem('admin-sidebar-collapsed', 'true');
            }
        });

        // User Menu Dropdown
        const userMenuTrigger = document.getElementById('userMenuTrigger');
        const userDropdown = document.getElementById('userDropdown');

        userMenuTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuTrigger.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });

        // Check for SweetAlert configuration from server
        @if(session('sweetalert'))
            const config = @json(session('sweetalert'));
            Swal.fire(config).then((result) => {
                if (config.redirect) {
                    window.location.href = config.redirect;
                }
            });
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
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
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

        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.admin-alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });

        // Responsive sidebar handling
        function handleResize() {
            if (window.innerWidth <= 1024) {
                sidebar.classList.add('collapsed');
                main.classList.add('sidebar-collapsed');
            } else {
                const wasCollapsed = localStorage.getItem('admin-sidebar-collapsed') === 'true';
                if (!wasCollapsed) {
                    sidebar.classList.remove('collapsed');
                    main.classList.remove('sidebar-collapsed');
                }
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize(); // Initial check

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + B to toggle sidebar
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                sidebarToggle.click();
            }

            // Ctrl/Cmd + Shift + D to toggle theme
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                themeToggle.click();
            }
        });

        // Add loading states to forms
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังดำเนินการ...';
                }
            });
        });
    });
    </script>

    @yield('scripts')
</body>
</html>

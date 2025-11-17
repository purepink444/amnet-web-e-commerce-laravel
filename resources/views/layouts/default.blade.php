{{-- resources/views/layouts/default.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Meta Tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <meta name="description" content="@yield('meta_description', 'Laravel E-commerce - ระบบจัดการร้านค้าออนไลน์')">
    <meta name="keywords" content="@yield('meta_keywords', 'ecommerce, shop, online store')">
    <meta name="author" content="@yield('meta_author', 'Your Company')">
    
    {{-- Title --}}
    <title>@yield('title', config('app.name', 'Laravel')) - {{ config('app.name') }}</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    
    {{-- Preconnect for Performance --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://code.jquery.com">
    
    {{-- Bootstrap 5.3 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" 
          crossorigin="anonymous">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    {{-- Custom CSS (Vite) --}}
    @vite(['resources/css/app.css'])
    
    {{-- Page Specific Styles (Deprecated - use CSS files instead) --}}
    @stack('styles')
    
    {{-- Additional Head Content --}}
    @stack('head')
</head>
<body>
    {{-- Loading Overlay --}}
    <div id="page-loader" class="page-loader">
        <div class="loader-spinner"></div>
        <p class="loader-text">กำลังโหลด...</p>
    </div>

    {{-- Skip to Content (Accessibility) --}}
    <a href="#main-content" class="skip-to-content">ข้ามไปยังเนื้อหา</a>

    {{-- Header --}}
    <header id="site-header" role="banner">
        @include('includes.header')
    </header>

    {{-- Main Content --}}
    <main id="main-content" role="main">
        {{-- Flash Messages --}}
        @if(session('success') || session('error') || session('warning') || session('info'))
            <div class="container mt-3">
                @if(session('success'))
                    <x-alert type="success" dismissible>
                        {{ session('success') }}
                    </x-alert>
                @endif
                
                @if(session('error'))
                    <x-alert type="danger" dismissible>
                        {{ session('error') }}
                    </x-alert>
                @endif
                
                @if(session('warning'))
                    <x-alert type="warning" dismissible>
                        {{ session('warning') }}
                    </x-alert>
                @endif
                
                @if(session('info'))
                    <x-alert type="info" dismissible>
                        {{ session('info') }}
                    </x-alert>
                @endif
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="container mt-3">
                <x-alert type="danger" dismissible>
                    <strong>กรุณาแก้ไขข้อผิดพลาด:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            </div>
        @endif

        {{-- Page Content --}}
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer id="site-footer" role="contentinfo">
        @include('includes.footer')
    </footer>

    {{-- Back to Top Button --}}
    <button id="back-to-top" class="back-to-top" aria-label="กลับสู่ด้านบน" style="display: none;">
        <i class="bi bi-arrow-up"></i>
    </button>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
            crossorigin="anonymous"></script>
    
    {{-- Bootstrap 5.3 JS Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" 
            crossorigin="anonymous"></script>
    
    {{-- Custom JS (Vite) --}}
    @vite(['resources/js/app.js'])
    
    {{-- Global Scripts --}}
    <script>
        $(document).ready(function() {
            // ============================
            // Page Loader
            // ============================
            setTimeout(function() {
                $('#page-loader').fadeOut('slow');
            }, 500);
            
            // ============================
            // Auto-dismiss Alerts
            // ============================
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
            
            // ============================
            // Back to Top Button
            // ============================
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#back-to-top').fadeIn();
                } else {
                    $('#back-to-top').fadeOut();
                }
            });
            
            $('#back-to-top').click(function() {
                $('html, body').animate({ scrollTop: 0 }, 600);
                return false;
            });
            
            // ============================
            // Smooth Scrolling
            // ============================
            $('a[href^="#"]').on('click', function(e) {
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                }
            });
            
            // ============================
            // Form Validation Feedback
            // ============================
            $('form').on('submit', function() {
                const $submitBtn = $(this).find('button[type="submit"]');
                const originalText = $submitBtn.html();
                
                $submitBtn.prop('disabled', true)
                          .html('<i class="bi bi-hourglass-split"></i> กำลังดำเนินการ...');
                
                // Re-enable after 5 seconds (fallback)
                setTimeout(function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                }, 5000);
            });
            
            // ============================
            // Tooltip Initialization
            // ============================
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // ============================
            // Popover Initialization
            // ============================
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // ============================
            // CSRF Token for AJAX
            // ============================
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // ============================
            // Lazy Loading Images
            // ============================
            if ('loading' in HTMLImageElement.prototype) {
                const images = document.querySelectorAll('img[loading="lazy"]');
                images.forEach(img => {
                    img.src = img.dataset.src || img.src;
                });
            }
            
            // ============================
            // Console Warning
            // ============================
            console.log('%c⚠️ Warning!', 'color: red; font-size: 20px; font-weight: bold;');
            console.log('%cDo not paste any code here unless you know what you are doing!', 'color: orange; font-size: 14px;');
        });
        
        // ============================
        // Window Load Event
        // ============================
        $(window).on('load', function() {
            // Hide page loader
            $('#page-loader').fadeOut('slow');
        });
    </script>
    
    {{-- Page Specific Scripts --}}
    @stack('scripts')
    
    {{-- Additional Body Content --}}
    @stack('body')
</body>
</html>
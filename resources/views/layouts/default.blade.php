<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Laravel App')</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts - Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/variables.css', 'resources/css/app.css', 'resources/js/app.js'])

    
    @yield('styles')
</head>
<body>
    <header>
        @include('includes.navbar')
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        @include('includes.footer')
    </footer>
    
    <!-- jQuery (ต้องโหลดก่อน Thailand.js) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap 5.3 JS Bundle (รวม Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Custom Scripts -->
    @yield('scripts')

    <!-- Theme Toggle Button (Fallback) -->
    <button id="theme-toggle-fallback" class="theme-toggle-fallback" onclick="toggleThemeFallback()">
        ??
    </button>
<html lang="th">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Laravel App')</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts - Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/variables.css', 'resources/css/app.css', 'resources/js/app.js'])

    
    @yield('styles')
</head>
<body>
    <header>
        @include('includes.navbar')
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        @include('includes.footer')
    </footer>
    
    <!-- jQuery (ต้องโหลดก่อน Thailand.js) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap 5.3 JS Bundle (รวม Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Custom Scripts -->
    @yield('scripts')

    <!-- Theme Toggle Button (Fallback) -->
    <button id="theme-toggle-fallback" style="
        position: fixed;
        bottom: 2rem;
        left: 2rem;
        width: 60px;
        height: 60px;
        background: #ff6b35;
        border: 3px solid #fff;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.4);
        color: white;
        font-size: 20px;
    " onclick="toggleThemeFallback()">
        ☀️
    </button>

    <script>
        // Bootstrap dropdowns should work automatically
        // If issues persist, check console for errors
        console.log('Bootstrap loaded, dropdowns should work');

        // Fallback theme toggle function
        function toggleThemeFallback() {
            const html = document.documentElement;
            const button = document.getElementById('theme-toggle-fallback');

            if (html.getAttribute('data-theme') === 'dark') {
                html.removeAttribute('data-theme');
                button.innerHTML = '☀️';
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                button.innerHTML = '🌙';
                localStorage.setItem('theme', 'dark');
            }

            // Add transition class
            document.body.classList.add('theme-transition');
            setTimeout(() => {
                document.body.classList.remove('theme-transition');
            }, 300);
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const button = document.getElementById('theme-toggle-fallback');

            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                button.innerHTML = '🌙';
            } else {
                button.innerHTML = '☀️';
            }
        });
    </script>

</body>
</html>

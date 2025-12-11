<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('description', 'ระบบจัดการสินค้าและการสั่งซื้อ')">
    <meta name="keywords" content="@yield('keywords', 'สินค้า, อีคอมเมิร์ซ, ระบบจัดการ')">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og:title', config('app.name'))">
    <meta property="og:description" content="@yield('og:description', 'ระบบจัดการสินค้าและการสั่งซื้อ')">
    <meta property="og:type" content="@yield('og:type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og:image', asset('images/og-default.jpg'))">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@100;300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS (เพิ่มตรงนี้) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (เพิ่มตรงนี้) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Core CSS -->
    @vite(['resources/css/variables.css', 'resources/css/components.css', 'resources/css/app.css'])

    @stack('styles')
    @yield('head')
</head>

<body class="@yield('body-class')">

    <a href="#main-content" class="sr-only focus:not-sr-only focus-visible">
        ข้ามไปยังเนื้อหาหลัก
    </a>

    @yield('layout')

    <!-- Core JavaScript -->
    @vite(['resources/js/app.js'])

    <!-- Bootstrap JS (เพิ่มตรงนี้) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
    @yield('footer-scripts')
</body>
</html>

@extends('layouts.admin')

@section('title', 'Guest')

@section('content')
<div class="container py-4">
    <h1>Guest</h1><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Login')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>

    <!-- TODO: เพิ่มเนื้อหาที่นี่ -->
</div>
@endsection
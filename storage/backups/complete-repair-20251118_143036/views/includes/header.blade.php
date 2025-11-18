<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #ff6b35 0%, #e85d2a 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold fs-4" href="/">
            <i class="bi bi-shop me-2"></i>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>  
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/product">
                        <i class="bi bi-bag me-1"></i>Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/news">
                        <i class="bi bi-newspaper me-1"></i>News
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/support">
                        <i class="bi bi-headset me-1"></i>Support
                    </a>
                </li>
                <li class="nav-item"> 
                    <a class="nav-link text-white fw-semibold px-3" href="/aboutus">
                        <i class="bi bi-info-circle me-1"></i>About Us
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/contact">
                        <i class="bi bi-envelope me-1"></i>Contact Us
                    </a>
                </li>

                {{-- ถ้ายังไม่ล็อกอิน แสดง Register + Login --}}
                @guest
                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold px-3 btn-register" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold px-3" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>เข้าสู่ระบบ
                        </a>
                    </li>
                @endguest

                {{-- ถ้าล็อกอินแล้ว แสดงชื่อ + Dropdown --}}
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white fw-semibold px-3" href="#" id="userMenu" data-bs-toggle="dropdown">
                            👤 {{ auth()->user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile">โปรไฟล์</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item" type="submit">ออกจากระบบ</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth

                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/language">
                        <i class="bi bi-globe me-1"></i>ภาษา
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<style>
.navbar {
    padding: 0.8rem 0;
    margin: 0 !important;
    width: 100%;
}

.navbar-brand {
    color: #fff !important;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.navbar-brand:hover {
    transform: scale(1.05);
}

.nav-link {
    transition: all 0.3s ease;
    border-radius: 8px;
    margin: 0 2px;
}

.nav-link:hover {
    background-color: rgba(26, 26, 26, 0.3);
    transform: translateY(-2px);
}

.btn-register {
    background-color: rgba(26, 26, 26, 0.4);
    border-radius: 8px;
}

.btn-register:hover {
    background-color: #1a1a1a;
}

@media (max-width: 991px) {
    .navbar-nav {
        padding: 1rem 0;
    }
    
    .nav-link {
        padding: 0.5rem 1rem !important;
        margin: 0.2rem 0;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

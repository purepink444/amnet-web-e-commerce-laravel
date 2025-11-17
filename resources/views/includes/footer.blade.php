<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    
<footer class="footer mt-auto">
    <div class="footer-content">
        <div class="container-fluid px-4 py-4">
            <div class="row align-items-center">
                <!-- Logo & Description -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="text-white fw-bold mb-2">
                        <i class="bi bi-shop me-2"></i>
                    </h5>
                    <p class="text-white-50 small mb-0">
                        
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div class="col-md-4 mb-3 mb-md-0 text-center">
                    <h6 class="text-white fw-bold mb-2">เมนูด่วน</h6>
                    <div class="d-flex justify-content-center flex-wrap gap-3">
                        <a href="/" class="footer-link">Home</a>
                        <a href="/shop" class="footer-link">Products</a>
                        <a href="/aboutus" class="footer-link">About Us</a>
                        <a href="/contact" class="footer-link">Contact</a>
                    </div>
                </div>
                
                <!-- Social & Copyright -->
                <div class="col-md-4 text-md-end text-center">
                    <div class="social-icons mb-2">
                        <a href="#" class="social-icon">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi bi-line"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                    </div>
                    <p class="text-white-50 small mb-0">
                        <i class="bi bi-c-circle me-1"></i>2025 All Rights Reserved
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Bar -->
    <div class="footer-bottom">
        <div class="container-fluid px-4 py-2">
            <div class="row">
                <div class="col-12 text-center">
                    <small class="text-white-50">
                        Made with <i class="bi bi-heart-fill text-danger"></i> in Thailand
                    </small>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.footer {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #e85d2a 100%);
    margin: 0 !important;
    width: 100%;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

.footer-content {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-bottom {
    background-color: rgba(0, 0, 0, 0.2);
}

.footer-link {
    color: #fff;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    opacity: 0.8;
}

.footer-link:hover {
    color: #ff6b35;
    opacity: 1;
    text-decoration: underline;
}

.social-icons {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
}

@media (max-width: 767px) {
    .social-icons {
        justify-content: center;
    }
}

.social-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.social-icon:hover {
    background-color: #ff6b35;
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
}

h5, h6 {
    margin-bottom: 0.5rem;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
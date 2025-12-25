<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balanced Footer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Main Content -->
    <main class="flex-grow-1 d-flex align-items-center justify-content-center bg-light">
        <div class="container text-center p-5">
            <h1>เนื้อหาหลักของเว็บไซต์</h1>
            <p class="text-muted">Footer สมดุลและสวยงาม</p>
            <button class="btn btn-warning">เข้าสู่ระบบ</button>
        </div>
    </main>

    <!-- Footer - Full Width & Balanced -->
    <footer class="footer mt-auto">
        <div class="footer-content py-5">
            <div class="container">
                <div class="row g-4 justify-content-center">
                    <!-- Logo & Description -->
                    <div class="col-lg-3 col-md-6 text-center text-lg-start">
                        <div class="footer-brand mb-3">
                            <h4 class="text-white fw-bold mb-3">
                                🛍️ ร้านค้าออนไลน์
                            </h4>
                            <p class="footer-text">
                                ศูนย์รวมสินค้าคุณภาพ ส่งตรงถึงบ้านคุณ
                            </p>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="col-lg-3 col-md-6 text-center text-lg-start">
                        <h5 class="text-white fw-bold mb-3">เมนูด่วน</h5>
                        <ul class="footer-links">
                            <li><a href="/">🏠 หน้าแรก</a></li>
                            <li><a href="/shop">🛒 สินค้า</a></li>
                            <li><a href="/aboutus">ℹ️ เกี่ยวกับเรา</a></li>
                            <li><a href="/contact">📧 ติดต่อ</a></li>
                        </ul>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="col-lg-3 col-md-6 text-center text-lg-start">
                        <h5 class="text-white fw-bold mb-3">ติดต่อเรา</h5>
                        <ul class="footer-contact">
                            <li>📍 Nakhon Ratchasima</li>
                            <li>📞 02-XXX-XXXX</li>
                            <li>📧 info@shop.com</li>
                            <li>⏰ 09:00 - 18:00</li>
                        </ul>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="col-lg-3 col-md-6 text-center text-lg-start">
                        <h5 class="text-white fw-bold mb-3">ติดตามเรา</h5>
                        <div class="social-links">
                            <a href="#" class="social-btn" title="Facebook">📱</a>
                            <a href="#" class="social-btn" title="Instagram">📸</a>
                            <a href="#" class="social-btn" title="Line">💬</a>
                            <a href="#" class="social-btn" title="Twitter">🐦</a>
                        </div>
                        <p class="footer-text mt-3 mb-0">
                            เชื่อมต่อกับเราผ่านโซเชียล
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center py-3">
                    <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                        <small class="text-white-50">
                            ©️ 2025 ร้านค้าออนไลน์ สงวนลิขสิทธิ์
                        </small>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <small class="text-white-50">
                            Made with ❤️ in Thailand 🇹🇭
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #e85d2a 100%);
            color: white;
            width: 100%;
        }

        .footer-content {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-brand h4 {
            font-size: 1.4rem;
            letter-spacing: 0.5px;
        }

        .footer-text {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }

        /* Links */
        .footer-links,
        .footer-contact {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li,
        .footer-contact li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .footer-links a:hover {
            color: #fff;
            transform: translateX(5px);
        }

        .footer-contact li {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.9rem;
        }

        /* Social Buttons */
        .social-links {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }

        @media (min-width: 992px) {
            .social-links {
                justify-content: flex-start;
            }
        }

        .social-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            font-size: 1.3rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .social-btn:hover {
            background: #e85d2a;
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 8px 20px rgba(232, 93, 42, 0.4);
        }

        /* Bottom Bar */
        .footer-bottom {
            background: rgba(0, 0, 0, 0.3);
        }

        /* Responsive - ปรับให้สมมาตรทุกขนาด */
        @media (max-width: 991px) {
            .footer [class*="col-"] {
                text-align: center !important;
            }
            
            .footer-links,
            .footer-contact {
                display: inline-block;
                text-align: left;
            }

            .social-links {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .footer-brand h4 {
                font-size: 1.2rem;
            }

            h5 {
                font-size: 1.1rem;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
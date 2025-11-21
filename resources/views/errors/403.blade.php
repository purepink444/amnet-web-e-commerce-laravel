<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - ไม่มีสิทธิ์เข้าถึง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --orange-primary: #ff6b35;
            --orange-dark: #e85d2a;
            --black-primary: #1a1a1a;
            --black-secondary: #2d2d2d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 50%, var(--orange-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff6b35' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
            z-index: 0;
        }

        .error-container {
            text-align: center;
            padding: 3rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(255, 107, 53, 0.3);
            max-width: 600px;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .error-code {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            line-height: 1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .error-icon {
            font-size: 4rem;
            color: var(--orange-primary);
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 4px 8px rgba(255, 107, 53, 0.3));
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        .error-message {
            font-size: 1.8rem;
            color: var(--black-primary);
            margin: 1.5rem 0 1rem;
            font-weight: 700;
        }

        .error-description {
            color: #6c757d;
            margin-bottom: 2.5rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-home {
            background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
            border: none;
            padding: 14px 32px;
            font-size: 1.1rem;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
            color: white;
            background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-primary) 100%);
        }

        .btn-login {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--orange-primary);
            color: var(--orange-primary);
            padding: 14px 32px;
            font-size: 1.1rem;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .btn-login:hover {
            background: var(--orange-primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
        }

        .help-text {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(0,0,0,0.1);
            color: #6c757d;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .error-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            .error-code {
                font-size: 6rem;
            }

            .error-message {
                font-size: 1.5rem;
            }

            .error-description {
                font-size: 1rem;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-home, .btn-login {
                width: 100%;
                max-width: 280px;
            }
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 5rem;
            }

            .error-icon {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h1 class="error-code">403</h1>
        <h2 class="error-message">ไม่มีสิทธิ์เข้าถึง</h2>
        <p class="error-description">
            ขอโทษด้วย! คุณไม่มีสิทธิ์เข้าถึงหน้านี้ กรุณาเข้าสู่ระบบด้วยบัญชีที่มีสิทธิ์
            หรือติดต่อผู้ดูแลระบบหากคุณคิดว่านี่เป็นข้อผิดพลาด
        </p>

        <div class="action-buttons">
            <a href="{{ url('/') }}" class="btn-home">
                <i class="bi bi-house-door me-2"></i>
                กลับหน้าแรก
            </a>
            <a href="{{ route('login') }}" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                เข้าสู่ระบบ
            </a>
        </div>

        <div class="help-text">
            <p class="mb-0">
                <strong>ต้องการความช่วยเหลือ?</strong><br>
                ติดต่อทีมสนับสนุนของเราเพื่อขอสิทธิ์เข้าถึง
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
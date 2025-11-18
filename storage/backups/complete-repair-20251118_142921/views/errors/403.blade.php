<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - ไม่มีสิทธิ์เข้าถึง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-icon {
            font-size: 5rem;
            color: #f5576c;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-lock"></i>
        </div>
        <h1 class="error-code">403</h1>
        <h2 class="error-message">ไม่มีสิทธิ์เข้าถึง</h2>
        <p class="error-description">
            ขอโทษด้วย! คุณไม่มีสิทธิ์เข้าถึงหน้านี้
        </p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">
            <i class="fas fa-home me-2"></i>กลับหน้าแรก
        </a>
    </div>
</body>
</html>
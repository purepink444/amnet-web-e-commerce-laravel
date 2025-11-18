<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - ไม่พบหน้าที่ต้องการ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ff670fff 0%, #212022ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            background: linear-gradient(135deg, #ff6030ff 0%, #000000ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            line-height: 1;
        }
        
        .error-icon {
            font-size: 5rem;
            color: #f8793eff;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .error-message {
            font-size: 1.5rem;
            color: #333;
            margin: 1.5rem 0;
            font-weight: 600;
        }
        
        .error-description {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            font-size: 1rem;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.5);
            color: white;
        }
        
        .btn-back {
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 12px 40px;
            font-size: 1rem;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin-left: 1rem;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-search"></i>
        </div>
        <h1 class="error-code">404</h1>
        <h2 class="error-message">ไม่พบหน้าที่คุณต้องการ</h2>
        <p class="error-description">
            ขอโทษด้วย! หน้าที่คุณกำลังมองหาอาจถูกย้าย ลบ หรือไม่เคยมีอยู่จริง
        </p>
        <div class="mt-4">
            <a href="{{ route('home') }}" class="btn-home">
                <i class="fas fa-home me-2"></i>
                กลับหน้าแรก
            </a>
            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>
                ย้อนกลับ
            </a>
        </div>
    </div>
</body>
</html> 
@section('title', 'ไม่พบหน้านี้') 
 
@section('content') 
<div class="container py-4"> 
    <h1>ไม่พบหน้านี้</h1> 
    < เพิ่มเนื้อหาที่นี่ --> 
</div> 
@endsection 

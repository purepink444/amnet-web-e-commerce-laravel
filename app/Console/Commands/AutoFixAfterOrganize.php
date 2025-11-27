<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AutoFixAfterOrganize extends Command
{
    protected $signature = 'project:fix
                            {--check : เช็คอย่างเดียวไม่แก้ไข}
                            {--backup : สำรองก่อนแก้ไข}';
    
    protected $description = 'แก้ไข namespace, routes, และ migrations อัตโนมัติหลังจัดระเบียบ';

    protected $fixed = [];
    protected $errors = [];
    protected $duplicates = [];

    public function handle()
    {
        $this->info('🔧 เริ่มแก้ไขไฟล์อัตโนมัติ...');
        $this->newLine();

        if ($this->option('backup')) {
            $this->createBackup();
        }

        // 1. แก้ไข Namespace ใน Controllers
        $this->fixControllerNamespaces();

        // 2. อัปเดต routes/web.php
        $this->updateRoutes();

        // 3. แก้ไข Migrations ซ้ำ
        $this->fixDuplicateMigrations();

        // 4. สร้างเนื้อหาพื้นฐานใน Views
        $this->generateBasicViewContent();

        // แสดงสรุป
        $this->showSummary();

        return 0;
    }

    // ═══════════════════════════════════════════════════════════
    // [1] แก้ไข Namespace ใน Controllers
    // ═══════════════════════════════════════════════════════════
    protected function fixControllerNamespaces()
    {
        $this->info('📝 [1/4] กำลังแก้ไข Namespace ใน Controllers...');

        $folders = [
            'Admin' => 'App\Http\Controllers\Admin',
            'Client' => 'App\Http\Controllers\Client',
            'Account' => 'App\Http\Controllers\Account',
            'Auth' => 'App\Http\Controllers\Auth',
        ];

        foreach ($folders as $folder => $namespace) {
            $path = app_path("Http/Controllers/{$folder}");
            
            if (!File::exists($path)) {
                continue;
            }

            $files = File::files($path);

            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $content = File::get($file->getPathname());
                $originalContent = $content;

                // แก้ไข namespace
                $pattern = '/^namespace\s+App\\\\Http\\\\Controllers;/m';
                $replacement = "namespace {$namespace};";
                
                $content = preg_replace($pattern, $replacement, $content);

                // ถ้ามีการเปลี่ยนแปลง
                if ($content !== $originalContent) {
                    if (!$this->option('check')) {
                        File::put($file->getPathname(), $content);
                    }
                    
                    $this->fixed[] = "Namespace: {$folder}/{$file->getFilename()}";
                    $this->line("  ✓ {$file->getFilename()} → {$namespace}");
                }
            }
        }

        $this->newLine();
    }

    // ═══════════════════════════════════════════════════════════
    // [2] อัปเดต routes/web.php
    // ═══════════════════════════════════════════════════════════
    protected function updateRoutes()
    {
        $this->info('📝 [2/4] กำลังอัปเดต routes/web.php...');

        $routesPath = base_path('routes/web.php');

        if (!File::exists($routesPath)) {
            $this->error('  ✗ ไม่พบไฟล์ routes/web.php');
            return;
        }

        $content = File::get($routesPath);
        $originalContent = $content;

        // แทนที่ use statements เก่า
        $replacements = [
            // Admin Controllers
            "use App\Http\Controllers\AdminProductController;" 
                => "use App\Http\Controllers\Admin\AdminProductController;",
            "use App\Http\Controllers\AdminOrderController;" 
                => "use App\Http\Controllers\Admin\AdminOrderController;",
            "use App\Http\Controllers\AdminUserController;" 
                => "use App\Http\Controllers\Admin\AdminUserController;",
            "use App\Http\Controllers\AdminReportController;" 
                => "use App\Http\Controllers\Admin\AdminReportController;",
            "use App\Http\Controllers\AdminCategoryController;" 
                => "use App\Http\Controllers\Admin\AdminCategoryController;",
            "use App\Http\Controllers\AdminBrandController;" 
                => "use App\Http\Controllers\Admin\AdminBrandController;",
            
            // Client Controllers
            "use App\Http\Controllers\ClientProductController;" 
                => "use App\Http\Controllers\Client\ClientProductController;",
            "use App\Http\Controllers\CartController;" 
                => "use App\Http\Controllers\Client\CartController;",
            "use App\Http\Controllers\CheckoutController;" 
                => "use App\Http\Controllers\Client\CheckoutController;",
            "use App\Http\Controllers\ReviewController;" 
                => "use App\Http\Controllers\Client\ReviewController;",
            
            // Account Controllers
            "use App\Http\Controllers\ProfileController;" 
                => "use App\Http\Controllers\Account\ProfileController;",
            "use App\Http\Controllers\OrderController;" 
                => "use App\Http\Controllers\Account\OrderController;",
            "use App\Http\Controllers\WishlistController;" 
                => "use App\Http\Controllers\Account\WishlistController;",
            "use App\Http\Controllers\SettingsController;" 
                => "use App\Http\Controllers\Account\SettingsController;",
        ];

        foreach ($replacements as $old => $new) {
            if (str_contains($content, $old)) {
                $content = str_replace($old, $new, $content);
                $this->line("  ✓ แทนที่: " . basename($old));
            }
        }

        // จัดกลุ่ม use statements ใหม่ (ถ้ายังไม่มี)
        if (!str_contains($content, "use App\Http\Controllers\Admin\\{")) {
            $content = $this->reorganizeUseStatements($content);
        }

        if ($content !== $originalContent) {
            if (!$this->option('check')) {
                File::put($routesPath, $content);
            }
            
            $this->fixed[] = "Routes: routes/web.php";
            $this->info("  ✓ อัปเดต routes/web.php สำเร็จ");
        } else {
            $this->line("  ⚠ routes/web.php อัปเดตอยู่แล้ว");
        }

        $this->newLine();
    }

    protected function reorganizeUseStatements($content)
    {
        // หา use statements block
        $pattern = '/(use\s+App\\\\Http\\\\Controllers[^;]+;\s*\n)+/';
        
        if (preg_match($pattern, $content, $matches)) {
            // สร้าง use statements ใหม่แบบจัดกลุ่ม
            $newUseStatements = <<<'PHP'
use App\Http\Controllers\{
    RegisterController,
    LoginController,
    DashboardController,
    HomeController,
};
use App\Http\Controllers\Admin\{
    AdminProductController,
    AdminOrderController,
    AdminUserController,
    AdminReportController,
    AdminCategoryController,
    AdminBrandController,
};
use App\Http\Controllers\Client\{
    ClientProductController,
    CartController,
    CheckoutController,
    ReviewController,
};
use App\Http\Controllers\Account\{
    ProfileController,
    OrderController,
    WishlistController,
    SettingsController,
};

PHP;
            
            // แทนที่ use statements block เดิม
            $content = preg_replace($pattern, $newUseStatements, $content, 1);
        }

        return $content;
    }

    // ═══════════════════════════════════════════════════════════
    // [3] แก้ไข Migrations ซ้ำ
    // ═══════════════════════════════════════════════════════════
    protected function fixDuplicateMigrations()
    {
        $this->info('📝 [3/4] กำลังตรวจสอบ Migrations ซ้ำ...');

        $migrationsPath = database_path('migrations');
        $files = File::files($migrationsPath);

        // เก็บรายการ migrations ตามชื่อตาราง
        $migrations = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            
            // ดึงชื่อตารางจากชื่อไฟล์
            if (preg_match('/\d+_\d+_\d+_\d+_(.+)\.php$/', $filename, $matches)) {
                $tableName = $matches[1];
                
                if (!isset($migrations[$tableName])) {
                    $migrations[$tableName] = [];
                }
                
                $migrations[$tableName][] = [
                    'filename' => $filename,
                    'path' => $file->getPathname(),
                    'timestamp' => $this->extractTimestamp($filename),
                ];
            }
        }

        // หา migrations ซ้ำ
        foreach ($migrations as $tableName => $files) {
            if (count($files) > 1) {
                // เรียงตามวันที่
                usort($files, function($a, $b) {
                    return $a['timestamp'] <=> $b['timestamp'];
                });

                // เก็บไฟล์แรก ลบไฟล์ที่เหลือ
                $keepFile = array_shift($files);
                
                $this->warn("  ⚠ พบ Migration ซ้ำ: {$tableName}");
                $this->line("    → เก็บ: {$keepFile['filename']}");

                foreach ($files as $duplicateFile) {
                    $this->duplicates[] = $duplicateFile['path'];
                    $this->line("    → ลบ: {$duplicateFile['filename']}");
                    
                    if (!$this->option('check')) {
                        // เปลี่ยนชื่อไฟล์แทนการลบทันที (ปลอดภัยกว่า)
                        File::move(
                            $duplicateFile['path'], 
                            $duplicateFile['path'] . '.duplicate'
                        );
                    }
                }
            }
        }

        if (empty($this->duplicates)) {
            $this->line("  ✓ ไม่พบ Migrations ซ้ำ");
        } else {
            $this->fixed[] = "Migrations: ลบไฟล์ซ้ำ " . count($this->duplicates) . " ไฟล์";
        }

        $this->newLine();
    }

    protected function extractTimestamp($filename)
    {
        if (preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_/', $filename, $matches)) {
            return $matches[1];
        }
        return '';
    }

    // ═══════════════════════════════════════════════════════════
    // [4] สร้างเนื้อหาพื้นฐานใน Views
    // ═══════════════════════════════════════════════════════════
    protected function generateBasicViewContent()
    {
        $this->info('📝 [4/4] กำลังสร้างเนื้อหาใน Views...');

        $viewTemplates = [
            // Cart Views
            'cart/index.blade.php' => $this->getCartIndexTemplate(),
            
            // Checkout Views
            'checkout/index.blade.php' => $this->getCheckoutIndexTemplate(),
            'checkout/success.blade.php' => $this->getCheckoutSuccessTemplate(),
            
            // Account Views
            'account/profile.blade.php' => $this->getProfileTemplate(),
            'account/orders/index.blade.php' => $this->getOrdersIndexTemplate(),
            'account/wishlist.blade.php' => $this->getWishlistTemplate(),
            
            // Product Views
            'products/index.blade.php' => $this->getProductsIndexTemplate(),
            'products/show.blade.php' => $this->getProductShowTemplate(),
            
            // Admin Views
            'admin/dashboard.blade.php' => $this->getAdminDashboardTemplate(),
            'admin/products/index.blade.php' => $this->getAdminProductsTemplate(),
            'admin/orders/index.blade.php' => $this->getAdminOrdersTemplate(),
            
            // Auth Views
            'auth/login.blade.php' => $this->getLoginTemplate(),
            'auth/register.blade.php' => $this->getRegisterTemplate(),
            
            // Error Pages
            'errors/404.blade.php' => $this->get404Template(),
        ];

        $count = 0;
        foreach ($viewTemplates as $path => $template) {
            $fullPath = resource_path("views/{$path}");
            
            // ตรวจสอบว่าไฟล์มีอยู่และว่างเปล่าหรือมีเนื้อหาน้อยมาก
            $shouldUpdate = false;
            
            if (!File::exists($fullPath)) {
                $shouldUpdate = true;
            } else {
                $content = File::get($fullPath);
                // ถ้าไฟล์มีเนื้อหาน้อยกว่า 200 ตัวอักษร ถือว่าว่างเปล่า
                if (strlen(trim($content)) < 200) {
                    $shouldUpdate = true;
                }
            }

            if ($shouldUpdate) {
                if (!$this->option('check')) {
                    // สร้างโฟลเดอร์ถ้ายังไม่มี
                    $dir = dirname($fullPath);
                    if (!File::exists($dir)) {
                        File::makeDirectory($dir, 0755, true);
                    }

                    File::put($fullPath, $template);
                }
                
                $this->fixed[] = "View: {$path}";
                $this->line("  ✓ {$path}");
                $count++;
            }
        }

        if ($count === 0) {
            $this->line("  ⚠ Views ทั้งหมดมีเนื้อหาแล้ว");
        } else {
            $this->info("  ✓ อัปเดต {$count} views");
        }

        $this->newLine();
    }

    // ═══════════════════════════════════════════════════════════
    // View Templates
    // ═══════════════════════════════════════════════════════════

    protected function getCartIndexTemplate()
    {
        return <<<'BLADE'
@extends('layouts.app')

@section('title', 'ตะกร้าสินค้า')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">ตะกร้าสินค้า</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <!-- TODO: แสดงรายการสินค้าในตะกร้า -->
                    <p class="text-muted">ยังไม่มีสินค้าในตะกร้า</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>สรุปยอดชำระ</h5>
                </div>
                <div class="card-body">
                    <!-- TODO: แสดงยอดรวม -->
                    <a href="/checkout" class="btn btn-primary w-100">ดำเนินการชำระเงิน</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getCheckoutIndexTemplate()
    {
        return <<<'BLADE'
@extends('layouts.app')

@section('title', 'ชำระเงิน')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">ชำระเงิน</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>ข้อมูลจัดส่ง</h5>
                </div>
                <div class="card-body">
                    <!-- TODO: ฟอร์มกรอกข้อมูลจัดส่ง -->
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>วิธีการชำระเงิน</h5>
                </div>
                <div class="card-body">
                    <!-- TODO: เลือกวิธีชำระเงิน -->
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>สรุปคำสั่งซื้อ</h5>
                </div>
                <div class="card-body">
                    <!-- TODO: สรุปยอดชำระ -->
                    <button class="btn btn-success w-100">ยืนยันการสั่งซื้อ</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getCheckoutSuccessTemplate()
    {
        return <<<'BLADE'
@extends('layouts.app')

@section('title', 'สั่งซื้อสำเร็จ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card">
                <div class="card-body py-5">
                    <i class="bi bi-check-circle text-success" style="font-size: 5rem;"></i>
                    <h2 class="mt-3">สั่งซื้อสำเร็จ!</h2>
                    <p class="text-muted">ขอบคุณสำหรับการสั่งซื้อ</p>
                    <p>เลขที่คำสั่งซื้อ: <strong>#{{ $orderId ?? '00000' }}</strong></p>
                    
                    <div class="mt-4">
                        <a href="/account/orders" class="btn btn-primary me-2">ดูคำสั่งซื้อ</a>
                        <a href="/products" class="btn btn-outline-secondary">ช้อปต่อ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getProfileTemplate()
    {
        return <<<'BLADE'
@extends('layouts.app')

@section('title', 'โปรไฟล์')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">โปรไฟล์</h1>
    
    <div class="row">
        <div class="col-md-3">
            <!-- TODO: เมนูด้านข้าง -->
            <div class="list-group">
                <a href="/account/profile" class="list-group-item active">โปรไฟล์</a>
                <a href="/account/orders" class="list-group-item">คำสั่งซื้อ</a>
                <a href="/account/wishlist" class="list-group-item">รายการโปรด</a>
                <a href="/account/settings" class="list-group-item">ตั้งค่า</a>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5>ข้อมูลส่วนตัว</h5>
                </div>
                <div class="card-body">
                    <!-- TODO: ฟอร์มแก้ไขโปรไฟล์ -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getOrdersIndexTemplate()
    {
        return <<<'BLADE'
@extends('layouts.app')

@section('title', 'ประวัติการสั่งซื้อ')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">ประวัติการสั่งซื้อ</h1>
    
    <div class="card">
        <div class="card-body">
            <!-- TODO: แสดงรายการคำสั่งซื้อ -->
            <p class="text-muted">ยังไม่มีคำสั่งซื้อ</p>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getWishlistTemplate()
    {
        return <<<'BLADE'
@extends('layouts.app')

@section('title', 'รายการโปรด')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">รายการโปรด</h1>
    
    <div class="row">
        <!-- TODO: แสดงสินค้าที่ชื่นชอบ -->
        <div class="col-12">
            <p class="text-muted">ยังไม่มีสินค้าในรายการโปรด</p>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getProductsIndexTemplate()
    {
        return <<<'BLADE'
@extends('layouts.app')

@section('title', 'สินค้าทั้งหมด')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">สินค้าทั้งหมด</h1>
    
    <div class="row">
        <!-- TODO: แสดงรายการสินค้า -->
    </div>
</div>
@endsection
BLADE;
    }

    protected function getProductShowTemplate()
    {
        return <<<'BLADE'
@extends('layouts.app')

@section('title', 'รายละเอียดสินค้า')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <!-- TODO: รูปภาพสินค้า -->
        </div>
        <div class="col-md-6">
            <!-- TODO: ข้อมูลสินค้า -->
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getAdminDashboardTemplate()
    {
        return <<<'BLADE'
@extends('layouts.admin')

@section('title', 'แดชบอร์ด')

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">แดชบอร์ด</h1>
    
    <div class="row">
        <!-- TODO: สถิติต่างๆ -->
    </div>
</div>
@endsection
BLADE;
    }

    protected function getAdminProductsTemplate()
    {
        return <<<'BLADE'
@extends('layouts.admin')

@section('title', 'จัดการสินค้า')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>จัดการสินค้า</h1>
        <a href="/admin/products/create" class="btn btn-primary">เพิ่มสินค้า</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <!-- TODO: ตารางสินค้า -->
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getAdminOrdersTemplate()
    {
        return <<<'BLADE'
@extends('layouts.admin')

@section('title', 'จัดการคำสั่งซื้อ')

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">จัดการคำสั่งซื้อ</h1>
    
    <div class="card">
        <div class="card-body">
            <!-- TODO: ตารางคำสั่งซื้อ -->
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getLoginTemplate()
    {
        return <<<'BLADE'
@extends('layouts.guest')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header text-center">
                    <h3>เข้าสู่ระบบ</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/login">
                        @csrf
                        <!-- TODO: ฟอร์ม login -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getRegisterTemplate()
    {
        return <<<'BLADE'
@extends('layouts.guest')

@section('title', 'สมัครสมาชิก')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>สมัครสมาชิก</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/register">
                        @csrf
                        <!-- TODO: ฟอร์ม register -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function get404Template()
    {
        return <<<'BLADE'
@extends('layouts.app')

@section('title', '404 - ไม่พบหน้านี้')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6 text-center">
            <h1 class="display-1">404</h1>
            <h2>ไม่พบหน้าที่คุณต้องการ</h2>
            <p class="text-muted">ขออภัย หน้าที่คุณกำลังมองหาอาจถูกย้ายหรือลบไปแล้ว</p>
            <a href="/" class="btn btn-primary">กลับหน้าแรก</a>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    // ═══════════════════════════════════════════════════════════
    // Helper Methods
    // ═══════════════════════════════════════════════════════════

    protected function createBackup()
    {
        $this->info('💾 กำลังสร้าง Backup...');
        
        $backupDir = storage_path('backups/autofix-' . date('Y-m-d-His'));
        
        File::ensureDirectoryExists($backupDir);
        
        // Backup routes
        if (File::exists(base_path('routes/web.php'))) {
            File::copy(
                base_path('routes/web.php'),
                $backupDir . '/web.php'
            );
        }
        
        $this->info("  ✓ Backup ที่: {$backupDir}");
        $this->newLine();
    }

    protected function showSummary()
    {
        $this->info('📊 สรุปผลการดำเนินการ');
        $this->line(str_repeat('─', 60));
        
        if (!empty($this->fixed)) {
            $this->info('✓ แก้ไขสำเร็จ: ' . count($this->fixed) . ' รายการ');
            if ($this->option('verbose')) {
                foreach ($this->fixed as $item) {
                    $this->line("  • {$item}");
                }
            }
            $this->newLine();
        }
        
        if (!empty($this->duplicates)) {
            $this->warn('⚠ ลบไฟล์ซ้ำ: ' . count($this->duplicates) . ' ไฟล์');
            foreach ($this->duplicates as $file) {
                $this->line("  • " . basename($file));
            }
            $this->newLine();
        }
        
        if (!empty($this->errors)) {
            $this->error('✗ พบข้อผิดพลาด: ' . count($this->errors));
            foreach ($this->errors as $error) {
                $this->line("  • {$error}");
            }
            $this->newLine();
        }

        if ($this->option('check')) {
            $this->comment('💡 นี่เป็นโหมดเช็คเท่านั้น ไม่มีการเปลี่ยนแปลงไฟล์');
            $this->info('รัน: php artisan project:fix เพื่อดำเนินการจริง');
        } else {
            $this->info('✅ แก้ไขเสร็จสมบูรณ์!');
            $this->newLine();
            $this->comment('📝 ขั้นตอนถัดไป:');
            $this->line('  1. รัน: php artisan route:clear');
            $this->line('  2. รัน: php artisan view:clear');
            $this->line('  3. รัน: php artisan config:clear');
            $this->line('  4. ทดสอบแอปพลิเคชัน: php artisan serve');
        }
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OrganizeProjectCommand extends Command
{
    protected $signature = 'project:organize 
                            {--check : เช็คอย่างเดียว ไม่สร้างไฟล์}
                            {--force : บังคับสร้างทับไฟล์เดิม}
                            {--backup : สำรองไฟล์ก่อนย้าย}';
    
    protected $description = 'จัดระเบียบโครงสร้างโปรเจค ย้ายไฟล์ไปโฟลเดอร์ที่ถูกต้อง';

    protected $moved = [];
    protected $created = [];
    protected $skipped = [];
    protected $errors = [];

    // โครงสร้างมาตรฐานที่ควรมี
    protected $structure = [
        'controllers' => [
            // Public Controllers
            'App\Http\Controllers' => [
                'HomeController.php',
                'LoginController.php',
                'RegisterController.php',
            ],
            
            // Client Controllers (สำหรับลูกค้า)
            'App\Http\Controllers\Client' => [
                'ClientProductController.php',
                'CartController.php',
                'CheckoutController.php',
                'ReviewController.php',
            ],
            
            // Account Controllers (สำหรับสมาชิก)
            'App\Http\Controllers\Account' => [
                'ProfileController.php',
                'OrderController.php',
                'WishlistController.php',
                'SettingsController.php',
            ],
            
            // Admin Controllers
            'App\Http\Controllers\Admin' => [
                'DashboardController.php',
                'AdminProductController.php',
                'AdminOrderController.php',
                'AdminUserController.php',
                'AdminReportController.php',
                'AdminCategoryController.php',
                'AdminBrandController.php',
            ],
        ],
        
        'views' => [
            // Layout Views
            'resources/views/layouts' => [
                'admin.blade.php',
                'app.blade.php',
                'guest.blade.php',
            ],
            
            // Public Views
            'resources/views' => [
                'home.blade.php',
                'welcome.blade.php',
            ],
            
            // Auth Views
            'resources/views/auth' => [
                'login.blade.php',
                'register.blade.php',
            ],
            
            // Product Views
            'resources/views/products' => [
                'index.blade.php',
                'show.blade.php',
            ],
            
            // Cart & Checkout Views
            'resources/views/cart' => [
                'index.blade.php',
            ],
            'resources/views/checkout' => [
                'index.blade.php',
                'success.blade.php',
                'cancel.blade.php',
            ],
            
            // Account Views
            'resources/views/account' => [
                'profile.blade.php',
                'orders.blade.php',
                'wishlist.blade.php',
                'settings.blade.php',
            ],
            
            // Admin Views
            'resources/views/admin' => [
                'dashboard.blade.php',
            ],
            'resources/views/admin/products' => [
                'index.blade.php',
                'create.blade.php',
                'edit.blade.php',
                'show.blade.php',
            ],
            'resources/views/admin/orders' => [
                'index.blade.php',
                'show.blade.php',
            ],
            'resources/views/admin/users' => [
                'index.blade.php',
                'edit.blade.php',
            ],
        ],
        
        'middleware' => [
            'RolesMiddleware.php',
            'CheckCartOwnership.php',
            'TrackUserActivity.php',
        ],
    ];

    public function handle()
    {
        $this->info('🔍 เริ่มตรวจสอบและจัดระเบียบโปรเจค...');
        $this->newLine();

        // 1. Backup ถ้าต้องการ
        if ($this->option('backup')) {
            $this->createBackup();
        }

        // 2. สแกนไฟล์ที่มีอยู่
        $this->info('📂 กำลังสแกนไฟล์ที่มีอยู่...');
        $existingFiles = $this->scanExistingFiles();
        
        // 3. วิเคราะห์และจัดระเบียบ Controllers
        $this->organizeControllers($existingFiles['controllers']);
        
        // 4. วิเคราะห์และจัดระเบียบ Views
        $this->organizeViews($existingFiles['views']);
        
        // 5. สร้างไฟล์ที่ขาดหาย
        if (!$this->option('check')) {
            $this->createMissingFiles();
        }
        
        // 6. แสดงสรุปผล
        $this->showSummary();
        
        return 0;
    }

    protected function scanExistingFiles()
    {
        $files = [
            'controllers' => [],
            'views' => [],
            'models' => [],
        ];

        // สแกน Controllers
        $controllerPath = app_path('Http/Controllers');
        if (File::exists($controllerPath)) {
            $files['controllers'] = $this->scanDirectory($controllerPath, '*.php');
        }

        // สแกน Views
        $viewPath = resource_path('views');
        if (File::exists($viewPath)) {
            $files['views'] = $this->scanDirectory($viewPath, '*.blade.php');
        }

        // สแกน Models
        $modelPath = app_path('Models');
        if (File::exists($modelPath)) {
            $files['models'] = $this->scanDirectory($modelPath, '*.php');
        }

        return $files;
    }

    protected function scanDirectory($path, $pattern)
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && fnmatch($pattern, $file->getFilename())) {
                $relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $files[] = [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'relative' => $relativePath,
                    'size' => $file->getSize(),
                ];
            }
        }

        return $files;
    }

    protected function organizeControllers($existingControllers)
    {
        $this->info('🎯 [1/3] กำลังจัดระเบียบ Controllers...');
        
        foreach ($existingControllers as $file) {
            $filename = $file['name'];
            $currentPath = $file['path'];
            
            // หา namespace/โฟลเดอร์ที่เหมาะสม
            $targetFolder = $this->determineControllerFolder($filename);
            
            if (!$targetFolder) {
                $this->skipped[] = "Controller: {$filename} (ไม่แน่ใจว่าควรอยู่ที่ไหน)";
                continue;
            }

            $targetPath = app_path($targetFolder . '/' . $filename);
            
            // ถ้าไฟล์อยู่ที่ถูกต้องแล้ว
            if ($currentPath === $targetPath) {
                $this->line("  ✓ {$filename} <fg=gray>(อยู่ที่ถูกต้องแล้ว)</>");
                continue;
            }

            // ย้ายไฟล์
            if (!$this->option('check')) {
                $this->moveFile($currentPath, $targetPath, $filename);
            } else {
                $this->info("  → {$filename} ควรย้ายไป: {$targetFolder}");
            }
        }
    }

    protected function determineControllerFolder($filename)
    {
        // กำหนดกฎการจัดโฟลเดอร์
        $rules = [
            '/^Admin.*Controller\.php$/' => 'Http/Controllers/Admin',
            '/^Client.*Controller\.php$/' => 'Http/Controllers/Client',
            '/^(Profile|Order|Wishlist|Settings)Controller\.php$/' => 'Http/Controllers/Account',
            '/^(Cart|Checkout|Review)Controller\.php$/' => 'Http/Controllers/Client',
            '/^(Login|Register|Password)Controller\.php$/' => 'Http/Controllers/Auth',
        ];

        foreach ($rules as $pattern => $folder) {
            if (preg_match($pattern, $filename)) {
                return $folder;
            }
        }

        // Default: อยู่ใน Controllers หลัก
        return 'Http/Controllers';
    }

    protected function organizeViews($existingViews)
    {
        $this->newLine();
        $this->info('🎯 [2/3] กำลังจัดระเบียบ Views...');
        
        foreach ($existingViews as $file) {
            $filename = $file['name'];
            $currentPath = $file['path'];
            
            // หาโฟลเดอร์ที่เหมาะสม
            $targetFolder = $this->determineViewFolder($filename, $file['relative']);
            
            if (!$targetFolder) {
                $this->skipped[] = "View: {$filename} (ไม่แน่ใจว่าควรอยู่ที่ไหน)";
                continue;
            }

            $targetPath = resource_path("views/{$targetFolder}/{$filename}");
            
            // ถ้าไฟล์อยู่ที่ถูกต้องแล้ว
            if ($currentPath === $targetPath) {
                $this->line("  ✓ {$filename} <fg=gray>(อยู่ที่ถูกต้องแล้ว)</>");
                continue;
            }

            // ย้ายไฟล์
            if (!$this->option('check')) {
                $this->moveFile($currentPath, $targetPath, $filename);
            } else {
                $this->info("  → {$filename} ควรย้ายไป: views/{$targetFolder}");
            }
        }
    }

    protected function determineViewFolder($filename, $relativePath)
    {
        // ถ้าอยู่ใน subfolder แล้ว ให้ใช้ folder เดิม
        if (str_contains($relativePath, DIRECTORY_SEPARATOR)) {
            $parts = explode(DIRECTORY_SEPARATOR, $relativePath);
            array_pop($parts); // เอาชื่อไฟล์ออก
            return implode('/', $parts);
        }

        // กำหนดกฎตามชื่อไฟล์
        $rules = [
            '/^home/' => '',
            '/^welcome/' => '',
            '/^(login|register)/' => 'auth',
            '/^(admin|dashboard)/' => 'admin',
            '/^(cart|checkout)/' => 'cart',
            '/^(profile|order|wishlist|settings)/' => 'account',
            '/^product/' => 'products',
        ];

        foreach ($rules as $pattern => $folder) {
            if (preg_match($pattern, $filename)) {
                return $folder;
            }
        }

        return null;
    }

    protected function moveFile($from, $to, $filename)
    {
        try {
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            $directory = dirname($to);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // ย้ายไฟล์
            File::move($from, $to);
            
            $this->moved[] = $filename;
            $this->line("  <fg=green>✓ ย้าย:</> {$filename}");
            
        } catch (\Exception $e) {
            $this->errors[] = "ไม่สามารถย้าย {$filename}: {$e->getMessage()}";
            $this->error("  ✗ Error: {$filename}");
        }
    }

    protected function createMissingFiles()
    {
        $this->newLine();
        $this->info('🎯 [3/3] กำลังสร้างไฟล์ที่ขาดหาย...');

        // สร้าง Controllers ที่ขาด
        $this->createMissingControllers();
        
        // สร้าง Views ที่ขาด
        $this->createMissingViews();
    }

    protected function createMissingControllers()
    {
        foreach ($this->structure['controllers'] as $namespace => $controllers) {
            $path = str_replace('App\\Http\\Controllers', 'Http/Controllers', $namespace);
            $fullPath = app_path($path);

            foreach ($controllers as $controller) {
                $filePath = $fullPath . '/' . $controller;
                
                if (File::exists($filePath)) {
                    continue;
                }

                // สร้างไฟล์
                $controllerName = str_replace('.php', '', $controller);
                
                try {
                    $this->call('make:controller', [
                        'name' => str_replace('App\\Http\\Controllers\\', '', $namespace) . '\\' . $controllerName,
                    ]);
                    
                    $this->created[] = "Controller: {$controller}";
                    $this->line("  <fg=green>✓ สร้าง:</> {$controller}");
                    
                } catch (\Exception $e) {
                    $this->errors[] = "ไม่สามารถสร้าง {$controller}: {$e->getMessage()}";
                }
            }
        }
    }

    protected function createMissingViews()
    {
        foreach ($this->structure['views'] as $folder => $views) {
            $fullPath = base_path($folder);

            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
            }

            foreach ($views as $view) {
                $filePath = $fullPath . '/' . $view;
                
                if (File::exists($filePath)) {
                    continue;
                }

                // สร้างไฟล์ view
                File::put($filePath, $this->getViewTemplate($view));
                
                $this->created[] = "View: {$view}";
                $this->line("  <fg=green>✓ สร้าง:</> {$view}");
            }
        }
    }

    protected function getViewTemplate($filename)
    {
        $title = ucwords(str_replace(['.blade.php', '-', '_'], ['', ' ', ' '], $filename));
        
        return <<<BLADE
@extends('layouts.admin')

@section('title', '{$title}')

@section('content')
<div class="container py-4">
    <h1>{$title}</h1>
    <!-- TODO: เพิ่มเนื้อหาที่นี่ -->
</div>
@endsection
BLADE;
    }

    protected function createBackup()
    {
        $this->info('💾 กำลังสำรองข้อมูล...');
        
        $backupDir = storage_path('backups/project-' . date('Y-m-d-His'));
        
        // Backup Controllers
        File::copyDirectory(
            app_path('Http/Controllers'),
            $backupDir . '/controllers'
        );
        
        // Backup Views
        File::copyDirectory(
            resource_path('views'),
            $backupDir . '/views'
        );
        
        $this->info("  ✓ สำรองข้อมูลที่: {$backupDir}");
        $this->newLine();
    }

    protected function showSummary()
    {
        $this->newLine();
        $this->info('📊 สรุปผลการดำเนินการ');
        $this->line(str_repeat('─', 50));
        
        if (!empty($this->moved)) {
            $this->info('✓ ย้ายไฟล์: ' . count($this->moved) . ' ไฟล์');
            if ($this->option('verbose')) {
                foreach ($this->moved as $file) {
                    $this->line("  • {$file}");
                }
            }
        }
        
        if (!empty($this->created)) {
            $this->info('✓ สร้างไฟล์ใหม่: ' . count($this->created) . ' ไฟล์');
            if ($this->option('verbose')) {
                foreach ($this->created as $file) {
                    $this->line("  • {$file}");
                }
            }
        }
        
        if (!empty($this->skipped)) {
            $this->warn('⚠ ข้ามไฟล์: ' . count($this->skipped) . ' ไฟล์');
            if ($this->option('verbose')) {
                foreach ($this->skipped as $item) {
                    $this->line("  • {$item}");
                }
            }
        }
        
        if (!empty($this->errors)) {
            $this->error('✗ เกิดข้อผิดพลาด: ' . count($this->errors));
            foreach ($this->errors as $error) {
                $this->line("  • {$error}");
            }
        }

        if ($this->option('check')) {
            $this->newLine();
            $this->comment('💡 นี่เป็นโหมดเช็คเท่านั้น ไม่มีการเปลี่ยนแปลงไฟล์');
            $this->info('รัน: php artisan project:organize เพื่อดำเนินการจริง');
        }
    }
}
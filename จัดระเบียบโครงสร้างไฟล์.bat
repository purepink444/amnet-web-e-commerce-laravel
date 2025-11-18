@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

echo ╔════════════════════════════════════════════════════════════╗
echo ║     จัดระเบียบและสร้างไฟล์โปรเจค Laravel อัตโนมัติ        ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

REM ตรวจสอบว่าอยู่ในโฟลเดอร์ Laravel หรือไม่
if not exist "artisan" (
    echo ❌ ไม่พบไฟล์ artisan - กรุณารันในโฟลเดอร์โปรเจค Laravel
    pause
    exit /b 1
)

echo 📋 สรุปสิ่งที่จะทำ:
echo ──────────────────────────────────────────────────────────
echo   1. สร้าง Backup ข้อมูล
echo   2. จัดโครงสร้างโฟลเดอร์
echo   3. ย้าย Controllers ไปโฟลเดอร์ที่ถูกต้อง
echo   4. ย้ายและจัดระเบียบ Views
echo   5. แก้ไข Migrations ที่ซ้ำ
echo   6. สร้าง Controllers ที่ขาดหาย
echo   7. สร้าง Views ที่ขาดหาย
echo   8. สร้าง Migrations ที่ขาดหาย
echo   9. อัปเดต namespace และ routes
echo ──────────────────────────────────────────────────────────
echo.

set /p CONFIRM="ต้องการดำเนินการต่อหรือไม่? (y/n): "
if /i not "%CONFIRM%"=="y" (
    echo ยกเลิกการดำเนินการ
    pause
    exit /b 0
)
echo.

REM ═══════════════════════════════════════════════════════════
REM [STEP 1] สร้าง Backup
REM ═══════════════════════════════════════════════════════════
echo [1/9] 💾 กำลังสร้าง Backup...

set TIMESTAMP=%date:~10,4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%
set BACKUP_DIR=storage\backups\backup_%TIMESTAMP%

mkdir "%BACKUP_DIR%" 2>nul
mkdir "%BACKUP_DIR%\controllers" 2>nul
mkdir "%BACKUP_DIR%\views" 2>nul
mkdir "%BACKUP_DIR%\routes" 2>nul
mkdir "%BACKUP_DIR%\migrations" 2>nul

REM Backup Controllers
if exist "app\Http\Controllers" (
    xcopy /E /I /Q "app\Http\Controllers" "%BACKUP_DIR%\controllers\" >nul 2>&1
    echo   ✓ Backup Controllers
)

REM Backup Views
if exist "resources\views" (
    xcopy /E /I /Q "resources\views" "%BACKUP_DIR%\views\" >nul 2>&1
    echo   ✓ Backup Views
)

REM Backup Routes
if exist "routes\web.php" (
    copy /Y "routes\web.php" "%BACKUP_DIR%\routes\web.php" >nul 2>&1
    echo   ✓ Backup Routes
)

REM Backup Migrations
if exist "database\migrations" (
    xcopy /E /I /Q "database\migrations" "%BACKUP_DIR%\migrations\" >nul 2>&1
    echo   ✓ Backup Migrations
)

echo   📁 Backup ที่: %BACKUP_DIR%
echo.

REM ═══════════════════════════════════════════════════════════
REM [STEP 2] สร้างโครงสร้างโฟลเดอร์
REM ═══════════════════════════════════════════════════════════
echo [2/9] 📁 กำลังสร้างโครงสร้างโฟลเดอร์...

REM Controllers
mkdir "app\Http\Controllers\Admin" 2>nul
mkdir "app\Http\Controllers\Client" 2>nul
mkdir "app\Http\Controllers\Account" 2>nul
mkdir "app\Http\Controllers\Auth" 2>nul
echo   ✓ Controllers folders

REM Views
mkdir "resources\views\layouts" 2>nul
mkdir "resources\views\components" 2>nul
mkdir "resources\views\pages" 2>nul
mkdir "resources\views\auth" 2>nul
mkdir "resources\views\products" 2>nul
mkdir "resources\views\cart" 2>nul
mkdir "resources\views\checkout" 2>nul
mkdir "resources\views\account" 2>nul
mkdir "resources\views\account\orders" 2>nul
mkdir "resources\views\admin" 2>nul
mkdir "resources\views\admin\products" 2>nul
mkdir "resources\views\admin\orders" 2>nul
mkdir "resources\views\admin\users" 2>nul
mkdir "resources\views\admin\reports" 2>nul
mkdir "resources\views\errors" 2>nul
echo   ✓ Views folders
echo.

REM ═══════════════════════════════════════════════════════════
REM [STEP 3] ย้าย Controllers
REM ═══════════════════════════════════════════════════════════
echo [3/9] 🚚 กำลังย้าย Controllers...

REM Admin Controllers
for %%F in (
    "AdminProductController.php"
    "AdminOrderController.php"
    "AdminUserController.php"
    "AdminReportController.php"
    "AdminCategoryController.php"
    "AdminBrandController.php"
) do (
    if exist "app\Http\Controllers\%%~F" (
        move /Y "app\Http\Controllers\%%~F" "app\Http\Controllers\Admin\" >nul 2>&1
        echo   ✓ %%~F → Admin/
    )
)

REM Client Controllers
for %%F in (
    "ClientProductController.php"
    "CartController.php"
    "CheckoutController.php"
    "ReviewController.php"
) do (
    if exist "app\Http\Controllers\%%~F" (
        move /Y "app\Http\Controllers\%%~F" "app\Http\Controllers\Client\" >nul 2>&1
        echo   ✓ %%~F → Client/
    )
)

REM Account Controllers
for %%F in (
    "ProfileController.php"
    "OrderController.php"
    "WishlistController.php"
    "SettingsController.php"
) do (
    if exist "app\Http\Controllers\%%~F" (
        move /Y "app\Http\Controllers\%%~F" "app\Http\Controllers\Account\" >nul 2>&1
        echo   ✓ %%~F → Account/
    )
)

REM Auth Controllers
for %%F in (
    "LoginController.php"
    "RegisterController.php"
) do (
    if exist "app\Http\Controllers\%%~F" (
        REM เก็บไว้ที่เดิมหรือย้าย - ขึ้นอยู่กับความต้องการ
        echo   ⚠ %%~F (เก็บไว้ที่ root Controllers)
    )
)

echo.

REM ═══════════════════════════════════════════════════════════
REM [STEP 4] ย้ายและจัดระเบียบ Views
REM ═══════════════════════════════════════════════════════════
echo [4/9] 🚚 กำลังย้าย Views...

REM ย้าย home_admin.blade.php → admin/dashboard.blade.php
if exist "resources\views\home_admin.blade.php" (
    move /Y "resources\views\home_admin.blade.php" "resources\views\admin\dashboard.blade.php" >nul 2>&1
    echo   ✓ home_admin.blade.php → admin/dashboard.blade.php
)

REM ย้าย contact.blade.php → pages/contact.blade.php
if exist "resources\views\contact.blade.php" (
    move /Y "resources\views\contact.blade.php" "resources\views\pages\contact.blade.php" >nul 2>&1
    echo   ✓ contact.blade.php → pages/contact.blade.php
)

echo.

REM ═══════════════════════════════════════════════════════════
REM [STEP 5] แก้ไข Migrations ที่ซ้ำ
REM ═══════════════════════════════════════════════════════════
echo [5/9] 🔧 กำลังตรวจสอบ Migrations ที่ซ้ำ...

REM หา Migrations ที่มีชื่อซ้ำกัน
set FOUND_DUPLICATE=0
if exist "database\migrations\2025_11_15_022135_create_orders_table.php" (
    if exist "database\migrations\2025_11_17_021942_create_orders_table.php" (
        echo   ⚠ พบ Migration ซ้ำ: create_orders_table
        echo   → ควรลบ 2025_11_17_021942_create_orders_table.php
        set FOUND_DUPLICATE=1
    )
)

if !FOUND_DUPLICATE!==0 (
    echo   ✓ ไม่พบ Migrations ซ้ำ
)
echo.

REM ═══════════════════════════════════════════════════════════
REM [STEP 6] สร้าง Controllers ที่ขาดหาย
REM ═══════════════════════════════════════════════════════════
echo [6/9] 🔨 กำลังสร้าง Controllers ที่ขาดหาย...

set CREATED_COUNT=0

REM Client Controllers
if not exist "app\Http\Controllers\Client\CartController.php" (
    php artisan make:controller Client/CartController >nul 2>&1
    echo   ✓ สร้าง: Client/CartController
    set /a CREATED_COUNT+=1
)

if not exist "app\Http\Controllers\Client\CheckoutController.php" (
    php artisan make:controller Client/CheckoutController >nul 2>&1
    echo   ✓ สร้าง: Client/CheckoutController
    set /a CREATED_COUNT+=1
)

if not exist "app\Http\Controllers\Client\ReviewController.php" (
    php artisan make:controller Client/ReviewController >nul 2>&1
    echo   ✓ สร้าง: Client/ReviewController
    set /a CREATED_COUNT+=1
)

REM Account Controllers
if not exist "app\Http\Controllers\Account\OrderController.php" (
    php artisan make:controller Account/OrderController >nul 2>&1
    echo   ✓ สร้าง: Account/OrderController
    set /a CREATED_COUNT+=1
)

if not exist "app\Http\Controllers\Account\WishlistController.php" (
    php artisan make:controller Account/WishlistController >nul 2>&1
    echo   ✓ สร้าง: Account/WishlistController
    set /a CREATED_COUNT+=1
)

if not exist "app\Http\Controllers\Account\SettingsController.php" (
    php artisan make:controller Account/SettingsController >nul 2>&1
    echo   ✓ สร้าง: Account/SettingsController
    set /a CREATED_COUNT+=1
)

if not exist "app\Http\Controllers\Account\ProfileController.php" (
    php artisan make:controller Account/ProfileController >nul 2>&1
    echo   ✓ สร้าง: Account/ProfileController
    set /a CREATED_COUNT+=1
)

REM Admin Controllers
if not exist "app\Http\Controllers\Admin\AdminOrderController.php" (
    php artisan make:controller Admin/AdminOrderController --resource >nul 2>&1
    echo   ✓ สร้าง: Admin/AdminOrderController (Resource)
    set /a CREATED_COUNT+=1
)

if not exist "app\Http\Controllers\Admin\AdminUserController.php" (
    php artisan make:controller Admin/AdminUserController --resource >nul 2>&1
    echo   ✓ สร้าง: Admin/AdminUserController (Resource)
    set /a CREATED_COUNT+=1
)

if not exist "app\Http\Controllers\Admin\AdminReportController.php" (
    php artisan make:controller Admin/AdminReportController >nul 2>&1
    echo   ✓ สร้าง: Admin/AdminReportController
    set /a CREATED_COUNT+=1
)

if not exist "app\Http\Controllers\Admin\AdminCategoryController.php" (
    php artisan make:controller Admin/AdminCategoryController --resource >nul 2>&1
    echo   ✓ สร้าง: Admin/AdminCategoryController (Resource)
    set /a CREATED_COUNT+=1
)

if not exist "app\Http\Controllers\Admin\AdminBrandController.php" (
    php artisan make:controller Admin/AdminBrandController --resource >nul 2>&1
    echo   ✓ สร้าง: Admin/AdminBrandController (Resource)
    set /a CREATED_COUNT+=1
)

echo   📊 สร้างทั้งหมด: !CREATED_COUNT! Controllers
echo.

REM ═══════════════════════════════════════════════════════════
REM [STEP 7] สร้าง Views ที่ขาดหาย
REM ═══════════════════════════════════════════════════════════
echo [7/9] 🔨 กำลังสร้าง Views ที่ขาดหาย...

REM สร้าง View Template
set "VIEW_TEMPLATE=@extends('layouts.admin')^

@section('title', '{{TITLE}}')^

@section('content')^

^<div class="container py-4"^>^

    ^<h1^>{{TITLE}}^</h1^>^

    ^<!-- TODO: เพิ่มเนื้อหาที่นี่ --^>^

^</div^>^

@endsection"

set VIEW_COUNT=0

REM สร้าง Views ต่างๆ
call :CreateView "cart\index.blade.php" "ตะกร้าสินค้า"
call :CreateView "checkout\index.blade.php" "ชำระเงิน"
call :CreateView "checkout\success.blade.php" "ชำระเงินสำเร็จ"
call :CreateView "checkout\cancel.blade.php" "ยกเลิกการชำระเงิน"
call :CreateView "account\profile.blade.php" "โปรไฟล์"
call :CreateView "account\orders\index.blade.php" "ประวัติการสั่งซื้อ"
call :CreateView "account\orders\show.blade.php" "รายละเอียดคำสั่งซื้อ"
call :CreateView "account\wishlist.blade.php" "รายการโปรด"
call :CreateView "account\settings.blade.php" "ตั้งค่า"
call :CreateView "products\index.blade.php" "สินค้าทั้งหมด"
call :CreateView "products\show.blade.php" "รายละเอียดสินค้า"
call :CreateView "admin\products\index.blade.php" "จัดการสินค้า"
call :CreateView "admin\products\create.blade.php" "เพิ่มสินค้า"
call :CreateView "admin\products\edit.blade.php" "แก้ไขสินค้า"
call :CreateView "admin\orders\index.blade.php" "จัดการคำสั่งซื้อ"
call :CreateView "admin\orders\show.blade.php" "รายละเอียดคำสั่งซื้อ"
call :CreateView "admin\users\index.blade.php" "จัดการผู้ใช้"
call :CreateView "admin\reports\index.blade.php" "รายงาน"
call :CreateView "auth\login.blade.php" "เข้าสู่ระบบ"
call :CreateView "auth\register.blade.php" "สมัครสมาชิก"
call :CreateView "errors\404.blade.php" "ไม่พบหน้านี้"

echo   📊 สร้างทั้งหมด: %VIEW_COUNT% Views
echo.

REM ═══════════════════════════════════════════════════════════
REM [STEP 8] สร้าง Migrations ที่ขาดหาย
REM ═══════════════════════════════════════════════════════════
echo [8/9] 🔨 กำลังสร้าง Migrations ที่ขาดหาย...

set MIG_COUNT=0

if not exist "database\migrations\*_create_carts_table.php" (
    php artisan make:migration create_carts_table >nul 2>&1
    echo   ✓ สร้าง: create_carts_table
    set /a MIG_COUNT+=1
)

if not exist "database\migrations\*_create_cart_items_table.php" (
    php artisan make:migration create_cart_items_table >nul 2>&1
    echo   ✓ สร้าง: create_cart_items_table
    set /a MIG_COUNT+=1
)

if not exist "database\migrations\*_create_reviews_table.php" (
    php artisan make:migration create_reviews_table >nul 2>&1
    echo   ✓ สร้าง: create_reviews_table
    set /a MIG_COUNT+=1
)

if not exist "database\migrations\*_create_payments_table.php" (
    php artisan make:migration create_payments_table >nul 2>&1
    echo   ✓ สร้าง: create_payments_table
    set /a MIG_COUNT+=1
)

if not exist "database\migrations\*_create_brands_table.php" (
    php artisan make:migration create_brands_table >nul 2>&1
    echo   ✓ สร้าง: create_brands_table
    set /a MIG_COUNT+=1
)

if not exist "database\migrations\*_create_categories_table.php" (
    php artisan make:migration create_categories_table >nul 2>&1
    echo   ✓ สร้าง: create_categories_table
    set /a MIG_COUNT+=1
)

if not exist "database\migrations\*_create_wishlists_table.php" (
    php artisan make:migration create_wishlists_table >nul 2>&1
    echo   ✓ สร้าง: create_wishlists_table
    set /a MIG_COUNT+=1
)

echo   📊 สร้างทั้งหมด: %MIG_COUNT% Migrations
echo.

REM ═══════════════════════════════════════════════════════════
REM [STEP 9] สรุปและคำแนะนำ
REM ═══════════════════════════════════════════════════════════
echo [9/9] ✅ เสร็จสมบูรณ์!
echo.

echo ╔════════════════════════════════════════════════════════════╗
echo ║                    สรุปการดำเนินการ                       ║
echo ╚════════════════════════════════════════════════════════════╝
echo.
echo ✓ Backup ข้อมูลที่: %BACKUP_DIR%
echo ✓ จัดระเบียบโครงสร้างโฟลเดอร์
echo ✓ ย้าย Controllers และ Views
echo ✓ สร้าง Controllers ใหม่: !CREATED_COUNT! ไฟล์
echo ✓ สร้าง Views ใหม่: %VIEW_COUNT% ไฟล์
echo ✓ สร้าง Migrations ใหม่: %MIG_COUNT% ไฟล์
echo.

echo ╔════════════════════════════════════════════════════════════╗
echo ║              ⚠️  ขั้นตอนที่ต้องทำเอง  ⚠️                 ║
echo ╚════════════════════════════════════════════════════════════╝
echo.
echo 1️⃣  อัปเดต namespace ใน Controllers ที่ย้าย
echo    เปลี่ยนจาก: App\Http\Controllers
echo    เป็น: App\Http\Controllers\Admin (หรือ Client, Account)
echo.
echo 2️⃣  อัปเดต routes/web.php
echo    เปลี่ยน use statements ให้ตรงกับโครงสร้างใหม่:
echo    use App\Http\Controllers\Admin\AdminProductController;
echo    use App\Http\Controllers\Client\ClientProductController;
echo.
echo 3️⃣  แก้ไข Migrations ที่ซ้ำ (ถ้ามี)
echo.
echo 4️⃣  เติมเนื้อหาใน Views ที่สร้างใหม่
echo.
echo 5️⃣  รันคำสั่ง:
echo    ^> php artisan route:clear
echo    ^> php artisan view:clear
echo    ^> php artisan config:clear
echo    ^> php artisan optimize:clear
echo.
echo 6️⃣  ทดสอบแอปพลิเคชัน:
echo    ^> php artisan serve
echo.

echo กด Enter เพื่อเปิดไฟล์ที่ต้องแก้ไข...
pause >nul

REM เปิดไฟล์ที่ต้องแก้ไขใน Notepad
start notepad "routes\web.php"

echo.
echo 🎉 ขอบคุณที่ใช้สคริปต์นี้!
echo.
pause
exit /b 0

REM ═══════════════════════════════════════════════════════════
REM Function: สร้าง View
REM ═══════════════════════════════════════════════════════════
:CreateView
set "FILE_PATH=resources\views\%~1"
set "TITLE=%~2"

if not exist "!FILE_PATH!" (
    echo @extends('layouts.admin') > "!FILE_PATH!"
    echo. >> "!FILE_PATH!"
    echo @section('title', '%TITLE%'^) >> "!FILE_PATH!"
    echo. >> "!FILE_PATH!"
    echo @section('content'^) >> "!FILE_PATH!"
    echo ^<div class="container py-4"^> >> "!FILE_PATH!"
    echo     ^<h1^>%TITLE%^</h1^> >> "!FILE_PATH!"
    echo     ^<!-- TODO: เพิ่มเนื้อหาที่นี่ --^> >> "!FILE_PATH!"
    echo ^</div^> >> "!FILE_PATH!"
    echo @endsection >> "!FILE_PATH!"
    
    echo   ✓ สร้าง: %~1
    set /a VIEW_COUNT+=1
)
exit /b 0
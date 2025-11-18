@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

echo ╔════════════════════════════════════════════════════════════╗
echo ║          แก้ไขไฟล์อัตโนมัติหลังจัดระเบียบ                ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

REM ตรวจสอบว่ามี PowerShell หรือไม่
where powershell >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ❌ ต้องการ PowerShell เพื่อรันสคริปต์นี้
    pause
    exit /b 1
)

echo 📋 สิ่งที่จะทำ:
echo   1. แก้ไข Namespace ใน Controllers
echo   2. อัปเดต routes/web.php
echo   3. ลบ Migrations ที่ซ้ำ
echo.

set /p CONFIRM="ต้องการดำเนินการต่อหรือไม่? (y/n): "
if /i not "%CONFIRM%"=="y" (
    echo ยกเลิกการดำเนินการ
    pause
    exit /b 0
)
echo.

REM ═══════════════════════════════════════════════════════════
REM [1] แก้ไข Namespace ใน Controllers
REM ═══════════════════════════════════════════════════════════
echo [1/3] 📝 กำลังแก้ไข Namespace ใน Controllers...
echo.

REM Admin Controllers
for %%F in (
    "AdminProductController.php"
    "AdminOrderController.php"
    "AdminUserController.php"
    "AdminReportController.php"
    "AdminCategoryController.php"
    "AdminBrandController.php"
) do (
    if exist "app\Http\Controllers\Admin\%%~F" (
        powershell -Command "(Get-Content 'app\Http\Controllers\Admin\%%~F') -replace 'namespace App\\Http\\Controllers;', 'namespace App\Http\Controllers\Admin;' | Set-Content 'app\Http\Controllers\Admin\%%~F'"
        echo   ✓ Admin\%%~F
    )
)

REM Client Controllers
for %%F in (
    "ClientProductController.php"
    "CartController.php"
    "CheckoutController.php"
    "ReviewController.php"
) do (
    if exist "app\Http\Controllers\Client\%%~F" (
        powershell -Command "(Get-Content 'app\Http\Controllers\Client\%%~F') -replace 'namespace App\\Http\\Controllers;', 'namespace App\Http\Controllers\Client;' | Set-Content 'app\Http\Controllers\Client\%%~F'"
        echo   ✓ Client\%%~F
    )
)

REM Account Controllers
for %%F in (
    "ProfileController.php"
    "OrderController.php"
    "WishlistController.php"
    "SettingsController.php"
) do (
    if exist "app\Http\Controllers\Account\%%~F" (
        powershell -Command "(Get-Content 'app\Http\Controllers\Account\%%~F') -replace 'namespace App\\Http\\Controllers;', 'namespace App\Http\Controllers\Account;' | Set-Content 'app\Http\Controllers\Account\%%~F'"
        echo   ✓ Account\%%~F
    )
)

echo.

REM ═══════════════════════════════════════════════════════════
REM [2] อัปเดต routes/web.php
REM ═══════════════════════════════════════════════════════════
echo [2/3] 📝 กำลังอัปเดต routes/web.php...
echo.

if exist "routes\web.php" (
    REM Backup ก่อน
    copy /Y "routes\web.php" "routes\web.php.backup" >nul 2>&1
    
    REM แทนที่ use statements
    powershell -Command "$content = Get-Content 'routes\web.php' -Raw; $content = $content -replace 'use App\\Http\\Controllers\\AdminProductController;', 'use App\Http\Controllers\Admin\AdminProductController;'; $content = $content -replace 'use App\\Http\\Controllers\\AdminOrderController;', 'use App\Http\Controllers\Admin\AdminOrderController;'; $content = $content -replace 'use App\\Http\\Controllers\\AdminUserController;', 'use App\Http\Controllers\Admin\AdminUserController;'; $content = $content -replace 'use App\\Http\\Controllers\\AdminReportController;', 'use App\Http\Controllers\Admin\AdminReportController;'; $content = $content -replace 'use App\\Http\\Controllers\\ClientProductController;', 'use App\Http\Controllers\Client\ClientProductController;'; $content = $content -replace 'use App\\Http\\Controllers\\CartController;', 'use App\Http\Controllers\Client\CartController;'; $content = $content -replace 'use App\\Http\\Controllers\\CheckoutController;', 'use App\Http\Controllers\Client\CheckoutController;'; $content = $content -replace 'use App\\Http\\Controllers\\ProfileController;', 'use App\Http\Controllers\Account\ProfileController;'; $content = $content -replace 'use App\\Http\\Controllers\\OrderController;', 'use App\Http\Controllers\Account\OrderController;'; $content = $content -replace 'use App\\Http\\Controllers\\WishlistController;', 'use App\Http\Controllers\Account\WishlistController;'; $content = $content -replace 'use App\\Http\\Controllers\\SettingsController;', 'use App\Http\Controllers\Account\SettingsController;'; Set-Content 'routes\web.php' $content"
    
    echo   ✓ อัปเดต routes/web.php
    echo   ✓ สำรองไว้ที่ routes\web.php.backup
) else (
    echo   ⚠ ไม่พบไฟล์ routes\web.php
)

echo.

REM ═══════════════════════════════════════════════════════════
REM [3] ลบ Migrations ที่ซ้ำ
REM ═══════════════════════════════════════════════════════════
echo [3/3] 📝 กำลังตรวจสอบ Migrations ที่ซ้ำ...
echo.

REM ตรวจสอบ create_orders_table
set FOUND_OLDER=0
set FOUND_NEWER=0

if exist "database\migrations\2025_11_15_022135_create_orders_table.php" (
    set FOUND_OLDER=1
)

if exist "database\migrations\2025_11_17_021942_create_orders_table.php" (
    set FOUND_NEWER=1
)

if !FOUND_OLDER!==1 if !FOUND_NEWER!==1 (
    echo   ⚠ พบ Migration ซ้ำ: create_orders_table
    echo   → เก็บ: 2025_11_15_022135_create_orders_table.php
    echo   → ลบ: 2025_11_17_021942_create_orders_table.php
    
    REM เปลี่ยนชื่อแทนการลบ (ปลอดภัยกว่า)
    ren "database\migrations\2025_11_17_021942_create_orders_table.php" "2025_11_17_021942_create_orders_table.php.duplicate"
    echo   ✓ เปลี่ยนชื่อเป็น .duplicate แล้ว
) else (
    echo   ✓ ไม่พบ Migrations ซ้ำ
)

echo.

REM ═══════════════════════════════════════════════════════════
REM สรุป
REM ═══════════════════════════════════════════════════════════
echo ╔════════════════════════════════════════════════════════════╗
echo ║                    ✅ เสร็จสมบูรณ์!                       ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

echo 📊 สรุปการดำเนินการ:
echo   ✓ แก้ไข Namespace ใน Controllers
echo   ✓ อัปเดต routes/web.php
echo   ✓ ตรวจสอบ Migrations ซ้ำ
echo.

echo ╔════════════════════════════════════════════════════════════╗
echo ║              📝 ขั้นตอนถัดไป (สำคัญ!)                     ║
echo ╚════════════════════════════════════════════════════════════╝
echo.
echo 1️⃣  Clear Cache ทั้งหมด:
echo    ^> php artisan route:clear
echo    ^> php artisan view:clear
echo    ^> php artisan config:clear
echo    ^> php artisan optimize:clear
echo.
echo 2️⃣  ตรวจสอบ routes:
echo    ^> php artisan route:list
echo.
echo 3️⃣  ทดสอบแอปพลิเคชัน:
echo    ^> php artisan serve
echo.

set /p CLEAR_CACHE="ต้องการให้ Clear Cache ให้อัตโนมัติเลยหรือไม่? (y/n): "
if /i "%CLEAR_CACHE%"=="y" (
    echo.
    echo กำลัง Clear Cache...
    call php artisan route:clear
    call php artisan view:clear
    call php artisan config:clear
    call php artisan optimize:clear
    echo.
    echo ✅ Clear Cache เรียบร้อย!
    echo.
)

echo กด Enter เพื่อดู Route List...
pause >nul

php artisan route:list --columns=method,uri,name,action

echo.
echo 🎉 ทุกอย่างพร้อมแล้ว!
echo.
pause
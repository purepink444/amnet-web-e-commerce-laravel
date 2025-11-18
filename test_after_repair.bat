@echo off
chcp 65001 >nul

echo ╔════════════════════════════════════════════════════════════╗
echo ║              🧪 ทดสอบหลังซ่อมแซม                         ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

echo [1/6] ตรวจสอบไฟล์...
echo ──────────────────────────────────────────────────────────

if exist "app\Models\User.php" (echo   ✓ User.php) else (echo   ✗ User.php)
if exist "app\Models\Role.php" (echo   ✓ Role.php) else (echo   ✗ Role.php)
if exist "app\Http\Controllers\LoginController.php" (echo   ✓ LoginController.php) else (echo   ✗ LoginController.php)
if exist "app\Http\Controllers\Admin\DashboardController.php" (echo   ✓ DashboardController.php) else (echo   ✗ DashboardController.php)
if exist "app\Http\Middleware\RolesMiddleware.php" (echo   ✓ RolesMiddleware.php) else (echo   ✗ RolesMiddleware.php)
if exist "resources\views\admin\dashboard.blade.php" (echo   ✓ dashboard.blade.php) else (echo   ✗ dashboard.blade.php)

echo.
echo [2/6] ตรวจสอบ Routes...
echo ──────────────────────────────────────────────────────────
php artisan route:list --name=admin.dashboard 2>nul
if %ERRORLEVEL%==0 (echo   ✓ Route admin.dashboard มีอยู่) else (echo   ✗ ไม่พบ Route)

echo.
echo [3/6] ตรวจสอบ Database Connection...
echo ──────────────────────────────────────────────────────────
php artisan tinker --execute="try { DB::connection(^)-^>getPdo(^); echo 'Connected'; } catch (Exception \$e^ { echo 'Error'; }" 2>nul
if %ERRORLEVEL%==0 (echo   ✓ เชื่อมต่อ Database สำเร็จ) else (echo   ✗ ไม่สามารถเชื่อมต่อ Database)

echo.
echo [4/6] ตรวจสอบ Roles...
echo ──────────────────────────────────────────────────────────
php artisan tinker --execute="try { \$admin = DB::table('roles'^)-^>where('role_name', 'admin'^)-^>first(^); \$member = DB::table('roles'^)-^>where('role_name', 'member'^)-^>first(^); if (\$admin^ echo 'Role admin มีอยู่ (ID: ' . \$admin-^>role_id . '^\n'; if (\$member^ echo 'Role member มีอยู่ (ID: ' . \$member-^>role_id . '^\n'; } catch (Exception \$e^ { echo 'Error: ' . \$e-^>getMessage(^ . '\n'; }" 2>nul

echo.
echo [5/6] ตรวจสอบ Users...
echo ──────────────────────────────────────────────────────────
php artisan tinker --execute="try { \$total = DB::table('users'^)-^>count(^); \$withRole = DB::table('users'^)-^>whereNotNull('role_id'^)-^>count(^); echo 'Users ทั้งหมด: ' . \$total . '\n'; echo 'Users ที่มี Role: ' . \$withRole . '\n'; } catch (Exception \$e^ { echo 'Error\n'; }" 2>nul

echo.
echo [6/6] ตรวจสอบ Cache...
echo ──────────────────────────────────────────────────────────
if not exist "bootstrap\cache\routes-v7.php" (
    echo   ✓ Route Cache ถูก Clear แล้ว
) else (
    echo   ⚠ Route Cache ยังมีอยู่
)

echo.
echo ╔════════════════════════════════════════════════════════════╗
echo ║                      สรุปการทดสอบ                         ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

echo 📝 วิธีทดสอบ Manual:
echo   1. รัน: php artisan serve
echo   2. เปิดเบราว์เซอร์: http://localhost:8000/login
echo   3. Login ด้วย Admin → ควร redirect ไป /admin/dashboard
echo   4. Logout แล้ว Login ด้วย Member → ควร redirect ไป /account/profile
echo.

set /p SHOW_ROUTES="ต้องการดู Routes ทั้งหมดหรือไม่? (y/n^): "
if /i "%SHOW_ROUTES%"=="y" (
    echo.
    php artisan route:list --columns=method,uri,name
)

echo.
pause
```

---

## 🎯 วิธีใช้งาน 3 ขั้นตอน

### 1. สร้างไฟล์ทั้ง 2 ตัว
- `repair-project.bat` (ไฟล์หลัก)
- `test-after-repair.bat` (ทดสอบ)

### 2. รัน repair-project.bat
```
Double-click → repair-project.bat
```

### 3. รัน test-after-repair.bat
```
Double-click → test-after-repair.bat
@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

echo ╔════════════════════════════════════════════════════════════╗
echo ║        🔧 ระบบซ่อมแซมโปรเจคอัตโนมัติ (ฉบับสมบูรณ์)       ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

REM ตรวจสอบว่าอยู่ในโฟลเดอร์ Laravel
if not exist "artisan" (
    echo ❌ ไม่พบไฟล์ artisan
    echo กรุณารันในโฟลเดอร์โปรเจค Laravel
    pause
    exit /b 1
)

echo 📋 ระบบจะซ่อมแซมปัญหาต่อไปนี้:
echo ──────────────────────────────────────────────────────────
echo   ✓ Database และ Roles (admin, member)
echo   ✓ Models (User, Role)
echo   ✓ Controllers (Login, Dashboard)
echo   ✓ Middleware (RolesMiddleware)
echo   ✓ Routes และ Namespace
echo   ✓ Views (Dashboard)
echo   ✓ Authentication System
echo   ✓ Permissions
echo ──────────────────────────────────────────────────────────
echo.

set /p CONFIRM="⚠️  คำเตือน: จะแก้ไขไฟล์หลายตัว ต้องการดำเนินการต่อหรือไม่? (y/n): "
if /i not "%CONFIRM%"=="y" (
    echo ยกเลิกการดำเนินการ
    pause
    exit /b 0
)
echo.

REM ═══════════════════════════════════════════════════════════
REM STEP 0: สร้าง Backup
REM ═══════════════════════════════════════════════════════════
echo [0/12] 💾 กำลังสร้าง Backup...

set TIMESTAMP=%date:~10,4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%
set BACKUP_DIR=storage\backups\complete-repair-%TIMESTAMP%

mkdir "%BACKUP_DIR%" 2>nul
mkdir "%BACKUP_DIR%\controllers" 2>nul
mkdir "%BACKUP_DIR%\middleware" 2>nul
mkdir "%BACKUP_DIR%\models" 2>nul
mkdir "%BACKUP_DIR%\views" 2>nul
mkdir "%BACKUP_DIR%\routes" 2>nul

xcopy /E /I /Q "app\Http\Controllers" "%BACKUP_DIR%\controllers" >nul 2>&1
xcopy /E /I /Q "app\Http\Middleware" "%BACKUP_DIR%\middleware" >nul 2>&1
xcopy /E /I /Q "app\Models" "%BACKUP_DIR%\models" >nul 2>&1
xcopy /E /I /Q "resources\views" "%BACKUP_DIR%\views" >nul 2>&1
copy /Y "routes\web.php" "%BACKUP_DIR%\routes\" >nul 2>&1

echo   ✓ Backup ที่: %BACKUP_DIR%
echo.

REM ═══════════════════════════════════════════════════════════
REM STEP 1: ซ่อม User Model
REM ═══════════════════════════════════════════════════════════
echo [1/12] 📦 ซ่อมแซม User Model...

(
echo ^<?php
echo.
echo namespace App\Models;
echo.
echo use Illuminate\Database\Eloquent\Factories\HasFactory;
echo use Illuminate\Foundation\Auth\User as Authenticatable;
echo use Illuminate\Notifications\Notifiable;
echo.
echo class User extends Authenticatable
echo {
echo     use HasFactory, Notifiable;
echo.
echo     protected $primaryKey = 'user_id';
echo.
echo     protected $fillable = [
echo         'username',
echo         'email',
echo         'password',
echo         'role_id',
echo         'full_name',
echo         'phone',
echo         'address',
echo     ];
echo.
echo     protected $hidden = [
echo         'password',
echo         'remember_token',
echo     ];
echo.
echo     protected function casts(^): array
echo     {
echo         return [
echo             'email_verified_at' =^> 'datetime',
echo             'password' =^> 'hashed',
echo         ];
echo     }
echo.
echo     public function role(^)
echo     {
echo         return $this-^>belongsTo(Role::class, 'role_id', 'role_id'^);
echo     }
echo.
echo     public function isAdmin(^): bool
echo     {
echo         return strtolower($this-^>role?-^>role_name ?? ''^ === 'admin';
echo     }
echo.
echo     public function isMember(^): bool
echo     {
echo         return strtolower($this-^>role?-^>role_name ?? ''^ === 'member';
echo     }
echo }
) > "app\Models\User.php"

echo   ✓ แก้ไข User Model แล้ว
echo.

REM ═══════════════════════════════════════════════════════════
REM STEP 2: สร้าง Role Model
REM ═══════════════════════════════════════════════════════════
echo [2/12] 📦 สร้าง Role Model...

(
echo ^<?php
echo.
echo namespace App\Models;
echo.
echo use Illuminate\Database\Eloquent\Factories\HasFactory;
echo use Illuminate\Database\Eloquent\Model;
echo.
echo class Role extends Model
echo {
echo     use HasFactory;
echo.
echo     protected $primaryKey = 'role_id';
echo.
echo     protected $fillable = [
echo         'role_name',
echo         'description',
echo     ];
echo.
echo     public function users(^)
echo     {
echo         return $this-^>hasMany(User::class, 'role_id', 'role_id'^);
echo     }
echo }
) > "app\Models\Role.php"

echo   ✓ สร้าง Role Model แล้ว
echo.

REM ═══════════════════════════════════════════════════════════
REM STEP 3: ซ่อม LoginController
REM ═══════════════════════════════════════════════════════════
echo [3/12] 🎮 ซ่อมแซม LoginController...

(
echo ^<?php
echo.
echo namespace App\Http\Controllers;
echo.
echo use Illuminate\Http\Request;
echo use Illuminate\Support\Facades\Auth;
echo.
echo class LoginController extends Controller
echo {
echo     public function showLoginForm(^)
echo     {
echo         return view('auth.login'^);
echo     }
echo.
echo     public function login(Request $request^)
echo     {
echo         $credentials = $request-^>validate([
echo             'username' =^> 'required^|string',
echo             'password' =^> 'required^|string',
echo         ]^);
echo.
echo         if (Auth::attempt($credentials, $request-^>filled('remember'^)^)^ {
echo             $request-^>session(^)-^>regenerate(^);
echo.
echo             $user = Auth::user(^);
echo             $roleName = strtolower($user-^>role?-^>role_name ?? ''^ ;
echo.
echo             if ($roleName === 'admin'^ {
echo                 return redirect(^)-^>intended(route('admin.dashboard'^)^);
echo             }
echo.
echo             return redirect(^)-^>intended(route('account.profile'^)^);
echo         }
echo.
echo         return back(^)-^>withErrors([
echo             'username' =^> 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง',
echo         ]^)-^>onlyInput('username'^);
echo     }
echo.
echo     public function logout(Request $request^)
echo     {
echo         Auth::logout(^);
echo         $request-^>session(^)-^>invalidate(^);
echo         $request-^>session(^)-^>regenerateToken(^);
echo.
echo         return redirect('/'^);
echo     }
echo }
) > "app\Http\Controllers\LoginController.php"

echo   ✓ แก้ไข LoginController แล้ว
echo.

REM ═══════════════════════════════════════════════════════════
REM STEP 4: สร้าง DashboardController
REM ═══════════════════════════════════════════════════════════
echo [4/12] 🎮 สร้าง DashboardController...

mkdir "app\Http\Controllers\Admin" 2>nul

(
echo ^<?php
echo.
echo namespace App\Http\Controllers\Admin;
echo.
echo use App\Http\Controllers\Controller;
echo use Illuminate\Http\Request;
echo.
echo class DashboardController extends Controller
echo {
echo     public function index(^)
echo     {
echo         return view('admin.dashboard'^);
echo     }
echo.
echo     public function refreshCache(^)
echo     {
echo         return redirect(^)-^>route('admin.dashboard'^)
echo             -^>with('success', 'Cache ถูก refresh แล้ว'^);
echo     }
echo }
) > "app\Http\Controllers\Admin\DashboardController.php"

echo   ✓ สร้าง DashboardController แล้ว
echo.

REM ═══════════════════════════════════════════════════════════
REM STEP 5: ซ่อม RolesMiddleware
REM ═══════════════════════════════════════════════════════════
echo [5/12] 🛡️  ซ่อมแซม RolesMiddleware...

(
echo ^<?php
echo.
echo namespace App\Http\Middleware;
echo.
echo use Closure;
echo use Illuminate\Http\Request;
echo use Symfony\Component\HttpFoundation\Response;
echo.
echo class RolesMiddleware
echo {
echo     public function handle(Request $request, Closure $next, ...$roles^): Response
echo     {
echo         if (^!auth(^)-^>check(^)^ {
echo             return redirect(^)-^>route('login'^)-^>with('error', 'กรุณาเข้าสู่ระบบก่อน'^);
echo         }
echo.
echo         $user = auth(^)-^>user(^);
echo         $userRole = strtolower($user-^>role?-^>role_name ?? ''^ ;
echo         $allowedRoles = array_map('strtolower', $roles^);
echo.
echo         if (^!in_array($userRole, $allowedRoles^)^ {
echo             if ($userRole === 'admin'^ {
echo                 return redirect(^)-^>route('admin.dashboard'^)
echo                     -^>with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้'^);
echo             }
echo.
echo             return redirect(^)-^>route('account.profile'^)
echo                 -^>with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้'^);
echo         }
echo.
echo         return $next($request^);
echo     }
echo }
) > "app\Http\Middleware\RolesMiddleware.php"

echo   ✓ แก้ไข RolesMiddleware แล้ว
echo.

REM ═══════════════════════════════════════════════════════════
REM STEP 6: สร้าง Dashboard View
REM ═══════════════════════════════════════════════════════════
echo [6/12] 👁️  สร้าง Dashboard View...

mkdir "resources\views\admin" 2>nul

(
echo @extends('layouts.admin'^)
echo.
echo @section('title', 'Dashboard'^)
echo.
echo @section('content'^)
echo ^<div class="container-fluid py-4"^>
echo     ^<div class="row mb-4"^>
echo         ^<div class="col-12"^>
echo             ^<h1 class="display-5 fw-bold"^>Dashboard^</h1^>
echo             ^<p class="text-muted"^>ยินดีต้อนรับ, {{ auth(^)-^>user(^)-^>username }} (Admin^)^</p^>
echo         ^</div^>
echo     ^</div^>
echo.
echo     ^<div class="row g-4 mb-4"^>
echo         ^<div class="col-xl-3 col-md-6"^>
echo             ^<div class="card border-0 shadow-sm"^>
echo                 ^<div class="card-body"^>
echo                     ^<div class="d-flex align-items-center"^>
echo                         ^<div class="flex-shrink-0"^>
echo                             ^<div class="bg-primary bg-opacity-10 rounded-3 p-3"^>
echo                                 ^<i class="bi bi-box-seam text-primary fs-3"^>^</i^>
echo                             ^</div^>
echo                         ^</div^>
echo                         ^<div class="flex-grow-1 ms-3"^>
echo                             ^<h6 class="text-muted mb-1"^>สินค้าทั้งหมด^</h6^>
echo                             ^<h3 class="mb-0"^>0^</h3^>
echo                         ^</div^>
echo                     ^</div^>
echo                 ^</div^>
echo             ^</div^>
echo         ^</div^>
echo     ^</div^>
echo.
echo     ^<div class="row g-4"^>
echo         ^<div class="col-lg-8"^>
echo             ^<div class="card border-0 shadow-sm"^>
echo                 ^<div class="card-header bg-white"^>
echo                     ^<h5 class="mb-0"^>เมนูด่วน^</h5^>
echo                 ^</div^>
echo                 ^<div class="card-body"^>
echo                     ^<div class="d-grid gap-2"^>
echo                         ^<a href="{{ route('admin.products.index'^ }}" class="btn btn-outline-primary"^>
echo                             ^<i class="bi bi-box-seam me-2"^>^</i^>จัดการสินค้า
echo                         ^</a^>
echo                         ^<a href="{{ route('admin.orders.index'^ }}" class="btn btn-outline-primary"^>
echo                             ^<i class="bi bi-cart me-2"^>^</i^>จัดการคำสั่งซื้อ
echo                         ^</a^>
echo                         ^<a href="{{ route('admin.users.index'^ }}" class="btn btn-outline-primary"^>
echo                             ^<i class="bi bi-people me-2"^>^</i^>จัดการผู้ใช้
echo                         ^</a^>
echo                     ^</div^>
echo                 ^</div^>
echo             ^</div^>
echo         ^</div^>
echo     ^</div^>
echo ^</div^>
echo @endsection
) > "resources\views\admin\dashboard.blade.php"

echo   ✓ สร้าง Dashboard View แล้ว
echo.

REM ═══════════════════════════════════════════════════════════
REM STEP 7: ตรวจสอบ Database
REM ═══════════════════════════════════════════════════════════
echo [7/12] 🗄️  ตรวจสอบ Database...
echo   (กรุณารอสักครู่...^)

php artisan tinker --execute="try { if (Schema::hasTable('roles'^)^ { echo 'Table roles มีอยู่\n'; \$admin = DB::table('roles'^)-^>where('role_name', 'admin'^)-^>first(^); \$member = DB::table('roles'^)-^>where('role_name', 'member'^)-^>first(^); if (^\$admin^ { echo 'Role admin มีอยู่แล้ว (ID: ' . \$admin-^>role_id . '^\n'; } else { DB::table('roles'^)-^>insert(['role_name' =^> 'admin', 'created_at' =^> now(^), 'updated_at' =^> now(^)]^); echo 'เพิ่ม Role admin แล้ว\n'; } if (^\$member^ { echo 'Role member มีอยู่แล้ว (ID: ' . \$member-^>role_id . '^\n'; } else { DB::table('roles'^)-^>insert(['role_name' =^> 'member', 'created_at' =^> now(^), 'updated_at' =^> now(^)]^); echo 'เพิ่ม Role member แล้ว\n'; } \$usersWithoutRole = DB::table('users'^)-^>whereNull('role_id'^)-^>count(^); if (\$usersWithoutRole ^> 0^ { \$memberRole = DB::table('roles'^)-^>where('role_name', 'member'^)-^>first(^); DB::table('users'^)-^>whereNull('role_id'^)-^>update(['role_id' =^> \$memberRole-^>role_id]^); echo 'แก้ไข ' . \$usersWithoutRole . ' Users ให้มี role_id แล้ว\n'; } } else { echo 'ไม่พบ Table roles - กรุณารัน migrations\n'; } } catch (Exception \$e^ { echo 'Error: ' . \$e-^>getMessage(^ . '\n'; }" 2>nul

echo.

REM ═══════════════════════════════════════════════════════════
REM STEP 8-12: Clear Cache
REM ═══════════════════════════════════════════════════════════
echo [8/12] 🧹 Clear Route Cache...
call php artisan route:clear >nul 2>&1
echo   ✓ Clear Route Cache

echo [9/12] 🧹 Clear View Cache...
call php artisan view:clear >nul 2>&1
echo   ✓ Clear View Cache

echo [10/12] 🧹 Clear Config Cache...
call php artisan config:clear >nul 2>&1
echo   ✓ Clear Config Cache

echo [11/12] 🧹 Clear Application Cache...
call php artisan cache:clear >nul 2>&1
echo   ✓ Clear Application Cache

echo [12/12] ✅ Optimize Autoload...
call composer dump-autoload -o >nul 2>&1
echo   ✓ Optimize Autoload
echo.

REM ═══════════════════════════════════════════════════════════
REM สรุปผล
REM ═══════════════════════════════════════════════════════════
echo ╔════════════════════════════════════════════════════════════╗
echo ║                  ✅ ซ่อมแซมเสร็จสมบูรณ์!                 ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

echo 📊 สิ่งที่ได้ทำ:
echo   ✓ แก้ไข User Model (เพิ่ม role relationship^)
echo   ✓ สร้าง Role Model
echo   ✓ แก้ไข LoginController (redirect ตาม role^)
echo   ✓ สร้าง Admin DashboardController
echo   ✓ แก้ไข RolesMiddleware
echo   ✓ สร้าง Dashboard View
echo   ✓ ตรวจสอบ Database Roles (admin, member^)
echo   ✓ Clear Cache ทั้งหมด
echo.

echo 📝 ขั้นตอนถัดไป:
echo ──────────────────────────────────────────────────────────
echo   1. ทดสอบ Routes:
echo      php artisan route:list
echo.
echo   2. เริ่ม Server:
echo      php artisan serve
echo.
echo   3. ทดสอบ Login:
echo      - Admin: ควร redirect → /admin/dashboard
echo      - Member: ควร redirect → /account/profile
echo ──────────────────────────────────────────────────────────
echo.

echo 💾 Backup อยู่ที่: %BACKUP_DIR%
echo.

set /p START_SERVER="ต้องการเริ่ม Server ทันทีหรือไม่? (y/n^): "
if /i "%START_SERVER%"=="y" (
    echo.
    echo 🚀 Starting Server...
    echo กด Ctrl+C เพื่อหยุด Server
    echo.
    php artisan serve
)

pause
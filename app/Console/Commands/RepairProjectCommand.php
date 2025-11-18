<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class RepairProjectCommand extends Command
{
    protected $signature = 'project:repair 
                            {--check : เช็คอย่างเดียว ไม่แก้ไข}
                            {--force : บังคับแก้ไขทุกอย่าง}
                            {--skip-db : ข้ามการแก้ไข Database}';
    
    protected $description = 'ซ่อมแซมและแก้ไขปัญหาทั้งหมดในโปรเจค';

    protected $issues = [];
    protected $fixed = [];
    protected $errors = [];

    public function handle()
    {
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║        🔧 ระบบซ่อมแซมโปรเจคอัตโนมัติ                    ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();

        if (!$this->option('check')) {
            if (!$this->confirm('⚠️  คำเตือน: คำสั่งนี้จะแก้ไขไฟล์หลายตัว ต้องการดำเนินการต่อหรือไม่?')) {
                $this->warn('ยกเลิกการดำเนินการ');
                return 0;
            }
        }

        // สร้าง Backup
        $this->createFullBackup();

        // เริ่มซ่อมแซม
        $this->info('🔍 เริ่มตรวจสอบและซ่อมแซม...');
        $this->newLine();

        $this->step1_CheckEnvironment();
        $this->step2_RepairDatabase();
        $this->step3_RepairModels();
        $this->step4_RepairControllers();
        $this->step5_RepairMiddleware();
        $this->step6_RepairRoutes();
        $this->step7_RepairViews();
        $this->step8_RepairAuth();
        $this->step9_RepairPermissions();
        $this->step10_ClearCache();

        // สรุปผล
        $this->showSummary();

        return 0;
    }

    // ═══════════════════════════════════════════════════════════
    // STEP 0: สร้าง Backup
    // ═══════════════════════════════════════════════════════════
   protected function createFullBackup()
{
    $this->info('💾 กำลังสร้าง Backup...');
    
    $backupDir = storage_path('backups/full-repair-' . date('Y-m-d-His'));
    
    // ✅ สร้างโฟลเดอร์ทั้งหมดก่อน
    File::makeDirectory($backupDir, 0755, true, true);
    File::makeDirectory($backupDir . '/controllers', 0755, true, true);
    File::makeDirectory($backupDir . '/middleware', 0755, true, true);
    File::makeDirectory($backupDir . '/models', 0755, true, true);
    File::makeDirectory($backupDir . '/routes', 0755, true, true);
    File::makeDirectory($backupDir . '/migrations', 0755, true, true);
    File::makeDirectory($backupDir . '/views', 0755, true, true);

    $itemsToBackup = [
        'app/Http/Controllers' => 'controllers',
        'app/Http/Middleware' => 'middleware',
        'app/Models' => 'models',
        'routes/web.php' => 'routes/web.php',
        'database/migrations' => 'migrations',
        'resources/views' => 'views',
        '.env' => '.env',
    ];

    foreach ($itemsToBackup as $source => $dest) {
        $sourcePath = base_path($source);
        $destPath = $backupDir . '/' . $dest;

        if (!File::exists($sourcePath)) {
            $this->warn("  ⚠ ไม่พบ: {$source}");
            continue;
        }

        try {
            if (File::isDirectory($sourcePath)) {
                File::copyDirectory($sourcePath, $destPath);
            } else {
                // ✅ สร้างโฟลเดอร์ปลายทางก่อน copy ไฟล์
                $destDir = dirname($destPath);
                if (!File::exists($destDir)) {
                    File::makeDirectory($destDir, 0755, true, true);
                }
                File::copy($sourcePath, $destPath);
            }
        } catch (\Exception $e) {
            $this->warn("  ⚠ ไม่สามารถ backup {$source}: " . $e->getMessage());
        }
    }

    $this->info("  ✓ Backup ที่: {$backupDir}");
    $this->newLine();
}

    // ═══════════════════════════════════════════════════════════
    // STEP 1: ตรวจสอบ Environment
    // ═══════════════════════════════════════════════════════════
    protected function step1_CheckEnvironment()
    {
        $this->info('[1/10] 🔍 ตรวจสอบ Environment...');

        // ตรวจสอบ PHP Version
        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '8.1.0', '<')) {
            $this->issues[] = "PHP Version: {$phpVersion} (แนะนำ >= 8.1)";
        } else {
            $this->line("  ✓ PHP Version: {$phpVersion}");
        }

        // ตรวจสอบ Laravel Version
        $laravelVersion = app()->version();
        $this->line("  ✓ Laravel Version: {$laravelVersion}");

        // ตรวจสอบ .env
        if (!File::exists(base_path('.env'))) {
            $this->issues[] = "ไม่พบไฟล์ .env";
            $this->error("  ✗ ไม่พบ .env");
            
            if (!$this->option('check')) {
                File::copy(base_path('.env.example'), base_path('.env'));
                $this->fixed[] = "สร้างไฟล์ .env จาก .env.example";
                $this->info("  ✓ สร้างไฟล์ .env แล้ว");
            }
        } else {
            $this->line("  ✓ ไฟล์ .env มีอยู่");
        }

        // ตรวจสอบ APP_KEY
        if (empty(config('app.key'))) {
            $this->issues[] = "APP_KEY ว่างเปล่า";
            
            if (!$this->option('check')) {
                Artisan::call('key:generate');
                $this->fixed[] = "สร้าง APP_KEY";
                $this->info("  ✓ สร้าง APP_KEY แล้ว");
            }
        } else {
            $this->line("  ✓ APP_KEY กำหนดแล้ว");
        }

        // ตรวจสอบ Storage Permissions
        $directories = [
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
        ];

        foreach ($directories as $dir) {
            if (!File::isWritable($dir)) {
                $this->issues[] = "โฟลเดอร์ {$dir} ไม่มีสิทธิ์เขียน";
                $this->warn("  ⚠ {$dir} ไม่มีสิทธิ์เขียน");
            }
        }

        $this->newLine();
    }

    // ═══════════════════════════════════════════════════════════
    // STEP 2: ซ่อมแซม Database
    // ═══════════════════════════════════════════════════════════
    protected function step2_RepairDatabase()
    {
        if ($this->option('skip-db')) {
            $this->warn('[2/10] ⏭️  ข้าม Database Repair');
            $this->newLine();
            return;
        }

        $this->info('[2/10] 🗄️  ซ่อมแซม Database...');

        try {
            DB::connection()->getPdo();
            $this->line("  ✓ เชื่อมต่อ Database สำเร็จ");

            // ตรวจสอบ Roles Table
            if (Schema::hasTable('roles')) {
                $this->line("  ✓ Table 'roles' มีอยู่");
                
                // ตรวจสอบข้อมูล Roles
                $adminRole = DB::table('roles')->where('role_name', 'admin')->first();
                $memberRole = DB::table('roles')->where('role_name', 'member')->first();

                if (!$adminRole) {
                    $this->issues[] = "ไม่พบ Role 'admin'";
                    
                    if (!$this->option('check')) {
                        DB::table('roles')->insert([
                            'role_name' => 'admin',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $this->fixed[] = "เพิ่ม Role 'admin'";
                        $this->info("  ✓ เพิ่ม Role 'admin' แล้ว");
                    }
                } else {
                    $this->line("  ✓ Role 'admin' มีอยู่ (ID: {$adminRole->role_id})");
                }

                if (!$memberRole) {
                    $this->issues[] = "ไม่พบ Role 'member'";
                    
                    if (!$this->option('check')) {
                        DB::table('roles')->insert([
                            'role_name' => 'member',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $this->fixed[] = "เพิ่ม Role 'member'";
                        $this->info("  ✓ เพิ่ม Role 'member' แล้ว");
                    }
                } else {
                    $this->line("  ✓ Role 'member' มีอยู่ (ID: {$memberRole->role_id})");
                }

            } else {
                $this->issues[] = "ไม่พบ Table 'roles'";
                $this->error("  ✗ ไม่พบ Table 'roles' - กรุณารัน migrations");
            }

            // ตรวจสอบ Users Table
            if (Schema::hasTable('users')) {
                $this->line("  ✓ Table 'users' มีอยู่");
                
                // ตรวจสอบ Users ที่ไม่มี role_id
                $usersWithoutRole = DB::table('users')->whereNull('role_id')->count();
                if ($usersWithoutRole > 0) {
                    $this->issues[] = "มี {$usersWithoutRole} Users ที่ไม่มี role_id";
                    
                    if (!$this->option('check')) {
                        $memberRole = DB::table('roles')->where('role_name', 'member')->first();
                        if ($memberRole) {
                            DB::table('users')->whereNull('role_id')
                                ->update(['role_id' => $memberRole->role_id]);
                            $this->fixed[] = "กำหนด role_id ให้ Users ที่ไม่มี Role";
                            $this->info("  ✓ แก้ไข {$usersWithoutRole} Users แล้ว");
                        }
                    }
                }

            } else {
                $this->issues[] = "ไม่พบ Table 'users'";
                $this->error("  ✗ ไม่พบ Table 'users' - กรุณารัน migrations");
            }

        } catch (\Exception $e) {
            $this->errors[] = "Database Error: " . $e->getMessage();
            $this->error("  ✗ ไม่สามารถเชื่อมต่อ Database: " . $e->getMessage());
        }

        $this->newLine();
    }

    // ═══════════════════════════════════════════════════════════
    // STEP 3: ซ่อมแซม Models
    // ═══════════════════════════════════════════════════════════
    protected function step3_RepairModels()
    {
        $this->info('[3/10] 📦 ซ่อมแซม Models...');

        // ตรวจสอบ User Model
        $this->repairUserModel();
        
        // ตรวจสอบ Role Model
        $this->repairRoleModel();

        $this->newLine();
    }

    protected function repairUserModel()
    {
        $userModelPath = app_path('Models/User.php');
        
        if (!File::exists($userModelPath)) {
            $this->issues[] = "ไม่พบ User Model";
            return;
        }

        $content = File::get($userModelPath);
        $needUpdate = false;

        // ตรวจสอบ role relationship
        if (!str_contains($content, 'function role()')) {
            $this->issues[] = "User Model ไม่มี role() relationship";
            $needUpdate = true;
        } else {
            $this->line("  ✓ User Model มี role() relationship");
        }

        // ตรวจสอบ fillable
        if (!str_contains($content, 'role_id')) {
            $this->issues[] = "User Model fillable ไม่มี role_id";
            $needUpdate = true;
        } else {
            $this->line("  ✓ User Model fillable มี role_id");
        }

        if ($needUpdate && !$this->option('check')) {
            $this->createFixedUserModel();
            $this->fixed[] = "แก้ไข User Model";
            $this->info("  ✓ แก้ไข User Model แล้ว");
        }
    }

    protected function createFixedUserModel()
    {
        $content = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        'full_name',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: User belongsTo Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return strtolower($this->role?->role_name ?? '') === 'admin';
    }

    /**
     * Check if user is member
     */
    public function isMember(): bool
    {
        return strtolower($this->role?->role_name ?? '') === 'member';
    }
}
PHP;

        File::put(app_path('Models/User.php'), $content);
    }

    protected function repairRoleModel()
    {
        $roleModelPath = app_path('Models/Role.php');
        
        if (!File::exists($roleModelPath)) {
            $this->issues[] = "ไม่พบ Role Model";
            
            if (!$this->option('check')) {
                $this->createRoleModel();
                $this->fixed[] = "สร้าง Role Model";
                $this->info("  ✓ สร้าง Role Model แล้ว");
            }
        } else {
            $this->line("  ✓ Role Model มีอยู่");
        }
    }

    protected function createRoleModel()
    {
        $content = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_name',
        'description',
    ];

    /**
     * Relationship: Role hasMany Users
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
PHP;

        File::put(app_path('Models/Role.php'), $content);
    }

    // ═══════════════════════════════════════════════════════════
    // STEP 4: ซ่อมแซม Controllers
    // ═══════════════════════════════════════════════════════════
    protected function step4_RepairControllers()
    {
        $this->info('[4/10] 🎮 ซ่อมแซม Controllers...');

        // ซ่อม LoginController
        $this->repairLoginController();
        
        // ซ่อม DashboardController
        $this->repairDashboardController();

        $this->newLine();
    }

    protected function repairLoginController()
    {
        $path = app_path('Http/Controllers/LoginController.php');
        
        if (!File::exists($path)) {
            $this->issues[] = "ไม่พบ LoginController";
            
            if (!$this->option('check')) {
                $this->createLoginController();
                $this->fixed[] = "สร้าง LoginController";
                $this->info("  ✓ สร้าง LoginController แล้ว");
            }
            return;
        }

        $content = File::get($path);
        
        // ตรวจสอบ redirect logic
        if (!str_contains($content, "route('admin.dashboard')")) {
            $this->issues[] = "LoginController ไม่มี redirect ไป admin.dashboard";
            
            if (!$this->option('check')) {
                $this->createLoginController();
                $this->fixed[] = "แก้ไข LoginController redirect logic";
                $this->info("  ✓ แก้ไข LoginController แล้ว");
            }
        } else {
            $this->line("  ✓ LoginController มี redirect logic ถูกต้อง");
        }
    }

    protected function createLoginController()
    {
        $content = <<<'PHP'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $roleName = strtolower($user->role?->role_name ?? '');

            if ($roleName === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
            
            return redirect()->intended(route('account.profile'));
        }

        return back()->withErrors([
            'username' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
PHP;

        File::put(app_path('Http/Controllers/LoginController.php'), $content);
    }

    protected function repairDashboardController()
    {
        $path = app_path('Http/Controllers/Admin/DashboardController.php');
        
        if (!File::exists($path)) {
            $this->issues[] = "ไม่พบ Admin/DashboardController";
            
            if (!$this->option('check')) {
                File::ensureDirectoryExists(app_path('Http/Controllers/Admin'));
                $this->createDashboardController();
                $this->fixed[] = "สร้าง Admin/DashboardController";
                $this->info("  ✓ สร้าง Admin/DashboardController แล้ว");
            }
        } else {
            $this->line("  ✓ Admin/DashboardController มีอยู่");
        }
    }

    protected function createDashboardController()
    {
        $content = <<<'PHP'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function refreshCache()
    {
        return redirect()->route('admin.dashboard')
            ->with('success', 'Cache ถูก refresh แล้ว');
    }
}
PHP;

        File::put(app_path('Http/Controllers/Admin/DashboardController.php'), $content);
    }

    // ═══════════════════════════════════════════════════════════
    // STEP 5: ซ่อมแซม Middleware
    // ═══════════════════════════════════════════════════════════
    protected function step5_RepairMiddleware()
    {
        $this->info('[5/10] 🛡️  ซ่อมแซม Middleware...');

        $path = app_path('Http/Middleware/RolesMiddleware.php');
        
        if (!File::exists($path)) {
            $this->issues[] = "ไม่พบ RolesMiddleware";
            
            if (!$this->option('check')) {
                $this->createRolesMiddleware();
                $this->fixed[] = "สร้าง RolesMiddleware";
                $this->info("  ✓ สร้าง RolesMiddleware แล้ว");
            }
        } else {
            $this->line("  ✓ RolesMiddleware มีอยู่");
            
            // ตรวจสอบเนื้อหา
            $content = File::get($path);
            if (!str_contains($content, 'strtolower')) {
                $this->issues[] = "RolesMiddleware อาจมีปัญหาการเปรียบเทียบ Role";
                
                if (!$this->option('check')) {
                    $this->createRolesMiddleware();
                    $this->fixed[] = "แก้ไข RolesMiddleware";
                    $this->info("  ✓ แก้ไข RolesMiddleware แล้ว");
                }
            }
        }

        $this->newLine();
    }

    protected function createRolesMiddleware()
    {
        $content = <<<'PHP'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolesMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = auth()->user();
        $userRole = strtolower($user->role?->role_name ?? '');
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            }
            
            return redirect()->route('account.profile')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return $next($request);
    }
}
PHP;

        File::put(app_path('Http/Middleware/RolesMiddleware.php'), $content);
    }

    // ═══════════════════════════════════════════════════════════
    // STEP 6-10: (ต่อในส่วนถัดไป)
    // ═══════════════════════════════════════════════════════════

    protected function step6_RepairRoutes()
    {
        $this->info('[6/10] 🗺️  ตรวจสอบ Routes...');
        
        $routesPath = base_path('routes/web.php');
        $content = File::get($routesPath);

        // ตรวจสอบ namespace
        if (!str_contains($content, 'App\Http\Controllers\Admin')) {
            $this->issues[] = "Routes ยังไม่อัปเดต namespace";
            $this->warn("  ⚠ Routes ควรอัปเดต namespace");
        } else {
            $this->line("  ✓ Routes namespace ถูกต้อง");
        }

        $this->newLine();
    }

    protected function step7_RepairViews()
    {
        $this->info('[7/10] 👁️  ตรวจสอบ Views...');

        $viewPath = resource_path('views/admin/dashboard.blade.php');
        
        if (!File::exists($viewPath)) {
            $this->issues[] = "ไม่พบ admin/dashboard.blade.php";
            
            if (!$this->option('check')) {
                File::ensureDirectoryExists(resource_path('views/admin'));
                $this->createDashboardView();
                $this->fixed[] = "สร้าง admin/dashboard.blade.php";
                $this->info("  ✓ สร้าง dashboard view แล้ว");
            }
        } else {
            $this->line("  ✓ admin/dashboard.blade.php มีอยู่");
        }

        $this->newLine();
    }

    protected function createDashboardView()
    {
        $content = <<<'BLADE'
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">Dashboard</h1>
    <p>ยินดีต้อนรับ, {{ auth()->user()->username }} (Admin)</p>
    
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>สินค้าทั้งหมด</h5>
                    <h2>0</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE;

        File::put(resource_path('views/admin/dashboard.blade.php'), $content);
    }

    protected function step8_RepairAuth()
    {
        $this->info('[8/10] 🔐 ตรวจสอบ Authentication...');
        
        // ตรวจสอบ Auth config
        $guard = config('auth.defaults.guard');
        $this->line("  ✓ Default Guard: {$guard}");
        
        $this->newLine();
    }

    protected function step9_RepairPermissions()
    {
        $this->info('[9/10] 🔒 ตรวจสอบ File Permissions...');
        
        $directories = [
            storage_path(),
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
        ];

        foreach ($directories as $dir) {
            if (File::isWritable($dir)) {
                $this->line("  ✓ {$dir}");
            } else {
                $this->warn("  ⚠ {$dir} ไม่มีสิทธิ์เขียน");
            }
        }

        $this->newLine();
    }

    protected function step10_ClearCache()
    {
        $this->info('[10/10] 🧹 Clear Cache...');

        if (!$this->option('check')) {
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            
            $this->fixed[] = "Clear Cache ทั้งหมด";
            $this->info("  ✓ Clear Cache เรียบร้อย");
        } else {
            $this->line("  ⚠ ข้าม (check mode)");
        }

        $this->newLine();
    }

    // ═══════════════════════════════════════════════════════════
    // แสดงสรุปผล
    // ═══════════════════════════════════════════════════════════
    protected function showSummary()
    {
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║                    📊 สรุปผลการซ่อมแซม                    ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();

        if (!empty($this->issues)) {
            $this->warn('⚠️  ปัญหาที่พบ: ' . count($this->issues));
            foreach ($this->issues as $issue) {
                $this->line("  • {$issue}");
            }
            $this->newLine();
        }

        if (!empty($this->fixed)) {
            $this->info('✅ แก้ไขแล้ว: ' . count($this->fixed));
            foreach ($this->fixed as $fix) {
                $this->line("  • {$fix}");
            }
            $this->newLine();
        }

        if (!empty($this->errors)) {
            $this->error('❌ ข้อผิดพลาด: ' . count($this->errors));
            foreach ($this->errors as $error) {
                $this->line("  • {$error}");
            }
            $this->newLine();
        }

        if ($this->option('check')) {
            $this->comment('💡 นี่เป็นโหมดเช็คเท่านั้น ไม่มีการเปลี่ยนแปลงไฟล์');
            $this->info('รัน: php artisan project:repair เพื่อดำเนินการจริง');
        } else {
            $this->info('✅ ซ่อมแซมเสร็จสมบูรณ์!');
            $this->newLine();
            
            $this->comment('📝 ขั้นตอนถัดไป:');
            $this->line('  1. ทดสอบ Login ด้วย Admin');
            $this->line('  2. ตรวจสอบว่า redirect ไป /admin/dashboard');
            $this->line('  3. ทดสอบ Login ด้วย Member');
            $this->line('  4. ตรวจสอบว่า redirect ไป /account/profile');
            $this->newLine();
            
            $this->info('🚀 รัน: php artisan serve');
        }
    }
}
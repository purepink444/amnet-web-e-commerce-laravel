<?php

namespace App\\Http\\Controllers\\Web\\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Role, Member};
use App\Services\SweetAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, DB, Log};
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    protected $sweetAlert;

    public function __construct(SweetAlertService $sweetAlert)
    {
        $this->sweetAlert = $sweetAlert;
    }

    public function index(Request $request)
    {
        // Sorting parameters
        $sortBy = $request->get('sort', 'user_id');
        $sortDirection = $request->get('direction', 'asc');

        // Validate sort parameters
        $allowedSorts = ['user_id', 'username', 'email', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'user_id';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query = User::with(['role', 'member']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('member', function($memberQuery) use ($search) {
                    $memberQuery->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                ->orWhere('username', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->orderBy($sortBy, $sortDirection)->paginate(15)->appends($request->query());

        return view('admin.users.index', compact('users', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,role_id',
            'prefix' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:10',
        ]);

        try {
            DB::beginTransaction();

            // Separate User and Member data
            $userData = [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ];

            $memberData = [
                'prefix' => $validated['prefix'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'province' => $validated['province'],
                'district' => $validated['district'],
                'subdistrict' => $validated['subdistrict'],
                'postal_code' => $validated['zipcode'],
            ];

            // Generate display_id for the new user
            $userData['display_id'] = User::generateDisplayId();

            // Create User
            $user = User::create($userData);

            // Create Member with user_id
            $memberData['user_id'] = $user->user_id;
            Member::create($memberData);

            DB::commit();

            $this->sweetAlert->created('ผู้ใช้');
            return redirect()->route('admin.users.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User creation failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    }

    public function show($id)
    {
        $user = User::with(['role', 'member', 'orders' => function($query) {
            $query->with('orderItems.product')->latest()->take(10);
        }])->findOrFail($id);

        // Calculate comprehensive statistics
        $totalOrders = $user->orders->count();
        $totalSpent = $user->orders->sum('total_amount');
        $averageOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;

        // Order status breakdown
        $orderStatuses = $user->orders->groupBy('status')->map->count();

        // Monthly spending trend (last 6 months)
        $monthlySpending = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthOrders = $user->orders->filter(function($order) use ($date) {
                return $order->created_at->format('Y-m') === $date->format('Y-m');
            });
            $monthlySpending->push([
                'month' => $date->format('M Y'),
                'amount' => $monthOrders->sum('total_amount'),
                'orders' => $monthOrders->count()
            ]);
        }

        // Favorite products (most ordered)
        $favoriteProducts = collect();
        $user->orders->each(function($order) use (&$favoriteProducts) {
            $order->orderItems->each(function($item) use (&$favoriteProducts) {
                $productId = $item->product_id;
                if (!isset($favoriteProducts[$productId])) {
                    $favoriteProducts[$productId] = [
                        'product' => $item->product,
                        'quantity' => 0,
                        'total_spent' => 0
                    ];
                }
                $favoriteProducts[$productId]['quantity'] += $item->quantity;
                $favoriteProducts[$productId]['total_spent'] += $item->total_price;
            });
        });
        $favoriteProducts = $favoriteProducts->sortByDesc('quantity')->take(5);

        // Recent activity (mock data for now - in real app, you'd have an activity log)
        $recentActivity = collect([
            [
                'type' => 'login',
                'description' => 'เข้าสู่ระบบ',
                'timestamp' => now()->subHours(2),
                'icon' => 'bi bi-box-arrow-in-right'
            ],
            [
                'type' => 'order',
                'description' => 'สั่งซื้อสินค้า',
                'timestamp' => $user->orders->first()?->created_at ?? now()->subDays(1),
                'icon' => 'bi bi-cart-check'
            ],
            [
                'type' => 'profile_update',
                'description' => 'อัพเดทข้อมูลส่วนตัว',
                'timestamp' => $user->updated_at,
                'icon' => 'bi bi-person-gear'
            ]
        ]);

        $stats = [
            'total_orders' => $totalOrders,
            'total_spent' => $totalSpent,
            'average_order_value' => $averageOrderValue,
            'order_statuses' => $orderStatuses,
            'monthly_spending' => $monthlySpending,
            'favorite_products' => $favoriteProducts,
            'recent_activity' => $recentActivity,
            'member_since' => $user->created_at->diffInDays(now()),
            'last_order' => $user->orders->max('created_at'),
            'account_status' => $user->is_active ? 'active' : 'inactive'
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit($id)
    {
        $user = User::with('member')->findOrFail($id);
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,role_id',
            'prefix' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:10',
        ]);

        try {
            DB::beginTransaction();

            // Separate User and Member data
            $userData = [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'role_id' => $validated['role_id'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ];

            $memberData = [
                'prefix' => $validated['prefix'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'province' => $validated['province'],
                'district' => $validated['district'],
                'subdistrict' => $validated['subdistrict'],
                'postal_code' => $validated['zipcode'],
            ];

            // Handle password update
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            // Update User model
            $user->update($userData);

            // Update or create Member model
            if ($user->member) {
                $user->member->update($memberData);
            } else {
                $memberData['user_id'] = $user->user_id;
                Member::create($memberData);
            }

            DB::commit();

            $this->sweetAlert->updated('ผู้ใช้');
            return redirect()->route('admin.users.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting self
        if ($user->user_id === auth()->id()) {
            $this->sweetAlert->error('ไม่สามารถดำเนินการได้', 'ไม่สามารถลบผู้ใช้ตัวเองได้');
            return redirect()->route('admin.users.index');
        }

        // Check if user has orders
        if ($user->orders()->count() > 0) {
            $this->sweetAlert->error('ไม่สามารถดำเนินการได้', 'ไม่สามารถลบผู้ใช้ที่มีคำสั่งซื้อได้');
            return redirect()->route('admin.users.index');
        }

        try {
            DB::beginTransaction();

            $deletedDisplayId = $user->display_id;

            // Delete the user
            $user->delete();

            // Recalculate display_ids to fill the gap
            $users = User::orderBy('user_id')->get();
            $displayId = 1;
            foreach ($users as $remainingUser) {
                $remainingUser->update(['display_id' => $displayId]);
                $displayId++;
            }

            DB::commit();

            $this->sweetAlert->deleted('ผู้ใช้');
            return redirect()->route('admin.users.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User deletion failed: ' . $e->getMessage());

            return back()->with('error', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    }

    /**
     * Handle bulk actions for users
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,user_id',
            'action' => 'required|in:activate,deactivate,delete,change_role'
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;

        try {
            DB::beginTransaction();

            switch ($action) {
                case 'activate':
                    User::whereIn('user_id', $userIds)->update(['is_active' => 1]);
                    $message = 'เปิดใช้งานผู้ใช้เรียบร้อยแล้ว';
                    break;

                case 'deactivate':
                    User::whereIn('user_id', $userIds)->update(['is_active' => 0]);
                    $message = 'ปิดใช้งานผู้ใช้เรียบร้อยแล้ว';
                    break;

                case 'change_role':
                    $newRoleId = $request->new_role_id;
                    if (!$newRoleId || !Role::find($newRoleId)) {
                        throw new \Exception('บทบาทไม่ถูกต้อง');
                    }
                    User::whereIn('user_id', $userIds)->update(['role_id' => $newRoleId]);
                    $message = 'เปลี่ยนบทบาทผู้ใช้เรียบร้อยแล้ว';
                    break;

                case 'delete':
                    // Prevent deleting self
                    if (in_array(auth()->id(), $userIds)) {
                        throw new \Exception('ไม่สามารถลบผู้ใช้ตัวเองได้');
                    }

                    // Check if users have orders
                    $usersWithOrders = User::whereIn('user_id', $userIds)
                        ->whereHas('orders')
                        ->pluck('username')
                        ->toArray();

                    if (!empty($usersWithOrders)) {
                        throw new \Exception('ไม่สามารถลบผู้ใช้ที่มีคำสั่งซื้อได้: ' . implode(', ', $usersWithOrders));
                    }

                    User::whereIn('user_id', $userIds)->delete();
                    $message = 'ลบผู้ใช้เรียบร้อยแล้ว';
                    break;
            }

            DB::commit();

            $this->sweetAlert->success('ดำเนินการสำเร็จ', $message);
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk user action failed: ' . $e->getMessage());

            $this->sweetAlert->error('เกิดข้อผิดพลาด', $e->getMessage());
            return back();
        }
    }

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        $query = User::with(['role', 'member']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('member', function($memberQuery) use ($search) {
                    $memberQuery->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                ->orWhere('username', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $users = $query->orderBy('user_id')->get();

        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID', 'Display ID', 'Username', 'Email', 'Phone', 'Full Name',
                'Role', 'Status', 'Province', 'Registration Date', 'Last Updated'
            ]);

            // CSV data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->user_id,
                    $user->getDisplayId(),
                    $user->username,
                    $user->email,
                    $user->phone,
                    $user->member ? trim(($user->member->prefix ?? '') . ' ' . $user->member->first_name . ' ' . $user->member->last_name) : '',
                    $user->role?->role_name ?? '',
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->province ?? '',
                    $user->created_at?->format('Y-m-d H:i:s'),
                    $user->updated_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

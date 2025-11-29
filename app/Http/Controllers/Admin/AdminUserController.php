<?php

namespace App\Http\Controllers\Admin;

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

        $users = $query->orderBy('user_id', 'asc')->paginate(15)->appends($request->query());

        return view('admin.users.index', compact('users'));
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
            $query->latest()->take(5);
        }])->findOrFail($id);

        return view('admin.users.show', compact('user'));
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
}

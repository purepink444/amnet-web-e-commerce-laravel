<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Role};
use App\Services\SweetAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    protected $sweetAlert;

    public function __construct(SweetAlertService $sweetAlert)
    {
        $this->sweetAlert = $sweetAlert;
    }

    public function index()
    {
        $users = User::with('role')->latest()->paginate(15);

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
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:10',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        $this->sweetAlert->created('ผู้ใช้');
        return redirect()->route('admin.users.index');
    }

    public function show($id)
    {
        $user = User::with(['role', 'orders' => function($query) {
            $query->latest()->take(5);
        }])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
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
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:10',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        $this->sweetAlert->updated('ผู้ใช้');
        return redirect()->route('admin.users.index');
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

        $user->delete();

        $this->sweetAlert->deleted('ผู้ใช้');
        return redirect()->route('admin.users.index');
    }
}

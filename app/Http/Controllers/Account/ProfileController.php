<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update user data
        $user->email = $request->email;
        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update member data
        $member = $user->member;
        if ($member) {
            $member->update([
                'first_name' => $request->name,
                'last_name' => $request->name, // Assuming single name field
                'address' => $request->address,
            ]);
        }

        return redirect()
            ->route('account.profile')
            ->with('success', 'Profile updated successfully.');
    }
}

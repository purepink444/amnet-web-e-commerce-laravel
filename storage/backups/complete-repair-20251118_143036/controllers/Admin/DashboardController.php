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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index');
    }

    public function show($id)
    {
        return view('admin.orders.show', compact('id'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Update order status logic
        return redirect()->back()->with('success', 'อัปเดตสถานะสำเร็จ');
    }
}
<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        // ดึงข้อมูล orders ของ user ที่ login อยู่
        $orders = Auth::user()->orders()
            ->orderBy('created_at', 'desc')
            ->get();
        
        // ส่งข้อมูลไปที่ view
        return view('account.orders', compact('orders'));
    }
    
    public function show($id)
    {
        // ดูรายละเอียด order แต่ละรายการ
        $order = Auth::user()->orders()->findOrFail($id);
        
        return view('account.order-detail', compact('order'));
    }
}
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

    public function cancel($id)
{
    $order = Order::where('id', $id)
                  ->where('user_id', auth()->id())
                  ->firstOrFail();
    
    // ตรวจสอบว่ายกเลิกได้หรือไม่
    if ($order->status !== 'pending') {
        return back()->with('error', 'ไม่สามารถยกเลิกคำสั่งซื้อนี้ได้');
    }
    
    $order->update(['status' => 'cancelled']);
    
    return back()->with('success', 'ยกเลิกคำสั่งซื้อเรียบร้อย');
}
}

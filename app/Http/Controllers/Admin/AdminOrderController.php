<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with([
            'member' => function($query) {
                $query->select('member_id', 'first_name', 'last_name');
            },
            'orderItems' => function($query) {
                $query->with(['product' => function($productQuery) {
                    $productQuery->select('product_id', 'product_name');
                }]);
            }
        ])->latest()->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with([
            'member' => function($query) {
                $query->select('member_id', 'first_name', 'last_name');
            },
            'user' => function($query) {
                $query->select('user_id', 'email', 'phone');
            },
            'orderItems' => function($query) {
                $query->with(['product' => function($productQuery) {
                    $productQuery->select('product_id', 'product_name', 'price', 'image_url');
                }]);
            }
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded'
        ]);

        $order = Order::with([
            'member' => function($query) {
                $query->select('member_id', 'first_name', 'last_name');
            },
            'user' => function($query) {
                $query->select('user_id', 'email');
            }
        ])->findOrFail($id);
        $oldStatus = $order->order_status;

        // Don't send notification if status hasn't changed
        if ($oldStatus === $request->status) {
            return redirect()->back()->with('info', 'สถานะไม่มีการเปลี่ยนแปลง');
        }

        $order->update(['order_status' => $request->status]);

        // Send notification to user
        if ($order->user) {
            // Load member relationship for notification
            $order->user->load('member');
            $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $request->status));
        }

        return redirect()->back()->with('success', 'อัปเดตสถานะคำสั่งซื้อสำเร็จ และส่งการแจ้งเตือนให้ผู้ใช้แล้ว');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        // Optional: Check if order can be deleted (e.g., not delivered)
        if ($order->order_status === 'delivered') {
            return redirect()->route('admin.orders.index')
                ->with('error', 'ไม่สามารถลบคำสั่งซื้อที่จัดส่งแล้วได้');
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'ลบคำสั่งซื้อสำเร็จ');
    }
}

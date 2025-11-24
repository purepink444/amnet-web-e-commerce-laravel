<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        // ดึงข้อมูล orders ของ user ที่ login อยู่ (มี pagination)
        $orders = Auth::user()->orders()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // ส่งข้อมูลไปที่ view
        return view('account.orders', compact('orders'));
    }
    
    public function show($id)
    {
        // ดูรายละเอียด order แต่ละรายการ
        $order = Auth::user()->orders()->where('order_id', $id)->firstOrFail();

        return view('account.orders.show', compact('order'));
    }

    /**
     * Cancel an order with comprehensive business logic
     *
     * Business Rules:
     * - Can cancel within 24 hours of order creation
     * - Only pending and paid orders can be cancelled
     * - Automatically restore product stock
     * - Handle payment refunds for completed payments
     * - Use database transactions for data consistency
     *
     * @param int $id Order ID to cancel
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception For transaction rollback
     */
    public function cancel($id)
    {
        $order = Order::with(['orderItems.product', 'payment'])
                      ->where('order_id', $id)
                      ->where('user_id', auth()->id())
                      ->firstOrFail();

        // ตรวจสอบว่าสามารถยกเลิกได้หรือไม่
        if (!in_array($order->order_status, ['pending', 'paid'])) {
            return back()->with('error', 'ไม่สามารถยกเลิกคำสั่งซื้อนี้ได้ เนื่องจากสถานะปัจจุบันคือ: ' . $order->status_label);
        }

        // ตรวจสอบเวลายกเลิก (ยกเลิกได้ภายใน 24 ชั่วโมงหลังสั่งซื้อ)
        if ($order->created_at->diffInHours(now()) > 24) {
            return back()->with('error', 'ไม่สามารถยกเลิกคำสั่งซื้อได้ เนื่องจากเกินเวลาที่กำหนด (24 ชั่วโมง)');
        }

        try {
            DB::beginTransaction();

            // อัปเดตสถานะ order เป็น cancelled
            $order->update(['order_status' => 'cancelled']);

            // คืน stock ของสินค้า
            foreach ($order->orderItems as $item) {
                if ($item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            // จัดการ payment ถ้ามี
            if ($order->payment) {
                if ($order->payment->status === 'completed') {
                    // สำหรับระบบจริงควรเรียก API refund
                    // แต่ตอนนี้แค่เปลี่ยนสถานะเป็น refunded
                    $order->payment->update([
                        'status' => 'refunded',
                        'payment_data' => array_merge($order->payment->payment_data ?? [], [
                            'refunded_at' => now(),
                            'refund_reason' => 'order_cancelled'
                        ])
                    ]);
                } else {
                    // ถ้ายังไม่ได้ชำระเงิน จริงๆ ก็แค่เปลี่ยนสถานะเป็น cancelled
                    $order->payment->update(['status' => 'cancelled']);
                }
            }

            DB::commit();

            Log::info('Order cancelled successfully', [
                'order_id' => $order->order_id,
                'user_id' => auth()->id(),
                'cancelled_at' => now()
            ]);

            return back()->with('success', 'ยกเลิกคำสั่งซื้อเรียบร้อยแล้ว สินค้าจะถูกคืนเข้าสต็อก');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order cancellation failed', [
                'order_id' => $order->order_id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'เกิดข้อผิดพลาดในการยกเลิกคำสั่งซื้อ กรุณาลองใหม่อีกครั้ง');
        }
    }
}

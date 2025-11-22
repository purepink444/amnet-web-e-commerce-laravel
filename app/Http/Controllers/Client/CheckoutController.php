<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{CartItem, Order, OrderItem, Payment};
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View as ViewContract;

class CheckoutController extends Controller
{
    /**
     * แสดงหน้า checkout
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $cartItems = CartItem::with('product')->where('user_id', $user->user_id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('account.cart.index')
                ->with('error', 'ตะกร้าของคุณว่างเปล่า กรุณาเพิ่มสินค้าก่อน');
        }

        // Create a cart-like object for the view
        $cart = (object) [
            'items' => $cartItems,
            'total_items' => $cartItems->sum('quantity'),
            'total_price' => $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            }),
        ];

        return view('checkout.index', compact('cart'));
    }

    /**
     * ประมวลผลการสั่งซื้อ
     */
    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|in:credit,qr,cod',
            'shipping_address' => 'required|string|max:500',
        ]);

        $user = auth()->user();
        $cartItems = CartItem::with('product')->where('user_id', $user->user_id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('account.cart.index')
                ->with('error', 'ตะกร้าของคุณว่างเปล่า');
        }

        // คำนวณราคารวม
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        // ตรวจสอบ stock อีกครั้ง
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                return redirect()->route('account.cart.index')
                    ->with('error', "สินค้า {$item->product->product_name} มีจำนวนไม่เพียงพอ");
            }
        }

        try {
            // สร้างคำสั่งซื้อ
            $order = Order::create([
                'user_id' => $user->user_id,
                'total_amount' => $totalPrice,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
            ]);

            // สร้างรายการสินค้าในคำสั่งซื้อ
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                // ลดจำนวนสินค้าในสต็อก
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // สร้างการชำระเงิน
            Payment::create([
                'order_id' => $order->order_id,
                'amount' => $totalPrice,
                'payment_method' => $request->payment_method,
                'status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
            ]);

            // ล้างตะกร้า
            CartItem::where('user_id', $user->user_id)->delete();

            return redirect()->route('account.checkout.success', $order->order_id)
                ->with('success', 'คำสั่งซื้อสำเร็จ!');

        } catch (\Exception $e) {
            return redirect()->route('account.checkout.index')
                ->with('error', 'เกิดข้อผิดพลาดในการสั่งซื้อ กรุณาลองใหม่อีกครั้ง');
        }
    }

    /**
     * แสดงหน้า success หลังสั่งซื้อ
     */
    public function success(int $orderId): View
    {
        $user = auth()->user();
        $order = Order::with(['items.product', 'payment'])
            ->where('user_id', $user->user_id)
            ->where('order_id', $orderId)
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}

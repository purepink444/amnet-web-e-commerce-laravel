<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{CartItem, Product};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * แสดงหน้า cart
     */
    public function index(): View
    {
        $user = auth()->user();
        $cartItems = CartItem::with('product')->where('user_id', $user->user_id)->get();

        // Create a simple cart-like object for the view
        $cart = (object) [
            'items' => $cartItems,
            'total_items' => $cartItems->sum('quantity'),
            'total_price' => $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            }),
        ];

        return view('cart.index', compact('cart'));
    }

    /**
     * เพิ่มสินค้าในตะกร้า
     */
    public function add(Request $request, int $productId): JsonResponse
    {
        try {
            $request->validate([
                'quantity' => 'nullable|integer|min:1|max:99',
            ]);

            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาเข้าสู่ระบบก่อน',
                ], 401);
            }

            $quantity = $request->quantity ?? 1;

            // ตรวจสอบสินค้า
            $product = Product::findOrFail($productId);

        if ($product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'สินค้าในสต็อกไม่เพียงพอ',
            ], 400);
        }

        // ตรวจสอบว่ามีสินค้านี้ในตะกร้าแล้วหรือไม่
        $existingItem = CartItem::where('user_id', $user->user_id)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;

            if ($product->stock_quantity < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'สินค้าในสต็อกไม่เพียงพอสำหรับจำนวนที่เพิ่ม',
                ], 400);
            }

            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'user_id' => $user->user_id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price_at_add' => $product->price,
                'added_at' => now(),
            ]);
        }

        $cartCount = CartItem::where('user_id', $user->user_id)->sum('quantity');
        return response()->json([
            'success' => true,
            'message' => 'เพิ่มสินค้าลงตะกร้าแล้ว',
            'cart_count' => $cartCount,
        ]);
        } catch (\Exception $e) {
            \Log::error('Cart add error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * อัปเดตจำนวนสินค้าในตะกร้า
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:1|max:99',
            ]);

            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาเข้าสู่ระบบก่อน',
                ], 401);
            }

            $cartItem = CartItem::with('product')
                ->where('user_id', $user->user_id)
                ->where('product_id', $request->product_id)
                ->firstOrFail();

            // ตรวจสอบ stock
            if ($request->quantity > $cartItem->product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'สินค้าในสต็อกไม่เพียงพอ',
                ], 400);
            }

            $cartItem->update(['quantity' => $request->quantity]);

            return response()->json([
                'success' => true,
                'message' => 'อัปเดตจำนวนแล้ว',
                'subtotal' => number_format($cartItem->subtotal, 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง',
            ], 500);
        }
    }

    /**
     * ลบสินค้าออกจากตะกร้า
     */
    public function remove(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|integer',
            ]);

            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาเข้าสู่ระบบก่อน',
                ], 401);
            }

            $cartItem = CartItem::where('user_id', $user->user_id)
                ->where('product_id', $request->product_id)
                ->firstOrFail();

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'ลบสินค้าออกจากตะกร้าแล้ว',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง',
            ], 500);
        }
    }

    /**
     * รับจำนวนสินค้าในตะกร้า (สำหรับ AJAX)
     */
    public function count(): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'count' => 0,
                ]);
            }

            $count = CartItem::where('user_id', $user->user_id)->sum('quantity');

            return response()->json([
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'count' => 0,
            ], 500);
        }
    }
}

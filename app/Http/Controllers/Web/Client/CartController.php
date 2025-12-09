<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\{AddToCartRequest, UpdateCartRequest, RemoveFromCartRequest};
use App\Http\Resources\ApiResponse;
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
        $member = $user->member;

        if (!$member) {
            $cartItems = collect();
        } else {
            $cartItems = CartItem::with('product')->where('member_id', $member->member_id)->get();
        }

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
     * Add product to shopping cart with member-based architecture
     *
     * Business Logic:
     * - Validates user authentication and member existence
     * - Checks product stock availability
     * - Handles both new items and quantity updates
     * - Uses member_id for proper data isolation
     *
     * @param AddToCartRequest $request Validated request
     * @param int $productId Product identifier
     * @return JsonResponse JSON response with success/error status
     */
    public function add(AddToCartRequest $request, int $productId): JsonResponse
    {
        try {
            $user = auth()->user();
            $member = $user->member;
            $quantity = $request->quantity ?? 1;
            $product = $request->getProduct();

            // Check if item already exists in cart
            $existingItem = CartItem::where('member_id', $member->member_id)
                ->where('product_id', $productId)
                ->first();

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $quantity;
                $existingItem->update(['quantity' => $newQuantity]);
            } else {
                CartItem::create([
                    'member_id' => $member->member_id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_at_add' => $product->price,
                    'added_at' => now(),
                ]);
            }

            $cartCount = CartItem::where('member_id', $member->member_id)->sum('quantity');

            return ApiResponse::success([
                'cart_count' => $cartCount,
                'product_id' => $productId,
                'quantity_added' => $quantity,
            ], 'Product added to cart successfully');

        } catch (\Exception $e) {
            \Log::error('Cart add error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'product_id' => $productId,
                'quantity' => $request->quantity
            ]);

            return ApiResponse::internalError('Failed to add product to cart');
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(UpdateCartRequest $request): JsonResponse
    {
        try {
            $cartItem = $request->getCartItem();
            $cartItem->update(['quantity' => $request->quantity]);

            $subtotal = $cartItem->quantity * $cartItem->product->price;

            return ApiResponse::success([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'subtotal' => number_format($subtotal, 2),
                'unit_price' => number_format($cartItem->product->price, 2),
            ], 'Cart item updated successfully');

        } catch (\Exception $e) {
            \Log::error('Cart update error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);

            return ApiResponse::internalError('Failed to update cart item');
        }
    }

    /**
     * Remove item from cart
     */
    public function remove(RemoveFromCartRequest $request): JsonResponse
    {
        try {
            $cartItem = $request->getCartItem();
            $cartItem->delete();

            return ApiResponse::success([
                'product_id' => $request->product_id,
            ], 'Product removed from cart successfully');

        } catch (\Exception $e) {
            \Log::error('Cart remove error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'product_id' => $request->product_id
            ]);

            return ApiResponse::internalError('Failed to remove product from cart');
        }
    }

    /**
     * Get cart item count
     */
    public function count(): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->member) {
                return ApiResponse::success(['count' => 0]);
            }

            $count = CartItem::where('member_id', $user->member->member_id)->sum('quantity');

            return ApiResponse::success(['count' => (int) $count]);

        } catch (\Exception $e) {
            \Log::error('Cart count error: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);

            return ApiResponse::success(['count' => 0]);
        }
    }
}

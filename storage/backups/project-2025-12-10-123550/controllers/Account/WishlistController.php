<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class WishlistController extends Controller
{
    /**
     * แสดงรายการสินค้าที่ชอบทั้งหมด
     */
    public function index()
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            // ถ้ายังไม่มี member record ให้สร้าง
            $member = $user->member()->create([
                'first_name' => $user->firstname ?? 'Unknown',
                'last_name' => $user->lastname ?? 'User',
                'membership_level' => 'bronze',
                'points' => 0,
            ]);
        }

        // ดึง wishlist items พร้อมข้อมูลสินค้า
        $wishlist = $member->wishlists()
            ->with(['product' => function ($query) {
                $query->select('product_id', 'product_name', 'price', 'image_url', 'stock_quantity');
            }])
            ->paginate(12);

        return view('account.wishlist', compact('wishlist'));
    }

    /**
     * เพิ่ม/ลบสินค้าออกจากรายการโปรด
     */
    public function toggle($productId)
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลสมาชิก'
            ]);
        }

        // ตรวจสอบว่าสินค้าอยู่ใน wishlist หรือไม่
        $exists = $member->wishlists()->where('product_id', $productId)->exists();

        if ($exists) {
            // ลบออกจาก wishlist
            $member->wishlists()->where('product_id', $productId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'ลบออกจากรายการโปรดแล้ว',
                'action' => 'removed'
            ]);
        } else {
            // เพิ่มลงใน wishlist
            $member->wishlists()->create([
                'product_id' => $productId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มลงในรายการโปรดแล้ว',
                'action' => 'added'
            ]);
        }
    }
}

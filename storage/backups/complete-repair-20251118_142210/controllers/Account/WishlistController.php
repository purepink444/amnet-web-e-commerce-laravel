<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * แสดงรายการสินค้าที่ชอบทั้งหมด
     */
    public function index()
    {
        $user = auth()->user();
        
        // ถ้ามี relationship wishlist ในโมเดล User
        // $wishlistItems = $user->wishlist()->with('product')->get();
        
        // ถ้ายังไม่มี ให้ส่งค่าว่าง
        $wishlistItems = collect();
        
        return view('account.wishlist', compact('wishlistItems'));
    }

    /**
     * เพิ่ม/ลบสินค้าออกจากรายการโปรด
     */
    public function toggle($productId)
    {
        $user = auth()->user();
        
        // ตรวจสอบว่ามีสินค้าอยู่ในรายการโปรดหรือไม่
        // $exists = $user->wishlist()->where('product_id', $productId)->exists();
        
        // สมมติว่ามี relationship wishlist
        // if ($exists) {
        //     $user->wishlist()->detach($productId);
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'ลบออกจากรายการโปรดแล้ว',
        //         'action' => 'removed'
        //     ]);
        // } else {
        //     $user->wishlist()->attach($productId);
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'เพิ่มลงในรายการโปรดแล้ว',
        //         'action' => 'added'
        //     ]);
        // }
        
        // ชั่วคราวจนกว่าจะสร้าง relationship
        return response()->json([
            'success' => true,
            'message' => 'ฟีเจอร์นี้ยังไม่พร้อมใช้งาน'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Review, Product, Member};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * แสดง reviews ของสินค้า
     */
    public function index(Request $request, int $productId): View
    {
        $product = Product::findOrFail($productId);

        $reviews = Review::with(['member.user'])
            ->forProduct($productId)
            ->latest()
            ->paginate(10);

        // สถิติ rating
        $ratingStats = $this->getRatingStats($productId);

        return view('products.reviews.index', compact('product', 'reviews', 'ratingStats'));
    }

    /**
     * แสดงฟอร์มเพิ่ม review
     */
    public function create(int $productId): View|RedirectResponse
    {
        $product = Product::findOrFail($productId);

        // ตรวจสอบว่าผู้ใช้ล็อกอินแล้ว
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'กรุณาเข้าสู่ระบบก่อนเขียนรีวิว');
        }

        // ตรวจสอบว่ามี member record หรือไม่
        $member = Auth::user()->member;
        if (!$member) {
            return redirect()->back()
                ->with('error', 'ไม่พบข้อมูลสมาชิก กรุณาติดต่อผู้ดูแลระบบ');
        }

        // ตรวจสอบว่าสามารถรีวิวได้หรือไม่
        if (!$member->canReviewProduct($productId)) {
            return redirect()->back()
                ->with('error', 'คุณต้องซื้อสินค้านี้และได้รับการจัดส่งแล้วจึงจะสามารถเขียนรีวิวได้');
        }

        // ตรวจสอบว่าเคยรีวิวแล้วหรือไม่
        if ($member->hasReviewedProduct($productId)) {
            return redirect()->back()
                ->with('error', 'คุณได้เขียนรีวิวสำหรับสินค้านี้แล้ว');
        }

        return view('products.reviews.create', compact('product'));
    }

    /**
     * บันทึก review ใหม่
     */
    public function store(Request $request, int $productId): RedirectResponse
    {
        $product = Product::findOrFail($productId);

        // ตรวจสอบการ authentication
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'กรุณาเข้าสู่ระบบก่อนเขียนรีวิว');
        }

        $member = Auth::user()->member;
        if (!$member) {
            return redirect()->back()
                ->with('error', 'ไม่พบข้อมูลสมาชิก');
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'review_images' => 'nullable|array|max:5',
            'review_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ตรวจสอบสิทธิ์ในการรีวิว
        if (!$member->canReviewProduct($productId)) {
            return redirect()->back()
                ->with('error', 'คุณไม่มีสิทธิ์เขียนรีวิวสำหรับสินค้านี้');
        }

        if ($member->hasReviewedProduct($productId)) {
            return redirect()->back()
                ->with('error', 'คุณได้เขียนรีวิวสำหรับสินค้านี้แล้ว');
        }

        // จัดการรูปภาพ
        $reviewImages = [];
        if ($request->hasFile('review_images')) {
            foreach ($request->file('review_images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('reviews', $filename, 'public');
                $reviewImages[] = $filename;
            }
        }

        // สร้าง review
        Review::create([
            'product_id' => $productId,
            'member_id' => $member->member_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'review_images' => $reviewImages,
        ]);

        return redirect()->route('products.show', $productId)
            ->with('success', 'ขอบคุณสำหรับการรีวิว! รีวิวของคุณได้ถูกบันทึกแล้ว');
    }

    /**
     * แสดง review เดียว
     */
    public function show(int $productId, int $reviewId): View
    {
        $product = Product::findOrFail($productId);
        $review = Review::with(['member.user', 'product'])
            ->where('review_id', $reviewId)
            ->where('product_id', $productId)
            ->firstOrFail();

        return view('products.reviews.show', compact('product', 'review'));
    }

    /**
     * แสดงฟอร์มแก้ไข review
     */
    public function edit(int $productId, int $reviewId): View|RedirectResponse
    {
        $product = Product::findOrFail($productId);
        $review = Review::where('review_id', $reviewId)
            ->where('product_id', $productId)
            ->firstOrFail();

        // ตรวจสอบสิทธิ์
        if (!Auth::check() || Auth::user()->member_id !== $review->member_id) {
            return redirect()->back()
                ->with('error', 'คุณไม่มีสิทธิ์แก้ไขรีวิวนี้');
        }

        return view('products.reviews.edit', compact('product', 'review'));
    }

    /**
     * อัปเดต review
     */
    public function update(Request $request, int $productId, int $reviewId): RedirectResponse
    {
        $review = Review::where('review_id', $reviewId)
            ->where('product_id', $productId)
            ->firstOrFail();

        // ตรวจสอบสิทธิ์
        if (!Auth::check() || Auth::user()->member_id !== $review->member_id) {
            return redirect()->back()
                ->with('error', 'คุณไม่มีสิทธิ์แก้ไขรีวิวนี้');
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'review_images' => 'nullable|array|max:5',
            'review_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // จัดการรูปภาพใหม่
        $reviewImages = $review->review_images ?? [];
        if ($request->hasFile('review_images')) {
            foreach ($request->file('review_images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('reviews', $filename, 'public');
                $reviewImages[] = $filename;
            }
        }

        // อัปเดต review
        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'review_images' => $reviewImages,
        ]);

        return redirect()->route('products.reviews.show', [$productId, $reviewId])
            ->with('success', 'รีวิวได้ถูกอัปเดตแล้ว');
    }

    /**
     * ลบ review
     */
    public function destroy(int $productId, int $reviewId): RedirectResponse
    {
        $review = Review::where('review_id', $reviewId)
            ->where('product_id', $productId)
            ->firstOrFail();

        // ตรวจสอบสิทธิ์
        if (!Auth::check() || Auth::user()->member_id !== $review->member_id) {
            return redirect()->back()
                ->with('error', 'คุณไม่มีสิทธิ์ลบรีวิวนี้');
        }

        // ลบรูปภาพ
        if ($review->hasImages()) {
            foreach ($review->getImages() as $image) {
                $path = storage_path('app/public/reviews/' . $image);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        $review->delete();

        return redirect()->route('products.show', $productId)
            ->with('success', 'รีวิวได้ถูกลบแล้ว');
    }

    /**
     * API: ดึง reviews ของสินค้า (สำหรับ AJAX)
     */
    public function apiGetReviews(int $productId): JsonResponse
    {
        $reviews = Review::with(['member.user'])
            ->forProduct($productId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'reviews' => $reviews
        ]);
    }

    /**
     * คำนวณสถิติ rating
     */
    private function getRatingStats(int $productId): array
    {
        $reviews = Review::forProduct($productId)->get();

        $stats = [
            'total_reviews' => $reviews->count(),
            'average_rating' => $reviews->avg('rating') ?? 0,
            'rating_distribution' => [
                5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0
            ]
        ];

        foreach ($reviews as $review) {
            $stats['rating_distribution'][$review->rating]++;
        }

        return $stats;
    }
}

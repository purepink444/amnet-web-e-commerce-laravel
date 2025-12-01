<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Category, Brand, ProductImage};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\{DB, Log, Storage};

class AdminProductController extends Controller
{
    /**
     * Display paginated products list with proper ordering
     */
    public function index(Request $request)
    {
        $sortBy = $request->get('sort', 'product_id'); // Default sort by ID
        $sortDirection = $request->get('direction', 'asc'); // Default ascending for ID

        // Validate sort parameters
        $allowedSorts = ['product_id', 'product_name', 'price', 'stock_quantity', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'product_id';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $products = Product::with(['category', 'brand'])
            ->orderBy($sortBy, $sortDirection)
            ->paginate(15); // Add pagination for better performance

        return view('admin.products.index', compact('products', 'sortBy', 'sortDirection'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        return view('admin.products.create', [
            'categories' => Category::select('category_id', 'category_name')->get(),
            'brands' => Brand::select('brand_id', 'brand_name')->get()
        ]);
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        // Handle photo uploads
        $uploadedImages = [];
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            $isFirst = true;

            foreach ($files as $file) {
                $path = $file->store('products', 'public');
                $imageData = [
                    'image_path' => $path,
                    'image_filename' => $file->getClientOriginalName(),
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'is_primary' => $isFirst, // First image is primary
                    'display_order' => count($uploadedImages),
                    'uploaded_by' => auth()->id(),
                ];

                // Get image dimensions
                $imageInfo = getimagesize($file->getRealPath());
                if ($imageInfo) {
                    $imageData['width'] = $imageInfo[0];
                    $imageData['height'] = $imageInfo[1];
                }

                $uploadedImages[] = $imageData;
                $isFirst = false;
            }
        }

        try {
            DB::beginTransaction();

            $product = Product::create($validated);

            // Create product images
            if (!empty($uploadedImages)) {
                foreach ($uploadedImages as $imageData) {
                    $product->images()->create($imageData);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'เพิ่มสินค้าสำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    }

    /**
     * Get product data for modal (AJAX)
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::with(['category:category_id,category_name', 'brand:brand_id,brand_name'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบสินค้า'
            ], 404);
        }
    }

    /**
     * Show edit product form
     */
    public function edit(int $id)
    {
        $product = Product::with('images')->findOrFail($id);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::select('category_id', 'category_name')->get(),
            'brands' => Brand::select('brand_id', 'brand_name')->get()
        ]);
    }

    /**
     * Update product (supports both Form and AJAX)
     */
    public function update(Request $request, int $id)
    {
        $validated = $this->validateProduct($request);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);

            // Handle photo uploads
            $uploadedImages = [];
            if ($request->hasFile('photos')) {
                $files = $request->file('photos');
                $currentOrder = $product->images()->max('display_order') ?? 0;

                foreach ($files as $file) {
                    $path = $file->store('products', 'public');
                    $imageData = [
                        'image_path' => $path,
                        'image_filename' => $file->getClientOriginalName(),
                        'original_filename' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'is_primary' => false, // New images are not primary by default
                        'display_order' => ++$currentOrder,
                        'uploaded_by' => auth()->id(),
                    ];

                    // Get image dimensions
                    $imageInfo = getimagesize($file->getRealPath());
                    if ($imageInfo) {
                        $imageData['width'] = $imageInfo[0];
                        $imageData['height'] = $imageInfo[1];
                    }

                    $uploadedImages[] = $imageData;
                }
            }

            $product->update($validated);

            // Create new product images
            if (!empty($uploadedImages)) {
                foreach ($uploadedImages as $imageData) {
                    $product->images()->create($imageData);
                }
            }

            DB::commit();

            // AJAX Response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'อัพเดทสินค้าสำเร็จ',
                    'product' => $product->load(['category', 'brand'])
                ]);
            }

            // Form Response
            return redirect()
                ->route('admin.products.index')
                ->with('success', 'อัพเดทสินค้าสำเร็จ');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'ไม่พบสินค้า'], 404)
                : back()->with('error', 'ไม่พบสินค้า');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage());

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด'], 500)
                : back()->withInput()->with('error', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    }

    /**
     * Delete product
     */
    public function destroy(int $id)
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);

            // Check if product has been ordered (exists in order_items)
            if ($product->orderItems()->exists()) {
                DB::rollBack();
                return back()->with('error', 'ไม่สามารถลบสินค้าได้ เนื่องจากมีประวัติการสั่งซื้อ');
            }

            // Delete related data
            $product->images()->delete();
            $product->cartItems()->delete();
            $product->reviews()->delete();
            $product->wishlists()->delete();

            // Delete photo file if exists
            if ($product->photo_path && Storage::disk('public')->exists($product->photo_path)) {
                Storage::disk('public')->delete($product->photo_path);
            }

            $product->delete();

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'ลบสินค้าสำเร็จ');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'ไม่พบสินค้า');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product deletion failed: ' . $e->getMessage());

            return back()->with('error', 'เกิดข้อผิดพลาดในการลบสินค้า');
        }
    }

    /**
     * Validate product data
     */
    private function validateProduct(Request $request): array
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:999999'],
            'category_id' => ['required', 'exists:categories,category_id'],
            'brand_id' => ['nullable', 'exists:brands,brand_id'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'status' => ['nullable', 'in:active,inactive'],
        ], [
            'product_name.required' => 'กรุณาระบุชื่อสินค้า',
            'price.required' => 'กรุณาระบุราคา',
            'price.min' => 'ราคาต้องมากกว่าหรือเท่ากับ 0',
            'stock_quantity.required' => 'กรุณาระบุจำนวนสินค้า',
            'category_id.required' => 'กรุณาเลือกหมวดหมู่',
            'category_id.exists' => 'หมวดหมู่ไม่ถูกต้อง',
            'brand_id.exists' => 'แบรนด์ไม่ถูกต้อง',
            'photo.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'photo.mimes' => 'รูปภาพต้องเป็นไฟล์ jpeg, png, jpg หรือ gif',
            'photo.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 2MB',
        ]);

        $validated['status'] ??= 'active';

        return $validated;
    }

    /**
     * Delete a product image
     */
    public function deleteImage(int $imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);

            // Delete file from storage
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            // If this was the primary image, make another image primary
            if ($image->is_primary) {
                $nextImage = $image->product->images()->where('image_id', '!=', $imageId)->first();
                if ($nextImage) {
                    $nextImage->update(['is_primary' => true]);
                }
            }

            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'ลบรูปภาพสำเร็จ'
            ]);

        } catch (\Exception $e) {
            Log::error('Image deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบรูปภาพ'
            ], 500);
        }
    }
}

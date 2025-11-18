<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Product, Category, Brand};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\{DB, Log};

class AdminProductController extends Controller
{
    /**
     * Display paginated products list
     */
    public function index()
    {
        $products = Product::with(['category', 'brand'])
            ->latest()
            ->paginate(15);

        return view('admin.product.adm_product', compact('products'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        return view('admin.product.create', [
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

        try {
            DB::beginTransaction();
            
            Product::create($validated);
            
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
        $product = Product::findOrFail($id);
        
        return view('admin.product.edit', [
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
            $product->update($validated);
            
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
            $product->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.products.index')
                ->with('success', 'ลบสินค้าสำเร็จ');
                
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'ไม่พบสินค้า');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product deletion failed: ' . $e->getMessage());
            
            return back()->with('error', 'ไม่สามารถลบสินค้าได้ อาจมีข้อมูลที่เกี่ยวข้อง');
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
            'image_url' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'in:active,inactive'],
        ], [
            'product_name.required' => 'กรุณาระบุชื่อสินค้า',
            'price.required' => 'กรุณาระบุราคา',
            'price.min' => 'ราคาต้องมากกว่าหรือเท่ากับ 0',
            'stock_quantity.required' => 'กรุณาระบุจำนวนสินค้า',
            'category_id.required' => 'กรุณาเลือกหมวดหมู่',
            'category_id.exists' => 'หมวดหมู่ไม่ถูกต้อง',
            'brand_id.exists' => 'แบรนด์ไม่ถูกต้อง',
        ]);

        $validated['status'] ??= 'active';

        return $validated;
    }
}

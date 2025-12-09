<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\Product\AdminProductService;
use App\Services\Cache\AdminCacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminProductController extends Controller
{
    public function __construct(
        private AdminProductService $productService,
        private AdminCacheService $cacheService
    ) {}

    /**
     * Display paginated products list
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'brand', 'status']);
        $sortOptions = [
            'column' => $request->get('sort', 'product_id'),
            'direction' => $request->get('direction', 'desc')
        ];

        $products = $this->productService->getPaginatedProducts($filters, $sortOptions);
        $dropdowns = $this->cacheService->getProductDropdowns();

        return view('admin.products.index', array_merge(
            compact('products'),
            $dropdowns,
            ['sortBy' => $sortOptions['column'], 'sortDirection' => $sortOptions['direction']]
        ));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        $dropdowns = $this->cacheService->getProductDropdowns();

        return view('admin.products.create', $dropdowns);
    }

    /**
     * Store new product
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->productService->createProduct(
                $request->validated(),
                $request->file('photos', [])
            );

            $this->cacheService->invalidateProductRelatedCache();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'เพิ่มสินค้าสำเร็จ');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Get product data for modal (AJAX)
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductForModal($id);

            return response()->json([
                'success' => true,
                'product' => $product
            ]);

        } catch (ModelNotFoundException) {
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
        $product = $this->productService->getProductForEditing($id);
        $dropdowns = $this->cacheService->getProductDropdowns();

        return view('admin.products.edit', array_merge(
            compact('product'),
            $dropdowns
        ));
    }

    /**
     * Update product
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        try {
            $product = $this->productService->updateProduct(
                $id,
                $request->validated(),
                $request->file('photos', [])
            );

            $this->cacheService->invalidateProductRelatedCache();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'อัพเดทสินค้าสำเร็จ',
                    'product' => $product->load(['category', 'brand'])
                ]);
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'อัพเดทสินค้าสำเร็จ');

        } catch (ModelNotFoundException) {
            $response = ['success' => false, 'message' => 'ไม่พบสินค้า'];

            return $request->expectsJson()
                ? response()->json($response, 404)
                : back()->with('error', $response['message']);

        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาด'];

            return $request->expectsJson()
                ? response()->json($response, 500)
                : back()->withInput()->with('error', $response['message'] . ' กรุณาลองใหม่อีกครั้ง');
        }
    }

    /**
     * Delete product
     */
    public function destroy(int $id)
    {
        try {
            $this->productService->deleteProduct($id);
            $this->cacheService->invalidateProductRelatedCache();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'ลบสินค้าสำเร็จ');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handle bulk actions
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,product_id',
            'action' => 'required|in:activate,deactivate,delete'
        ]);

        try {
            $result = $this->productService->performBulkAction(
                $request->product_ids,
                $request->action
            );

            $this->cacheService->invalidateProductRelatedCache();

            return back()->with('success', $result['message']);

        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาดในการดำเนินการ');
        }
    }

    /**
     * Delete a product image
     */
    public function deleteImage(int $imageId): JsonResponse
    {
        try {
            $this->productService->deleteProductImage($imageId);
            $this->cacheService->invalidateProductRelatedCache();

            return response()->json([
                'success' => true,
                'message' => 'ลบรูปภาพสำเร็จ'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบรูปภาพ'
            ], 500);
        }
    }
}

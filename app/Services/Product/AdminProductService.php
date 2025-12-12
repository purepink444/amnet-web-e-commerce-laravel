<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminProductService
{
    /**
     * Get paginated products with filters and sorting
     */
    public function getPaginatedProducts(array $filters = [], array $sortOptions = []): LengthAwarePaginator
    {
        $query = Product::with([
            'category:category_id,category_name',
            'brand:brand_id,brand_name',
            'images' => fn($q) => $q->select('product_id', 'image_path', 'is_primary')
                                   ->orderBy('is_primary', 'desc')
                                   ->orderBy('display_order')
        ]);

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $sortOptions);

        return $query->paginate(15)->appends($filters);
    }

    /**
     * Create a new product with images
     */
    public function createProduct(array $data, array $photos = []): Product
    {
        return DB::transaction(function () use ($data, $photos) {
            $product = Product::create($data);
            Log::info('Product created', ['product_id' => $product->product_id]);

            if (!empty($photos)) {
                $this->createProductImages($product, $photos);
            }

            return $product;
        });
    }

    /**
     * Get product data for modal display
     */
    public function getProductForModal(int $productId): Product
    {
        return Product::with([
            'category:category_id,category_name',
            'brand:brand_id,brand_name'
        ])->findOrFail($productId);
    }

    /**
     * Get product data for editing
     */
    public function getProductForEditing(int $productId): Product
    {
        return Product::with([
            'images' => fn($q) => $q->orderBy('is_primary', 'desc')->orderBy('display_order')
        ])->findOrFail($productId);
    }

    /**
     * Update an existing product
     */
    public function updateProduct(int $productId, array $data, array $photos = []): Product
    {
        return DB::transaction(function () use ($productId, $data, $photos) {
            $product = Product::findOrFail($productId);
            $product->update($data);

            if (!empty($photos)) {
                $this->createProductImages($product, $photos, false);
            }

            return $product;
        });
    }

    /**
     * Delete a product and all related data
     */
    public function deleteProduct(int $productId): void
    {
        DB::transaction(function () use ($productId) {
            $product = Product::findOrFail($productId);

            // Check if product has been ordered
            if ($product->orderItems()->exists()) {
                throw new \Exception('ไม่สามารถลบสินค้าได้ เนื่องจากมีประวัติการสั่งซื้อ');
            }

            // Delete related data
            $product->images()->delete();
            $product->cartItems()->delete();
            $product->reviews()->delete();
            $product->wishlists()->delete();

            // Delete main photo if exists
            if ($product->photo_path && Storage::disk('public')->exists($product->photo_path)) {
                Storage::disk('public')->delete($product->photo_path);
            }

            $product->delete();
        });
    }

    /**
     * Perform bulk actions on products
     */
    public function performBulkAction(array $productIds, string $action): array
    {
        return DB::transaction(function () use ($productIds, $action) {
            return match($action) {
                'activate' => $this->activateProducts($productIds),
                'deactivate' => $this->deactivateProducts($productIds),
                'delete' => $this->deleteProductsBulk($productIds),
            };
        });
    }

    /**
     * Delete a product image
     */
    public function deleteProductImage(int $imageId): void
    {
        $image = ProductImage::findOrFail($imageId);

        // Delete file from storage
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        // If this was the primary image, make another image primary
        if ($image->is_primary) {
            $nextImage = $image->product->images()
                ->where('image_id', '!=', $imageId)
                ->first();

            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        $image->delete();
    }

    /**
     * Apply filters to product query
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'ILIKE', "%{$search}%")
                  ->orWhere('sku', 'ILIKE', "%{$search}%")
                  ->orWhereRaw('product_id::text LIKE ?', ["%{$search}%"]);
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (!empty($filters['brand'])) {
            $query->where('brand_id', $filters['brand']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
    }

    /**
     * Apply sorting to product query
     */
    private function applySorting($query, array $sortOptions): void
    {
        $column = $sortOptions['column'] ?? 'product_id';
        $direction = $sortOptions['direction'] ?? 'desc';

        $allowedSorts = ['product_id', 'product_name', 'price', 'stock_quantity', 'created_at', 'updated_at'];

        if (in_array($column, $allowedSorts)) {
            $query->orderBy($column, $direction);
        } else {
            $query->orderBy('product_id', 'desc');
        }
    }

    /**
     * Create product images from uploaded files
     */
    private function createProductImages(Product $product, array $photos, bool $isFirstBatch = true): void
    {
        $imageData = [];
        $isFirst = $isFirstBatch;

        foreach ($photos as $index => $photo) {
            $imageInfo = $this->processUploadedImage($photo, $index, $isFirst);
            $imageData[] = array_merge($imageInfo, [
                'product_id' => $product->product_id,
                'uploaded_by' => auth()->id(),
            ]);

            $isFirst = false;
        }

        if (!empty($imageData)) {
            ProductImage::insert($imageData);
            Log::info('Product images created', [
                'product_id' => $product->product_id,
                'count' => count($imageData)
            ]);
        }
    }

    /**
     * Process a single uploaded image
     */
    private function processUploadedImage($photo, int $index, bool $isPrimary): array
    {
        $this->validateImageFile($photo);

        $filename = $this->generateUniqueFilename($photo);
        $path = $this->storeImageFile($photo, $filename);

        return [
            'image_path' => $path,
            'image_filename' => $filename,
            'original_filename' => $photo->getClientOriginalName(),
            'file_size' => $photo->getSize(),
            'mime_type' => $photo->getMimeType(),
            'is_primary' => $isPrimary,
            'display_order' => $index,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Validate uploaded image file
     */
    private function validateImageFile($photo): void
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower($photo->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('ไฟล์ต้องเป็นรูปภาพเท่านั้น (jpg, jpeg, png, gif)');
        }

        if ($photo->getSize() > 2048 * 1024) { // 2MB
            throw new \Exception('ขนาดไฟล์ต้องไม่เกิน 2MB');
        }
    }

    /**
     * Generate unique filename for image
     */
    private function generateUniqueFilename($photo): string
    {
        $extension = $photo->getClientOriginalExtension();
        return time() . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Store image file and return path
     */
    private function storeImageFile($photo, string $filename): string
    {
        $directory = public_path('storage/products');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $photo->move($directory, $filename);
        return 'products/' . $filename;
    }

    /**
     * Activate multiple products
     */
    private function activateProducts(array $productIds): array
    {
        Product::whereIn('product_id', $productIds)->update(['status' => 'active']);
        return ['message' => 'เปิดใช้งานสินค้าเรียบร้อยแล้ว'];
    }

    /**
     * Deactivate multiple products
     */
    private function deactivateProducts(array $productIds): array
    {
        Product::whereIn('product_id', $productIds)->update(['status' => 'inactive']);
        return ['message' => 'ปิดใช้งานสินค้าเรียบร้อยแล้ว'];
    }

    /**
     * Delete multiple products
     */
    private function deleteProductsBulk(array $productIds): array
    {
        // Check if any products have been ordered
        $orderedProducts = Product::whereIn('product_id', $productIds)
            ->whereHas('orderItems')
            ->pluck('product_name')
            ->toArray();

        if (!empty($orderedProducts)) {
            throw new \Exception('ไม่สามารถลบสินค้าที่มีประวัติการสั่งซื้อได้: ' . implode(', ', $orderedProducts));
        }

        // Delete products and related data
        foreach ($productIds as $productId) {
            $this->deleteProduct($productId);
        }

        return ['message' => 'ลบสินค้าเรียบร้อยแล้ว'];
    }
}

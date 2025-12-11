<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use App\Services\Cache\ProductCacheService;
use App\Services\Logging\ApiLogger;
use App\Jobs\SendProductNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductCacheService $cacheService,
        private ApiLogger $logger
    ) {}

    /**
     * Get paginated products with caching and filtering.
     */
    public function getProducts(array $filters = []): LengthAwarePaginator
    {
        $startTime = microtime(true);

        try {
            $cacheKey = 'products_' . md5(serialize($filters));

            $products = $this->cacheService->rememberWithTags($cacheKey, function () use ($filters) {
                return $this->productRepository->search($filters);
            }, 3600); // Cache for 1 hour

            $this->logger->logPerformance('getProducts', (microtime(true) - $startTime) * 1000, [
                'filters' => $filters,
                'result_count' => $products->total()
            ]);

            return $products;

        } catch (\Exception $e) {
            $this->logger->logError($e, ['operation' => 'getProducts', 'filters' => $filters]);
            throw $e;
        }
    }

    /**
     * Get product by ID with optimized relations.
     */
    public function getProductById(int $id): ?Product
    {
        $startTime = microtime(true);

        try {
            $cacheKey = "product_{$id}_full";

            $product = $this->cacheService->rememberWithTags($cacheKey, function () use ($id) {
                return $this->productRepository->findByIdWithRelations($id);
            }, 1800); // Cache for 30 minutes

            if ($product) {
                // Increment view count asynchronously
                $this->incrementViewCount($id);
            }

            $this->logger->logPerformance('getProductById', (microtime(true) - $startTime) * 1000, [
                'product_id' => $id,
                'found' => $product !== null
            ]);

            return $product;

        } catch (\Exception $e) {
            $this->logger->logError($e, ['operation' => 'getProductById', 'product_id' => $id]);
            throw $e;
        }
    }

    /**
     * Create a new product.
     */
    public function createProduct(array $data): Product
    {
        $startTime = microtime(true);

        DB::beginTransaction();
        try {
            $product = $this->productRepository->create($data);

            // Clear related caches
            $this->cacheService->invalidateProductRelatedCache();

            DB::commit();

            // Dispatch notification job to queue
            SendProductNotification::dispatch($product, 'created')
                ->onQueue('high')
                ->delay(now()->addSeconds(5)); // Small delay to ensure transaction commits

            $this->logger->logBusinessEvent('product_created', [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'created_by' => auth()->id()
            ]);

            $this->logger->logPerformance('createProduct', (microtime(true) - $startTime) * 1000, [
                'product_id' => $product->product_id
            ]);

            return $product;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->logger->logError($e, ['operation' => 'createProduct', 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(int $id, array $data): bool
    {
        $startTime = microtime(true);

        DB::beginTransaction();
        try {
            $updated = $this->productRepository->update($id, $data);

            if ($updated) {
                // Clear related caches
                $this->cacheService->invalidateProduct($id);
                $this->cacheService->invalidateProductRelatedCache();

                $this->logger->logBusinessEvent('product_updated', [
                    'product_id' => $id,
                    'updated_by' => auth()->id(),
                    'changes' => $data
                ]);
            }

            DB::commit();

            $this->logger->logPerformance('updateProduct', (microtime(true) - $startTime) * 1000, [
                'product_id' => $id,
                'success' => $updated
            ]);

            return $updated;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->logger->logError($e, ['operation' => 'updateProduct', 'product_id' => $id, 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(int $id): bool
    {
        $startTime = microtime(true);

        DB::beginTransaction();
        try {
            // Check if product can be deleted
            $product = $this->productRepository->findById($id);
            if (!$product) {
                return false;
            }

            // Check for related orders
            if ($product->orderItems()->exists()) {
                throw new \Exception('Cannot delete product with existing orders');
            }

            $deleted = $this->productRepository->delete($id);

            if ($deleted) {
                // Clear related caches
                $this->cacheService->invalidateProduct($id);
                $this->cacheService->invalidateProductRelatedCache();

                $this->logger->logBusinessEvent('product_deleted', [
                    'product_id' => $id,
                    'product_name' => $product->product_name,
                    'deleted_by' => auth()->id()
                ]);
            }

            DB::commit();

            $this->logger->logPerformance('deleteProduct', (microtime(true) - $startTime) * 1000, [
                'product_id' => $id,
                'success' => $deleted
            ]);

            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->logger->logError($e, ['operation' => 'deleteProduct', 'product_id' => $id]);
            throw $e;
        }
    }

    /**
     * Get featured products.
     */
    public function getFeaturedProducts(int $limit = 10): Collection
    {
        $startTime = microtime(true);

        try {
            $cacheKey = "products_featured_{$limit}";

            $products = $this->cacheService->rememberWithTags($cacheKey, function () use ($limit) {
                return $this->productRepository->getFeatured($limit);
            }, 1800); // Cache for 30 minutes

            $this->logger->logPerformance('getFeaturedProducts', (microtime(true) - $startTime) * 1000, [
                'limit' => $limit,
                'result_count' => $products->count()
            ]);

            return $products;

        } catch (\Exception $e) {
            $this->logger->logError($e, ['operation' => 'getFeaturedProducts', 'limit' => $limit]);
            throw $e;
        }
    }

    /**
     * Get trending products.
     */
    public function getTrendingProducts(int $days = 7, int $limit = 10): Collection
    {
        $startTime = microtime(true);

        try {
            $cacheKey = "products_trending_{$days}_{$limit}";

            $products = $this->cacheService->rememberWithTags($cacheKey, function () use ($days, $limit) {
                return $this->productRepository->getTrending($days, $limit);
            }, 900); // Cache for 15 minutes

            $this->logger->logPerformance('getTrendingProducts', (microtime(true) - $startTime) * 1000, [
                'days' => $days,
                'limit' => $limit,
                'result_count' => $products->count()
            ]);

            return $products;

        } catch (\Exception $e) {
            $this->logger->logError($e, ['operation' => 'getTrendingProducts', 'days' => $days, 'limit' => $limit]);
            throw $e;
        }
    }

    /**
     * Update product stock.
     */
    public function updateStock(int $id, int $quantity): bool
    {
        try {
            $updated = $this->productRepository->updateStock($id, $quantity);

            if ($updated) {
                $this->cacheService->invalidateProduct($id);

                $this->logger->logBusinessEvent('product_stock_updated', [
                    'product_id' => $id,
                    'new_quantity' => $quantity,
                    'updated_by' => auth()->id()
                ]);
            }

            return $updated;

        } catch (\Exception $e) {
            $this->logger->logError($e, ['operation' => 'updateStock', 'product_id' => $id, 'quantity' => $quantity]);
            throw $e;
        }
    }

    /**
     * Increment product view count.
     */
    private function incrementViewCount(int $id): void
    {
        try {
            $this->productRepository->incrementViews($id);
        } catch (\Exception $e) {
            // Log but don't fail the request
            $this->logger->logError($e, ['operation' => 'incrementViewCount', 'product_id' => $id]);
        }
    }

    /**
     * Get product statistics.
     */
    public function getProductStats(): array
    {
        try {
            return [
                'total_products' => $this->productRepository->countByStatus('active') +
                                   $this->productRepository->countByStatus('inactive'),
                'active_products' => $this->productRepository->countByStatus('active'),
                'inactive_products' => $this->productRepository->countByStatus('inactive'),
                'out_of_stock' => $this->productRepository->countByStatus('out_of_stock'),
            ];

        } catch (\Exception $e) {
            $this->logger->logError($e, ['operation' => 'getProductStats']);
            return [];
        }
    }
}
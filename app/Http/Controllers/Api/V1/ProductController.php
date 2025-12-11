<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\Product\ProductService;
use App\Services\Logging\ApiLogger;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends \App\Http\Controllers\Controller
{
    public function __construct(
        private ProductService $productService,
        private ApiLogger $logger
    ) {}

    /**
     * Display a listing of the resource with pagination and caching.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->logger->logRequest($request, auth()->user());

            $filters = $request->only([
                'category_id', 'brand_id', 'search', 'status', 'price_min', 'price_max',
                'sort_by', 'sort_direction', 'per_page'
            ]);

            $products = $this->productService->getProducts($filters);

            $this->logger->logResponse($request, response()->json($products), 0);

            return ApiResponse::paginated(
                ProductResource::collection($products),
                'Products retrieved successfully'
            );

        } catch (\Exception $e) {
            $this->logger->logError($e, ['operation' => 'index', 'filters' => $filters ?? []]);

            return ApiResponse::internalError('Failed to retrieve products');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $this->logger->logRequest($request, auth()->user());

            $validatedData = $request->validated();
            $product = $this->productService->createProduct($validatedData);

            $this->logger->logBusinessEvent('product_api_created', [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'created_by' => auth()->id()
            ]);

            return ApiResponse::success(
                new ProductResource($product->load(['category', 'brand', 'primaryImage'])),
                'Product created successfully',
                Response::HTTP_CREATED
            );

        } catch (ValidationException $e) {
            $this->logger->logError($e, ['operation' => 'store', 'user_id' => auth()->id()]);

            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            $this->logger->logError($e, ['operation' => 'store', 'user_id' => auth()->id()]);

            return ApiResponse::internalError('Failed to create product');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $this->logger->logRequest($request, auth()->user());

            $product = $this->productService->getProductById((int) $id);

            if (!$product) {
                return ApiResponse::notFound('Product not found');
            }

            return ApiResponse::success(
                new ProductResource($product),
                'Product retrieved successfully'
            );

        } catch (ModelNotFoundException $e) {
            $this->logger->logError($e, ['operation' => 'show', 'product_id' => $id]);

            return ApiResponse::notFound('Product not found');

        } catch (\Exception $e) {
            $this->logger->logError($e, ['operation' => 'show', 'product_id' => $id]);

            return ApiResponse::internalError('Failed to retrieve product');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id): JsonResponse
    {
        try {
            $this->logger->logRequest($request, auth()->user());

            $validatedData = $request->validated();
            $updated = $this->productService->updateProduct((int) $id, $validatedData);

            if (!$updated) {
                return ApiResponse::notFound('Product not found');
            }

            // Get updated product
            $product = $this->productService->getProductById((int) $id);

            $this->logger->logBusinessEvent('product_api_updated', [
                'product_id' => $id,
                'updated_by' => auth()->id(),
                'changes' => $validatedData
            ]);

            return ApiResponse::success(
                new ProductResource($product),
                'Product updated successfully'
            );

        } catch (ValidationException $e) {
            $this->logger->logError($e, ['operation' => 'update', 'product_id' => $id, 'user_id' => auth()->id()]);

            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            $this->logger->logError($e, ['operation' => 'update', 'product_id' => $id, 'user_id' => auth()->id()]);

            return ApiResponse::internalError('Failed to update product');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $this->logger->logRequest($request, auth()->user());

            $deleted = $this->productService->deleteProduct((int) $id);

            if (!$deleted) {
                return ApiResponse::notFound('Product not found');
            }

            $this->logger->logBusinessEvent('product_api_deleted', [
                'product_id' => $id,
                'deleted_by' => auth()->id()
            ]);

            return ApiResponse::success(null, 'Product deleted successfully');

        } catch (\Exception $e) {
            // Check if it's a business rule violation
            if (str_contains($e->getMessage(), 'existing orders')) {
                return ApiResponse::conflict('Cannot delete product with existing orders');
            }

            $this->logger->logError($e, ['operation' => 'destroy', 'product_id' => $id, 'user_id' => auth()->id()]);

            return ApiResponse::internalError('Failed to delete product');
        }
    }
}
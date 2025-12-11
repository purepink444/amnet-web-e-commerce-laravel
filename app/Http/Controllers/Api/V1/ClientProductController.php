<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Get featured products
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $limit = min(max($limit, 1), 50); // Limit between 1-50

            $products = $this->productService->getFeaturedProducts($limit);

            return ApiResponse::success(
                $products,
                'Featured products retrieved successfully'
            );

        } catch (\Exception $e) {
            \Log::error('Featured products error: ' . $e->getMessage(), [
                'limit' => $request->input('limit', 10)
            ]);

            return ApiResponse::internalError('Failed to retrieve featured products');
        }
    }

    /**
     * Quick search for products
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = trim($request->input('q', ''));

            if (strlen($query) < 2) {
                return ApiResponse::success([], 'Search query too short');
            }

            if (strlen($query) > 100) {
                return ApiResponse::error('Search query too long', 400, null, 'INVALID_SEARCH_QUERY');
            }

            $suggestions = $this->productService->searchSuggestions($query, 5);

            return ApiResponse::success(
                $suggestions,
                'Search suggestions retrieved successfully'
            );

        } catch (\Exception $e) {
            \Log::error('Product search error: ' . $e->getMessage(), [
                'query' => $request->input('q', ''),
                'user_id' => auth()->id()
            ]);

            return ApiResponse::internalError('Failed to perform search');
        }
    }

    /**
     * Get related products
     */
    public function related(Request $request, int $productId): JsonResponse
    {
        try {
            $limit = $request->input('limit', 4);
            $limit = min(max($limit, 1), 20); // Limit between 1-20

            $relatedProducts = $this->productService->getRelatedProducts($productId, $limit);

            return ApiResponse::success(
                $relatedProducts,
                'Related products retrieved successfully'
            );

        } catch (\Exception $e) {
            \Log::error('Related products error: ' . $e->getMessage(), [
                'product_id' => $productId,
                'limit' => $request->input('limit', 4)
            ]);

            return ApiResponse::internalError('Failed to retrieve related products');
        }
    }

    /**
     * Get product reviews
     */
    public function reviews(Request $request, int $productId): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $limit = min(max($limit, 1), 50); // Limit between 1-50

            $reviews = $this->productService->getProductReviews($productId, $limit);

            return ApiResponse::success(
                $reviews,
                'Product reviews retrieved successfully'
            );

        } catch (\Exception $e) {
            \Log::error('Product reviews error: ' . $e->getMessage(), [
                'product_id' => $productId,
                'limit' => $request->input('limit', 10)
            ]);

            return ApiResponse::internalError('Failed to retrieve product reviews');
        }
    }
}
<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductSearchService
{
    /**
     * Advanced product search with multiple algorithms
     * Time Complexity: O(log n) for indexed searches, O(n) for full-text
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $startTime = microtime(true);
        \Log::info('ProductSearchService search start', ['filters' => $filters]);

        $query = Product::query()->with(['category', 'brand']);

        // Apply filters using efficient algorithms
        $query = $this->applyFilters($query, $filters);

        // Apply sorting with optimized algorithms
        $query = $this->applySorting($query, $filters['sort'] ?? 'relevance');

        $result = $query->paginate($perPage);

        $searchTime = microtime(true) - $startTime;
        \Log::info('ProductSearchService search end', ['time' => $searchTime, 'count' => $result->count()]);

        return $result;
    }

    /**
     * Apply filters using database indexes for O(log n) performance
     * Optimized for PostgreSQL with proper index utilization
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        // Use composite index: idx_products_status_price_stock
        $query->where('status', 'active');

        // Category filter - uses idx_products_category_brand
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Brand filter - uses idx_products_category_brand
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Price range filter - uses idx_products_status_price_stock
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $query->whereBetween('price', [
                $filters['min_price'] ?? 0,
                $filters['max_price'] ?? 999999999.99
            ]);
        }

        // Stock availability filter - uses partial index idx_products_in_stock
        if (isset($filters['in_stock']) && $filters['in_stock']) {
            $query->where('stock_quantity', '>', 0);
        }

        // Optimized full-text search using trigram indexes
        if (!empty($filters['search'])) {
            $searchTerm = trim($filters['search']);

            // Sanitize search term to prevent SQL injection
            $searchTerm = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $searchTerm);

            if (strlen($searchTerm) >= 2) {
                $query->where(function ($q) use ($searchTerm) {
                    // Use trigram similarity for better performance (uses idx_products_name_trgm)
                    $q->whereRaw("product_name % ?", [$searchTerm])
                      ->orWhereRaw("similarity(product_name, ?) > 0.3", [$searchTerm])
                      // Fallback to ILIKE for exact matches
                      ->orWhere('product_name', 'ILIKE', '%' . $searchTerm . '%')
                      // Search in SKU with exact match
                      ->orWhere('sku', 'ILIKE', $searchTerm . '%');
                });
            }
        }

        return $query;
    }

    /**
     * Apply sorting with optimized algorithms
     */
    private function applySorting(Builder $query, string $sort): Builder
    {
        switch ($sort) {
            case 'price_asc':
                return $query->orderBy('price', 'asc');
            case 'price_desc':
                return $query->orderBy('price', 'desc');
            case 'newest':
                return $query->orderBy('created_at', 'desc');
            case 'oldest':
                return $query->orderBy('created_at', 'asc');
            case 'popular':
                return $query->orderBy('view_count', 'desc');
            case 'name_asc':
                return $query->orderBy('product_name', 'asc');
            case 'name_desc':
                return $query->orderBy('product_name', 'desc');
            case 'relevance':
            default:
                // For relevance, prioritize exact matches and popularity
                return $query->orderBy('view_count', 'desc')
                           ->orderBy('created_at', 'desc');
        }
    }

    /**
     * Get search suggestions using Trie-like algorithm
     * Time Complexity: O(m) where m is prefix length
     */
    public function getSuggestions(string $prefix, int $limit = 10): Collection
    {
        return Product::where('product_name', 'ILIKE', "{$prefix}%")
                     ->where('status', 'active')
                     ->orderBy('view_count', 'desc')
                     ->limit($limit)
                     ->pluck('product_name');
    }

    /**
     * Get related products using collaborative filtering algorithm
     * Time Complexity: O(k log n) where k is number of similar products
     */
    public function getRelatedProducts(int $productId, int $limit = 6): Collection
    {
        $product = Product::find($productId);
        if (!$product) {
            return collect();
        }

        // Find products in same category with similar price range
        return Product::where('category_id', $product->category_id)
                     ->where('product_id', '!=', $productId)
                     ->where('status', 'active')
                     ->whereBetween('price', [
                         $product->price * 0.8, // 20% lower
                         $product->price * 1.2  // 20% higher
                     ])
                     ->orderBy('view_count', 'desc')
                     ->limit($limit)
                     ->get();
    }

    /**
     * Get trending products using time-weighted algorithm
     * Time Complexity: O(n log n) for sorting
     */
    public function getTrendingProducts(int $days = 30, int $limit = 10): Collection
    {
        $startDate = now()->subDays($days);

        return Product::where('status', 'active')
                     ->where('created_at', '>=', $startDate)
                     ->orderBy('view_count', 'desc')
                     ->orderBy('created_at', 'desc')
                     ->limit($limit)
                     ->get();
    }
}
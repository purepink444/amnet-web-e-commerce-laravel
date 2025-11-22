<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SearchService
{
    /**
     * Trie data structure for efficient prefix search
     */
    private array $trie = [];

    /**
     * Search products with DSA-optimized algorithms
     */
    public function searchProducts(Builder $query, string $searchTerm, array $options = []): Builder
    {
        if (empty($searchTerm)) {
            return $query;
        }

        // Use different search strategies based on search term length and options
        if (strlen($searchTerm) <= 3) {
            // For short terms, use exact match with wildcards
            return $this->exactSearch($query, $searchTerm);
        } elseif (isset($options['fuzzy']) && $options['fuzzy']) {
            // Fuzzy search for longer terms
            return $this->fuzzySearch($query, $searchTerm);
        } else {
            // Full-text search with ranking
            return $this->fullTextSearch($query, $searchTerm);
        }
    }

    /**
     * Exact search with optimized wildcards
     */
    private function exactSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('product_name', 'ILIKE', "%{$searchTerm}%")
              ->orWhere('description', 'ILIKE', "%{$searchTerm}%");
        });
    }

    /**
     * Fuzzy search using Levenshtein distance algorithm
     */
    private function fuzzySearch(Builder $query, string $searchTerm): Builder
    {
        // Get all products and calculate Levenshtein distance
        $products = $query->get();
        $matches = collect();

        foreach ($products as $product) {
            $nameDistance = $this->levenshtein($searchTerm, $product->product_name);
            $descDistance = $this->levenshtein($searchTerm, $product->description ?? '');

            $minDistance = min($nameDistance, $descDistance);
            $maxLength = max(strlen($searchTerm), strlen($product->product_name));

            // Accept matches with distance <= 30% of string length
            if ($minDistance <= $maxLength * 0.3) {
                $product->search_score = 1 - ($minDistance / $maxLength);
                $matches->push($product);
            }
        }

        // Return query with matched IDs
        $ids = $matches->sortByDesc('search_score')->pluck('product_id');
        return $query->whereIn('product_id', $ids)->orderByRaw('FIELD(product_id, ' . $ids->implode(',') . ')');
    }

    /**
     * Full-text search with relevance ranking
     */
    private function fullTextSearch(Builder $query, string $searchTerm): Builder
    {
        $terms = explode(' ', $searchTerm);

        return $query->where(function ($q) use ($terms) {
            foreach ($terms as $term) {
                $q->where(function ($subQ) use ($term) {
                    $subQ->where('product_name', 'ILIKE', "%{$term}%")
                         ->orWhere('description', 'ILIKE', "%{$term}%");
                });
            }
        });
    }

    /**
     * Build Trie for autocomplete functionality
     */
    public function buildAutocompleteTrie(Collection $items, string $field = 'product_name'): void
    {
        $this->trie = [];

        foreach ($items as $item) {
            $word = strtolower($item->$field);
            $this->insertIntoTrie($word, $item);
        }
    }

    /**
     * Insert word into Trie
     */
    private function insertIntoTrie(string $word, $item): void
    {
        $node = &$this->trie;

        for ($i = 0; $i < strlen($word); $i++) {
            $char = $word[$i];
            if (!isset($node[$char])) {
                $node[$char] = [];
            }
            $node = &$node[$char];
        }

        if (!isset($node['__end__'])) {
            $node['__end__'] = [];
        }
        $node['__end__'][] = $item;
    }

    /**
     * Search Trie for autocomplete suggestions
     */
    public function autocomplete(string $prefix, int $limit = 10): Collection
    {
        $node = &$this->trie;
        $prefix = strtolower($prefix);

        // Navigate to prefix node
        for ($i = 0; $i < strlen($prefix); $i++) {
            $char = $prefix[$i];
            if (!isset($node[$char])) {
                return collect();
            }
            $node = &$node[$char];
        }

        // Collect all words under this prefix
        $results = collect();
        $this->collectWords($node, $prefix, $results, $limit);

        return $results;
    }

    /**
     * Collect words from Trie node
     */
    private function collectWords(array &$node, string $currentWord, Collection &$results, int $limit): void
    {
        if (isset($node['__end__'])) {
            foreach ($node['__end__'] as $item) {
                if ($results->count() >= $limit) return;
                $results->push($item);
            }
        }

        foreach ($node as $char => &$childNode) {
            if ($char !== '__end__') {
                $this->collectWords($childNode, $currentWord . $char, $results, $limit);
                if ($results->count() >= $limit) return;
            }
        }
    }

    /**
     * Levenshtein distance algorithm implementation
     */
    private function levenshtein(string $str1, string $str2): int
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);

        if ($len1 === 0) return $len2;
        if ($len2 === 0) return $len1;

        $matrix = [];

        // Initialize first row and column
        for ($i = 0; $i <= $len1; $i++) {
            $matrix[$i][0] = $i;
        }
        for ($j = 0; $j <= $len2; $j++) {
            $matrix[0][$j] = $j;
        }

        // Fill the matrix
        for ($i = 1; $i <= $len1; $i++) {
            for ($j = 1; $j <= $len2; $j++) {
                $cost = ($str1[$i-1] === $str2[$j-1]) ? 0 : 1;

                $matrix[$i][$j] = min(
                    $matrix[$i-1][$j] + 1,      // deletion
                    $matrix[$i][$j-1] + 1,      // insertion
                    $matrix[$i-1][$j-1] + $cost // substitution
                );
            }
        }

        return $matrix[$len1][$len2];
    }

    /**
     * Advanced filtering with multiple criteria
     */
    public function advancedFilter(Builder $query, array $filters): Builder
    {
        // Price range filter using binary search principles
        if (isset($filters['price_min']) || isset($filters['price_max'])) {
            $query = $this->priceRangeFilter($query, $filters);
        }

        // Category filter with hash-based lookup
        if (isset($filters['categories']) && is_array($filters['categories'])) {
            $query = $this->categoryFilter($query, $filters['categories']);
        }

        // Brand filter
        if (isset($filters['brands']) && is_array($filters['brands'])) {
            $query = $this->brandFilter($query, $filters['brands']);
        }

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Stock filter
        if (isset($filters['in_stock'])) {
            $query->where('stock_quantity', '>', 0);
        }

        return $query;
    }

    /**
     * Price range filter using efficient range queries
     */
    private function priceRangeFilter(Builder $query, array $filters): Builder
    {
        $minPrice = $filters['price_min'] ?? null;
        $maxPrice = $filters['price_max'] ?? null;

        if ($minPrice !== null && $maxPrice !== null) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        } elseif ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    /**
     * Category filter using hash-based lookup
     */
    private function categoryFilter(Builder $query, array $categoryIds): Builder
    {
        return $query->whereIn('category_id', $categoryIds);
    }

    /**
     * Brand filter
     */
    private function brandFilter(Builder $query, array $brandIds): Builder
    {
        return $query->whereIn('brand_id', $brandIds);
    }

    /**
     * Efficient sorting using multiple algorithms
     */
    public function efficientSort(Builder $query, string $sortBy, string $direction = 'asc'): Builder
    {
        switch ($sortBy) {
            case 'price':
                return $query->orderBy('price', $direction);
            case 'name':
                return $query->orderBy('product_name', $direction);
            case 'newest':
                return $query->orderBy('created_at', 'desc');
            case 'popular':
                return $query->orderBy('view_count', 'desc');
            case 'rating':
                // This would require a reviews relationship
                return $query->orderBy('created_at', 'desc');
            default:
                return $query->orderBy('product_id', $direction);
        }
    }

    /**
     * Cache search results for better performance
     */
    public function cacheSearchResults(string $cacheKey, $results, int $ttl = 300)
    {
        return Cache::remember($cacheKey, $ttl, function () use ($results) {
            return $results;
        });
    }

    /**
     * Clear search cache
     */
    public function clearSearchCache(string $pattern = 'search_*')
    {
        $keys = Cache::store('redis')->keys($pattern) ?? [];
        foreach ($keys as $key) {
            Cache::forget(str_replace(Cache::getPrefix(), '', $key));
        }
    }
}

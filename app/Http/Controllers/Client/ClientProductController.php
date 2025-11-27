<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\{Product, Category, Brand};
use App\Services\{SearchService, ProductSearchService, CacheService};
use Illuminate\Support\Facades\{Cache, Log};

class ClientProductController extends Controller
{
    protected SearchService $searchService;
    protected ProductSearchService $productSearchService;
    protected CacheService $cacheService;

    public function __construct(
        SearchService $searchService,
        ProductSearchService $productSearchService,
        CacheService $cacheService
    ) {
        $this->searchService = $searchService;
        $this->productSearchService = $productSearchService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display all active products with optimized search algorithms
     * Time Complexity: O(log n) for indexed operations, O(n) for search
     */
    public function index(Request $request): View
    {
        // Build filters array for ProductSearchService
        $filters = [
            'search' => $request->input('search'),
            'category_id' => $request->input('category'),
            'brand_id' => $request->input('brand'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'in_stock' => $request->boolean('in_stock'),
            'sort' => $request->input('sort', 'relevance')
        ];

        // Use optimized ProductSearchService
        $products = $this->productSearchService->search($filters, 12);

        // Get cached filter options using CacheService
        $categories = $this->cacheService->getCategories();
        $brands = $this->cacheService->getBrands();

        return view('pages.product', compact('products', 'categories', 'brands'));
    }

    /**
     * Display single product with optimized related products algorithm
     * Time Complexity: O(log n) for product lookup, O(k) for related products
     */
    public function show(int $id): View
    {
        try {
            // Get product with optimized eager loading
            $product = Product::with([
                'category:category_id,category_name',
                'brand:brand_id,brand_name'
            ])
            ->where('status', 'active')
            ->findOrFail($id);

            // Increment view count using optimized caching
            $this->cacheService->incrementProductView($id);

            // Get related products using collaborative filtering algorithm
            $relatedProducts = $this->productSearchService->getRelatedProducts($id, 4);

            // Get recently viewed products from session
            $recentlyViewed = $this->getRecentlyViewedProducts($id);

            // Check if product is in user's wishlist (optimized query)
            $isInWishlist = false;
            if (auth()->check() && auth()->user()->member) {
                $isInWishlist = auth()->user()->member->wishlists()
                    ->where('product_id', $product->product_id)
                    ->exists();
            }

            return view('pages.product_detail', compact(
                'product',
                'relatedProducts',
                'recentlyViewed',
                'isInWishlist'
            ));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Product not found: {$id}");
            abort(404, 'ไม่พบสินค้าที่คุณต้องการ');
        }
    }

    /**
     * Increment product view count
     */
    private function incrementViewCount(Product $product): void
    {
        // Use increment to avoid race condition
        $product->increment('view_count');
        
        // Store in session to track recently viewed
        $viewed = session()->get('recently_viewed', []);
        
        // Add current product ID (keep only last 10)
        if (!in_array($product->product_id, $viewed)) {
            array_unshift($viewed, $product->product_id);
            $viewed = array_slice($viewed, 0, 10);
            session()->put('recently_viewed', $viewed);
        }
    }

    /**
     * Get recently viewed products from session
     */
    private function getRecentlyViewedProducts(int $currentProductId): \Illuminate\Support\Collection
    {
        $viewedIds = session()->get('recently_viewed', []);

        // Remove current product from list
        $viewedIds = array_diff($viewedIds, [$currentProductId]);

        if (empty($viewedIds)) {
            return collect([]);
        }

        return Product::whereIn('product_id', $viewedIds)
            ->where('status', 'active')
            ->limit(4)
            ->get()
            ->sortBy(function ($product) use ($viewedIds) {
                return array_search($product->product_id, $viewedIds);
            });
    }

    /**
     * Quick search API endpoint with optimized prefix matching
     * Time Complexity: O(m) where m is prefix length
     */
    public function quickSearch(Request $request)
    {
        $query = trim($request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Use optimized ProductSearchService for suggestions
        $suggestions = $this->productSearchService->getSuggestions($query, 5);

        return response()->json($suggestions->map(function ($productName) {
            // Get full product data for the suggestion
            $product = Product::where('product_name', $productName)
                             ->where('status', 'active')
                             ->select('product_id', 'product_name', 'price', 'image_url')
                             ->first();

            return $product ? [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'price' => $product->price,
                'image_url' => $product->image_url,
            ] : null;
        })->filter()->values());
    }
}

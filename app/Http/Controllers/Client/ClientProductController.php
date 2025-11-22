<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\{Product, Category, Brand};
use App\Services\SearchService;
use Illuminate\Support\Facades\{Cache, Log};

class ClientProductController extends Controller
{
    protected SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Display all active products with DSA-optimized filters and pagination
     */
    public function index(Request $request): View
    {
        $query = Product::query()
            ->with(['category:category_id,category_name', 'brand:brand_id,brand_name'])
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0);

        // DSA-optimized search
        if ($search = $request->input('search')) {
            $query = $this->searchService->searchProducts($query, $search, [
                'fuzzy' => $request->boolean('fuzzy', false)
            ]);
        }

        // Advanced filtering with DSA principles
        $filters = [];
        if ($categoryId = $request->input('category')) {
            $filters['categories'] = [$categoryId];
        }
        if ($brandId = $request->input('brand')) {
            $filters['brands'] = [$brandId];
        }
        if ($minPrice = $request->input('min_price')) {
            $filters['price_min'] = $minPrice;
        }
        if ($maxPrice = $request->input('max_price')) {
            $filters['price_max'] = $maxPrice;
        }
        if ($request->boolean('in_stock')) {
            $filters['in_stock'] = true;
        }

        $query = $this->searchService->advancedFilter($query, $filters);

        // Efficient sorting using DSA algorithms
        $sortBy = $request->input('sort', 'newest');
        $query = $this->searchService->efficientSort($query, $sortBy);

        // Cache results for better performance
        $cacheKey = 'products_' . md5(serialize($request->all()));
        $products = $this->searchService->cacheSearchResults($cacheKey, $query->paginate(12)->withQueryString(), 300);

        $products = $query->paginate(12)->withQueryString();

        // Get filter options (cached for 1 hour)
        $categories = Cache::remember('active_categories', 3600, fn() => 
            Category::whereHas('products', fn($q) => $q->where('status', 'active'))
                ->select('category_id', 'category_name')
                ->get()
        );

        $brands = Cache::remember('active_brands', 3600, fn() => 
            Brand::whereHas('products', fn($q) => $q->where('status', 'active'))
                ->select('brand_id', 'brand_name')
                ->get()
        );

        return view('pages.product', compact('products', 'categories', 'brands'));
    }

    /**
     * Display single product with related products
     */
    public function show(int $id): View
    {
        try {
            // Get product with relationships
            $product = Product::with([
                'category:category_id,category_name',
                'brand:brand_id,brand_name'
            ])
            ->where('status', 'active')
            ->findOrFail($id);

            // Increment view count
            // $this->incrementViewCount($product); // Temporarily disabled due to caching issue

            // Get related products (same category, exclude current)
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('product_id', '!=', $id)
                ->where('status', 'active')
                ->where('stock_quantity', '>', 0)
                ->inRandomOrder()
                ->limit(4)
                ->get();

            // Get recently viewed products from session
            $recentlyViewed = $this->getRecentlyViewedProducts($id);

            return view('pages.product_detail', compact(
                'product',
                'relatedProducts',
                'recentlyViewed'
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
     * Quick search API endpoint with Trie-based autocomplete (AJAX)
     */
    public function quickSearch(Request $request)
    {
        $query = $request->input('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Build Trie for autocomplete if not cached
        $cacheKey = 'autocomplete_trie';
        $trieBuilt = Cache::get($cacheKey . '_built', false);

        if (!$trieBuilt) {
            $products = Cache::remember('autocomplete_products', 3600, function () {
                return Product::where('status', 'active')
                    ->select('product_id', 'product_name', 'price', 'image_url')
                    ->get();
            });

            $this->searchService->buildAutocompleteTrie($products, 'product_name');
            Cache::put($cacheKey . '_built', true, 3600);
        }

        // Use Trie for fast autocomplete
        $suggestions = $this->searchService->autocomplete($query, 5);

        return response()->json($suggestions->map(function ($product) {
            return [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'price' => $product->price,
                'image_url' => $product->image_url,
            ];
        }));
    }
}

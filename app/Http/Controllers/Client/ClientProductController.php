<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\{Product, Category, Brand};
use Illuminate\Support\Facades\{Cache, Log};

class ClientProductController extends Controller
{
    /**
     * Display all active products with filters and pagination
     */
    public function index(Request $request): View
    {
        $query = Product::query()
            ->with(['category:category_id,category_name', 'brand:brand_id,brand_name'])
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by Category
        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by Brand
        if ($brandId = $request->input('brand')) {
            $query->where('brand_id', $brandId);
        }

        // Price Range Filter
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Sorting
        $sortBy = $request->input('sort', 'latest');
        match ($sortBy) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('product_name', 'asc'),
            'name_desc' => $query->orderBy('product_name', 'desc'),
            'popular' => $query->orderBy('view_count', 'desc'),
            default => $query->latest(),
        };

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
            $this->incrementViewCount($product);

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
    private function getRecentlyViewedProducts(int $currentProductId): \Illuminate\Database\Eloquent\Collection
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
     * Quick search API endpoint (AJAX)
     */
    public function quickSearch(Request $request)
    {
        $query = $request->input('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('status', 'active')
            ->where('product_name', 'ILIKE', "%{$query}%")
            ->select('product_id', 'product_name', 'price', 'image_url')
            ->limit(5)
            ->get();

        return response()->json($products);
    }
}

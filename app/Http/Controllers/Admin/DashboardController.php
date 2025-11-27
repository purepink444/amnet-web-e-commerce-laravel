<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Services\{CacheService, DataStructureService};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected CacheService $cacheService;
    protected DataStructureService $dataStructureService;

    public function __construct(CacheService $cacheService, DataStructureService $dataStructureService)
    {
        $this->cacheService = $cacheService;
        $this->dataStructureService = $dataStructureService;
    }

    /**
     * Display dashboard with optimized analytics
     * Time Complexity: O(1) amortized due to caching
     */
    public function index()
    {
        // Get cached dashboard statistics
        $stats = $this->cacheService->getDashboardStats();


        // Calculate monthly data using efficient aggregation
        $monthlyData = $this->getMonthlyAnalytics();

        // Extract arrays for view compatibility
        $monthlyOrders = $monthlyData['orders'];
        $monthlySales = $monthlyData['sales'];

        // Get recent orders with eager loading
        $recentOrders = Order::with([
            'user' => function($query) {
                $query->select('user_id', 'firstname', 'lastname');
            },
            'orderItems' => function($query) {
                $query->with(['product' => function($productQuery) {
                    $productQuery->select('product_id', 'product_name');
                }]);
            }
        ])
        ->latest()
        ->limit(10)
        ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyOrders',
            'monthlySales'
        ));
    }

    /**
     * Get monthly analytics data with optimized queries
     */
    private function getMonthlyAnalytics(): array
    {
        $currentYear = date('Y');

        // Use raw SQL for better performance on large datasets
        $monthlyStats = \DB::select("
            SELECT
                EXTRACT(MONTH FROM created_at) as month,
                COUNT(*) as orders_count,
                COALESCE(SUM(total_amount), 0) as sales_total
            FROM orders
            WHERE EXTRACT(YEAR FROM created_at) = ?
            GROUP BY EXTRACT(MONTH FROM created_at)
            ORDER BY month
        ", [$currentYear]);

        // Initialize arrays with zeros
        $monthlyOrders = array_fill(1, 12, 0);
        $monthlySales = array_fill(1, 12, 0);

        // Fill data from query results
        foreach ($monthlyStats as $stat) {
            $monthlyOrders[$stat->month] = (int) $stat->orders_count;
            $monthlySales[$stat->month] = (float) $stat->sales_total;
        }

        return [
            'orders' => array_values($monthlyOrders),
            'sales' => array_values($monthlySales)
        ];
    }

    public function refreshCache()
    {
        // Clear system caches
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');

        // Warm up application caches
        $this->cacheService->warmCache();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Cache ถูก refresh และ warm up แล้ว');
    }
}

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
            'member' => function($query) {
                $query->select('member_id', 'first_name', 'last_name');
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

        // Get recent user registrations
        $recentUsers = User::with('member')
            ->latest()
            ->limit(5)
            ->get();

        // Get low stock alerts
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
            ->where('status', 'active')
            ->orderBy('stock_quantity', 'asc')
            ->limit(5)
            ->get();

        // Get pending orders
        $pendingOrders = Order::where('order_status', 'pending')
            ->with(['member' => function($query) {
                $query->select('member_id', 'first_name', 'last_name');
            }])
            ->latest()
            ->limit(5)
            ->get();

        // Generate activity feed (mock data for demonstration)
        $activityFeed = $this->generateActivityFeed();

        // System health indicators
        $systemHealth = $this->getSystemHealth();

        // Quick stats for today
        $todayStats = $this->getTodayStats();

        // Top selling products
        $topProducts = $this->getTopSellingProducts();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyOrders',
            'monthlySales',
            'recentOrders',
            'recentUsers',
            'lowStockProducts',
            'pendingOrders',
            'activityFeed',
            'systemHealth',
            'todayStats',
            'topProducts'
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

    /**
     * Generate activity feed data
     */
    private function generateActivityFeed(): array
    {
        // In a real application, this would come from an activity log table
        // For now, we'll generate mock data based on recent database activity
        $activities = [];

        // Recent orders
        $recentOrders = Order::with('member')->latest()->limit(3)->get();
        foreach ($recentOrders as $order) {
            $activities[] = [
                'type' => 'order',
                'icon' => 'bi bi-cart-check',
                'color' => 'success',
                'title' => 'คำสั่งซื้อใหม่',
                'description' => 'คำสั่งซื้อ #' . $order->order_id . ' โดย ' . ($order->member ? $order->member->first_name . ' ' . $order->member->last_name : 'ผู้ใช้'),
                'amount' => '฿' . number_format($order->total_amount, 2),
                'time' => $order->created_at->diffForHumans(),
                'timestamp' => $order->created_at
            ];
        }

        // Recent user registrations
        $recentUsers = User::with('member')->latest()->limit(2)->get();
        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user',
                'icon' => 'bi bi-person-plus',
                'color' => 'info',
                'title' => 'ผู้ใช้ใหม่',
                'description' => 'สมัครสมาชิก: ' . ($user->member ? $user->member->first_name . ' ' . $user->member->last_name : $user->username),
                'time' => $user->created_at->diffForHumans(),
                'timestamp' => $user->created_at
            ];
        }

        // Sort by timestamp and limit to 10 items
        return collect($activities)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values()
            ->all();
    }

    /**
     * Get system health indicators
     */
    private function getSystemHealth(): array
    {
        return [
            'server_load' => rand(20, 80), // Mock data
            'memory_usage' => rand(30, 70),
            'disk_usage' => rand(40, 90),
            'db_connections' => rand(5, 25),
            'response_time' => rand(100, 500),
            'uptime' => rand(95, 99)
        ];
    }

    /**
     * Get today's statistics
     */
    private function getTodayStats(): array
    {
        $today = today();

        return [
            'orders_today' => Order::whereDate('created_at', $today)->count(),
            'sales_today' => Order::whereDate('created_at', $today)->sum('total_amount'),
            'users_today' => User::whereDate('created_at', $today)->count(),
            'products_sold_today' => \DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
                ->whereDate('orders.created_at', $today)
                ->sum('quantity')
        ];
    }

    /**
     * Get top selling products
     */
    private function getTopSellingProducts(): array
    {
        return \DB::select("
            SELECT
                p.product_name,
                SUM(oi.quantity) as total_sold,
                SUM(oi.subtotal) as total_revenue
            FROM products p
            JOIN order_items oi ON p.product_id = oi.product_id
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.order_status != 'cancelled'
            GROUP BY p.product_id, p.product_name
            ORDER BY total_sold DESC
            LIMIT 5
        ");
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

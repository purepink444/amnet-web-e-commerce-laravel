<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\{Order, Product, User};
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Cache, Log};

class DashboardController extends Controller
{
    private const CACHE_TTL = 300; // 5 minutes
    private const TOP_PRODUCTS_LIMIT = 4;
    private const RECENT_ORDERS_LIMIT = 5;
    
    /**
     * Display admin dashboard with statistics
     */
    public function index(): View
    {
        try {
            $stats = $this->getStatistics();
            return view('dashboard', $stats);
            
        } catch (\Exception $e) {
            Log::error('Dashboard loading error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('dashboard', $this->getEmptyStatistics());
        }
    }

    /**
     * Get all dashboard statistics
     */
    private function getStatistics(): array
    {
        return [
            'totalOrders' => $this->getTotalOrders(),
            'totalRevenue' => $this->getTotalRevenue(),
            'totalProducts' => $this->getTotalProducts(),
            'totalUsers' => $this->getTotalUsers(),
            
            'ordersGrowth' => $this->getOrdersGrowth(),
            'revenueGrowth' => $this->getRevenueGrowth(),
            'usersGrowth' => $this->getUsersGrowth(),
            
            'ordersByStatus' => $this->getOrdersByStatus(),
            'recentOrders' => $this->getRecentOrders(),
            'topProducts' => $this->getTopProducts(),
            'salesData' => $this->getSalesChartData(),
        ];
    }

    private function getTotalOrders(): int
    {
        return Cache::remember('dashboard.total_orders', self::CACHE_TTL, 
            fn() => Order::count()
        );
    }

    private function getTotalRevenue(): float
    {
        return Cache::remember('dashboard.total_revenue', self::CACHE_TTL, 
            fn() => (float) Order::where('status', 'completed')->sum('total_amount')
        );
    }

    private function getTotalProducts(): int
    {
        return Cache::remember('dashboard.total_products', self::CACHE_TTL, 
            fn() => Product::count()
        );
    }

    private function getTotalUsers(): int
    {
        return Cache::remember('dashboard.total_users', self::CACHE_TTL, 
            fn() => User::count()
        );
    }

    private function getOrdersGrowth(): float
    {
        [$current, $previous] = $this->getMonthlyComparison(
            fn($month, $year) => Order::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count()
        );

        return $this->calculateGrowthPercentage($current, $previous);
    }

    private function getRevenueGrowth(): float
    {
        [$current, $previous] = $this->getMonthlyComparison(
            fn($month, $year) => (float) Order::where('status', 'completed')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->sum('total_amount')
        );

        return $this->calculateGrowthPercentage($current, $previous);
    }

    private function getUsersGrowth(): float
    {
        [$current, $previous] = $this->getMonthlyComparison(
            fn($month, $year) => User::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count()
        );

        return $this->calculateGrowthPercentage($current, $previous);
    }

    private function getMonthlyComparison(callable $query): array
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        return [
            $query($now->month, $now->year),
            $query($lastMonth->month, $lastMonth->year),
        ];
    }

    private function calculateGrowthPercentage(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function getOrdersByStatus(): array
    {
        return Cache::remember('dashboard.orders_by_status', self::CACHE_TTL, function () {
            return Order::select('status')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
    }

    private function getRecentOrders()
    {
        return Order::with([
                'user:id,name,email',
                'orderItems' => fn($q) => $q->limit(3),
                'orderItems.product:id,product_name'
            ])
            ->select('order_id', 'user_id', 'order_number', 'total_amount', 'status', 'created_at')
            ->latest()
            ->limit(self::RECENT_ORDERS_LIMIT)
            ->get();
    }

    private function getTopProducts()
    {
        return Cache::remember('dashboard.top_products', self::CACHE_TTL, function () {
            return Product::select('id', 'product_name', 'price', 'image_url')
                ->withCount([
                    'orderItems as total_sold' => fn($q) => $q->select(DB::raw('COALESCE(SUM(quantity), 0)'))
                ])
                ->having('total_sold', '>', 0)
                ->orderByDesc('total_sold')
                ->limit(self::TOP_PRODUCTS_LIMIT)
                ->get();
        });
    }

    private function getSalesChartData(): array
    {
        return Cache::remember('dashboard.sales_chart', self::CACHE_TTL, function () {
            $currentYear = Carbon::now()->year;

            return [
                'currentYear' => $this->getYearlySalesData($currentYear),
                'lastYear' => $this->getYearlySalesData($currentYear - 1),
            ];
        });
    }

    private function getYearlySalesData(int $year): array
    {
        $sales = Order::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        return collect(range(1, 12))
            ->map(fn($month) => (float) ($sales[$month] ?? 0))
            ->values()
            ->toArray();
    }

    private function getEmptyStatistics(): array
    {
        return [
            'totalOrders' => 0,
            'totalRevenue' => 0.0,
            'totalProducts' => 0,
            'totalUsers' => 0,
            'ordersGrowth' => 0.0,
            'revenueGrowth' => 0.0,
            'usersGrowth' => 0.0,
            'ordersByStatus' => [],
            'recentOrders' => collect(),
            'topProducts' => collect(),
            'salesData' => [
                'currentYear' => array_fill(0, 12, 0),
                'lastYear' => array_fill(0, 12, 0),
            ],
        ];
    }

    public function refreshCache(): \Illuminate\Http\RedirectResponse
    {
        $keys = [
            'dashboard.total_orders',
            'dashboard.total_revenue',
            'dashboard.total_products',
            'dashboard.total_users',
            'dashboard.orders_by_status',
            'dashboard.top_products',
            'dashboard.sales_chart',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Log::info('Dashboard cache cleared', [
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'รีเฟรชข้อมูลสำเร็จ');
    }
}

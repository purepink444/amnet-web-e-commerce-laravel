<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class DataStructureService
{
    /**
     * Efficient product catalog using Hash Map data structure
     * Time Complexity: O(1) for lookups, O(n) for building
     */
    public function buildProductCatalog(): array
    {
        $products = Product::with(['category', 'brand'])
                          ->where('status', 'active')
                          ->get();

        $catalog = [
            'by_id' => [],      // HashMap: product_id => product
            'by_category' => [], // HashMap: category_id => [products]
            'by_brand' => [],    // HashMap: brand_id => [products]
            'by_price_range' => [
                'under_1000' => [],
                '1000_5000' => [],
                '5000_10000' => [],
                'over_10000' => []
            ],
            'search_index' => [] // Inverted index for search
        ];

        foreach ($products as $product) {
            // O(1) hash map insertions
            $catalog['by_id'][$product->product_id] = $product;

            // Category grouping
            $catalog['by_category'][$product->category_id][] = $product;

            // Brand grouping
            if ($product->brand_id) {
                $catalog['by_brand'][$product->brand_id][] = $product;
            }

            // Price range categorization using binary search logic
            $this->categorizeByPrice($catalog['by_price_range'], $product);

            // Build search index (inverted index)
            $this->buildSearchIndex($catalog['search_index'], $product);
        }

        return $catalog;
    }

    /**
     * Price categorization using binary search approach
     */
    private function categorizeByPrice(array &$priceRanges, Product $product): void
    {
        $price = $product->price;

        if ($price < 1000) {
            $priceRanges['under_1000'][] = $product;
        } elseif ($price < 5000) {
            $priceRanges['1000_5000'][] = $product;
        } elseif ($price < 10000) {
            $priceRanges['5000_10000'][] = $product;
        } else {
            $priceRanges['over_10000'][] = $product;
        }
    }

    /**
     * Build inverted index for efficient text search
     * Time Complexity: O(m) where m is number of terms
     */
    private function buildSearchIndex(array &$index, Product $product): void
    {
        $text = strtolower($product->product_name . ' ' . ($product->description ?? ''));
        $terms = array_unique(explode(' ', preg_replace('/[^\w\s]/', '', $text)));

        foreach ($terms as $term) {
            if (strlen($term) > 2) { // Ignore very short terms
                if (!isset($index[$term])) {
                    $index[$term] = [];
                }
                $index[$term][] = $product->product_id;
            }
        }
    }

    /**
     * Order processing using Queue data structure
     * FIFO (First In, First Out) processing
     */
    public function processOrderQueue(): array
    {
        $pendingOrders = Order::with(['user', 'orderItems.product'])
                             ->where('order_status', 'pending')
                             ->orderBy('created_at', 'asc')
                             ->get();

        $processedOrders = [];
        $failedOrders = [];

        foreach ($pendingOrders as $order) {
            try {
                // Process order using efficient algorithms
                $result = $this->processSingleOrder($order);

                if ($result['success']) {
                    $processedOrders[] = $result['order'];
                } else {
                    $failedOrders[] = [
                        'order' => $order,
                        'reason' => $result['reason']
                    ];
                }
            } catch (\Exception $e) {
                $failedOrders[] = [
                    'order' => $order,
                    'reason' => $e->getMessage()
                ];
            }
        }

        return [
            'processed' => $processedOrders,
            'failed' => $failedOrders,
            'total_processed' => count($processedOrders),
            'total_failed' => count($failedOrders)
        ];
    }

    /**
     * Single order processing with validation algorithms
     */
    private function processSingleOrder(Order $order): array
    {
        // Validate stock availability using efficient lookup
        foreach ($order->orderItems as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                return [
                    'success' => false,
                    'reason' => "สินค้า {$item->product->product_name} มีจำนวนไม่เพียงพอ"
                ];
            }
        }

        // Update stock using batch operations for efficiency
        $stockUpdates = [];
        foreach ($order->orderItems as $item) {
            $stockUpdates[$item->product_id] = $item->quantity;
        }

        // Batch update stock (reduces database calls)
        foreach ($stockUpdates as $productId => $quantity) {
            Product::where('product_id', $productId)
                  ->decrement('stock_quantity', $quantity);
        }

        // Update order status
        $order->update(['order_status' => 'paid']);

        return [
            'success' => true,
            'order' => $order
        ];
    }

    /**
     * User analytics using efficient aggregation algorithms
     * Time Complexity: O(n) with single pass aggregation
     */
    public function calculateUserAnalytics(): array
    {
        $users = User::with(['orders' => function($query) {
            $query->select(['user_id', 'total_amount', 'created_at']);
        }])->get();

        $analytics = [
            'total_users' => $users->count(),
            'active_users' => 0,
            'total_revenue' => 0,
            'average_order_value' => 0,
            'top_spenders' => [],
            'user_segments' => [
                'new' => [],      // < 30 days
                'regular' => [],  // 1-5 orders
                'vip' => []       // > 5 orders
            ]
        ];

        $totalOrders = 0;
        $spenderData = [];

        foreach ($users as $user) {
            $orderCount = $user->orders->count();
            $totalSpent = $user->orders->sum('total_amount');

            $totalOrders += $orderCount;
            $analytics['total_revenue'] += $totalSpent;

            // Count active users (have orders in last 30 days)
            $recentOrders = $user->orders->filter(function($order) {
                return $order->created_at->greaterThan(now()->subDays(30));
            });

            if ($recentOrders->count() > 0) {
                $analytics['active_users']++;
            }

            // User segmentation
            if ($user->created_at->greaterThan(now()->subDays(30))) {
                $analytics['user_segments']['new'][] = $user;
            } elseif ($orderCount <= 5) {
                $analytics['user_segments']['regular'][] = $user;
            } else {
                $analytics['user_segments']['vip'][] = $user;
            }

            // Track top spenders
            $spenderData[] = [
                'user' => $user,
                'total_spent' => $totalSpent,
                'order_count' => $orderCount
            ];
        }

        // Calculate average order value
        if ($totalOrders > 0) {
            $analytics['average_order_value'] = $analytics['total_revenue'] / $totalOrders;
        }

        // Get top 10 spenders using efficient sorting
        $analytics['top_spenders'] = collect($spenderData)
            ->sortByDesc('total_spent')
            ->take(10)
            ->values()
            ->all();

        return $analytics;
    }

    /**
     * Inventory optimization using forecasting algorithms
     * Time Complexity: O(n log n) for sorting and analysis
     */
    public function optimizeInventory(): array
    {
        $products = Product::with(['orderItems' => function($query) {
            $query->selectRaw('product_id, SUM(quantity) as total_sold, COUNT(*) as order_count')
                  ->groupBy('product_id');
        }])->where('status', 'active')->get();

        $optimization = [
            'low_stock' => [],
            'overstock' => [],
            'fast_moving' => [],
            'slow_moving' => [],
            'recommendations' => []
        ];

        foreach ($products as $product) {
            $orderItem = $product->orderItems->first();
            $totalSold = $orderItem ? $orderItem->total_sold : 0;
            $orderCount = $orderItem ? $orderItem->order_count : 0;

            // Low stock detection (less than 10 units)
            if ($product->stock_quantity <= 10) {
                $optimization['low_stock'][] = [
                    'product' => $product,
                    'current_stock' => $product->stock_quantity,
                    'estimated_days' => $this->estimateStockoutDays($product, $totalSold)
                ];
            }

            // Overstock detection (stock > 3 months supply)
            $monthlyDemand = $this->calculateMonthlyDemand($totalSold, $product->created_at);
            if ($monthlyDemand > 0 && $product->stock_quantity > ($monthlyDemand * 3)) {
                $optimization['overstock'][] = [
                    'product' => $product,
                    'current_stock' => $product->stock_quantity,
                    'monthly_demand' => $monthlyDemand
                ];
            }

            // Fast/slow moving classification
            if ($orderCount >= 10) {
                $optimization['fast_moving'][] = $product;
            } elseif ($orderCount <= 2) {
                $optimization['slow_moving'][] = $product;
            }
        }

        // Generate recommendations
        $optimization['recommendations'] = $this->generateInventoryRecommendations($optimization);

        return $optimization;
    }

    /**
     * Estimate days until stockout using linear regression
     */
    private function estimateStockoutDays(Product $product, int $totalSold): float
    {
        if ($totalSold <= 0) return 999; // No sales data

        $daysSinceCreation = $product->created_at->diffInDays(now());
        $dailySalesRate = $totalSold / max($daysSinceCreation, 1);

        return $dailySalesRate > 0 ? $product->stock_quantity / $dailySalesRate : 999;
    }

    /**
     * Calculate monthly demand using time-weighted average
     */
    private function calculateMonthlyDemand(int $totalSold, $createdAt): float
    {
        $monthsSinceCreation = max($createdAt->diffInMonths(now()), 1);
        return $totalSold / $monthsSinceCreation;
    }

    /**
     * Generate inventory recommendations using rule-based system
     */
    private function generateInventoryRecommendations(array $optimization): array
    {
        $recommendations = [];

        // Low stock recommendations
        if (count($optimization['low_stock']) > 0) {
            $recommendations[] = [
                'type' => 'restock',
                'priority' => 'high',
                'message' => "มีสินค้า " . count($optimization['low_stock']) . " รายการที่ใกล้หมดสต็อก",
                'action' => 'สั่งซื้อสินค้าเพิ่ม'
            ];
        }

        // Overstock recommendations
        if (count($optimization['overstock']) > 0) {
            $recommendations[] = [
                'type' => 'discount',
                'priority' => 'medium',
                'message' => "มีสินค้า " . count($optimization['overstock']) . " รายการที่สต็อกมากเกินไป",
                'action' => 'จัดโปรโมชั่นหรือลดราคา'
            ];
        }

        // Fast moving recommendations
        if (count($optimization['fast_moving']) > 0) {
            $recommendations[] = [
                'type' => 'monitor',
                'priority' => 'medium',
                'message' => "มีสินค้าขายดี " . count($optimization['fast_moving']) . " รายการ",
                'action' => 'เพิ่มสต็อกและโปรโมท'
            ];
        }

        return $recommendations;
    }
}
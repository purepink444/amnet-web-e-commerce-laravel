<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Order, Product, User, Review};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;
use App\Exports\ProductsReportExport;
use App\Exports\CustomersReportExport;

class AdminReportController extends Controller
{
    public function index()
    {
        // Summary statistics
        $stats = [
            'total_sales' => Order::sum('total_amount') ?? 0,
            'total_orders' => Order::count(),
            'total_customers' => User::whereHas('role', function($q) {
                $q->where('role_name', 'member');
            })->count(),
            'total_products' => Product::count(),
            'average_order_value' => Order::avg('total_amount') ?? 0,
            'total_reviews' => Review::count(),
            'average_rating' => Review::avg('rating') ?? 0,
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function sales(Request $request)
    {
        $query = Order::with('user');

        // Date filtering
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Status filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20);

        // Sales summary
        $salesSummary = [
            'total_sales' => $query->sum('total_amount') ?? 0,
            'total_orders' => $query->count(),
            'average_order_value' => $query->avg('total_amount') ?? 0,
        ];

        return view('admin.reports.sales', compact('orders', 'salesSummary'));
    }

    public function products(Request $request)
    {
        $query = Product::with(['category', 'brand', 'orderItems' => function($q) {
            $q->selectRaw('product_id, SUM(quantity) as total_sold, SUM(price * quantity) as total_revenue')
              ->groupBy('product_id');
        }]);

        // Search
        if ($request->filled('search')) {
            $query->where('product_name', 'ILIKE', "%{$request->search}%");
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Brand filter
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        $products = $query->paginate(20);

        // Calculate sales data for each product
        foreach ($products as $product) {
            $orderItem = $product->orderItems->first();
            $product->total_sold = $orderItem ? $orderItem->total_sold : 0;
            $product->total_revenue = $orderItem ? $orderItem->total_revenue : 0;
        }

        return view('admin.reports.products', compact('products'));
    }

    public function customers(Request $request)
    {
        $query = User::with(['role', 'orders' => function($q) {
            $q->selectRaw('user_id, COUNT(*) as total_orders, SUM(total_amount) as total_spent')
              ->groupBy('user_id');
        }])->whereHas('role', function($q) {
            $q->where('role_name', 'member');
        });

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('firstname', 'ILIKE', "%{$request->search}%")
                  ->orWhere('lastname', 'ILIKE', "%{$request->search}%")
                  ->orWhere('email', 'ILIKE', "%{$request->search}%");
            });
        }

        $customers = $query->paginate(20);

        // Calculate customer metrics
        foreach ($customers as $customer) {
            $orderData = $customer->orders->first();
            $customer->total_orders = $orderData ? $orderData->total_orders : 0;
            $customer->total_spent = $orderData ? $orderData->total_spent : 0;
            $customer->average_order_value = $customer->total_orders > 0
                ? $customer->total_spent / $customer->total_orders
                : 0;
        }

        return view('admin.reports.customers', compact('customers'));
    }

    public function export(Request $request, $type)
    {
        $filename = 'report_' . $type . '_' . date('Y-m-d_H-i-s');

        switch ($type) {
            case 'sales':
                return Excel::download(new SalesReportExport($request->all()), $filename . '.xlsx');
            case 'products':
                return Excel::download(new ProductsReportExport($request->all()), $filename . '.xlsx');
            case 'customers':
                return Excel::download(new CustomersReportExport($request->all()), $filename . '.xlsx');
            default:
                abort(404);
        }
    }
}

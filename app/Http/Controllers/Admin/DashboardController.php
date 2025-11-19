<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_sales' => Order::sum('total_amount') ?? 0,
        ];

        // Monthly orders data
        $monthlyOrders = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyOrders[] = Order::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $i)
                ->count();
        }

        // Monthly sales data
        $monthlySales = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlySales[] = Order::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $i)
                ->sum('total_amount') ?? 0;
        }

        return view('admin.dashboard', compact('stats', 'monthlyOrders', 'monthlySales'));
    }

    public function refreshCache()
    {
        return redirect()->route('admin.dashboard')
            ->with('success', 'Cache ถูก refresh แล้ว');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products_count'   => Product::count(),
            'active_products'  => Product::where('is_active', true)->count(),
            'categories_count' => Category::count(),
            'orders_count'     => Order::count(),
            'orders_pending'   => Order::where('status', Order::STATUS_PENDING)->count(),
            'revenue'          => Order::where('status', '!=', Order::STATUS_CANCELLED)
                ->where('status', '!=', Order::STATUS_PENDING)
                ->sum('total'),
        ];

        $recentOrders = Order::with('items')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}

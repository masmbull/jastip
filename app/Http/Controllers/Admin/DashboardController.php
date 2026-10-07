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
            'orders_pending'   => Order::count(),
            'revenue'          => 0,
        ];

        $paymentStats = [
            'pending' => 0,
            'verified' => 0,
            'rejected' => 0,
        ];

        $recentOrders = collect([]);
        $pendingProofs = collect([]);

        return view('admin.dashboard', compact('stats', 'recentOrders', 'paymentStats', 'pendingProofs'));
    }
}

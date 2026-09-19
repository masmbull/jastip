<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)->count();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $revenue = Order::where('status', '!=', Order::STATUS_CANCELLED)
            ->where('status', '!=', Order::STATUS_PENDING)
            ->sum('total');

        $recentOrders = Order::with('items')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'totalProducts',
            'activeProducts',
            'revenue',
            'recentOrders'
        ));
    }
}
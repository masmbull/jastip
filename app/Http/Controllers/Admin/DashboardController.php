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
            'orders_pending'   => Order::whereIn('status', [
                Order::STATUS_PENDING,
                Order::STATUS_AWAITING_PAYMENT,
            ])->count(),
            'revenue'          => Order::whereNotNull('paid_at')
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->sum('total'),
        ];

        // Payment proof statistics
        $paymentStats = [
            'pending' => \App\Models\PaymentProof::where('status', 'pending')->count(),
            'verified' => \App\Models\PaymentProof::where('status', 'verified')->count(),
            'rejected' => \App\Models\PaymentProof::where('status', 'rejected')->count(),
        ];

        $recentOrders = Order::with('items')
            ->latest()
            ->take(5)
            ->get();

        // Recent payment proofs waiting for verification
        $pendingProofs = \App\Models\PaymentProof::where('status', 'pending')
            ->with('order')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'paymentStats', 'pendingProofs'));
    }
}

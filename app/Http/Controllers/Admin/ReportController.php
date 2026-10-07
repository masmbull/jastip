<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $from = $request->input('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $orders = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('date')
            ->get();

        return view('admin.reports.sales', compact('orders', 'from', 'to'));
    }

    public function inventory()
    {
        $products = Product::select('id', 'name', 'stock', 'price', 'sold_count')
            ->orderBy('stock', 'asc')
            ->paginate(20);

        return view('admin.reports.inventory', compact('products'));
    }

    public function customers()
    {
        $users = User::where('role', 'customer')
            ->withCount('orders')
            ->with('profile')
            ->latest()
            ->paginate(20);

        return view('admin.reports.customers', compact('users'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::withCount('items');

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('customer_whatsapp', 'like', '%' . $request->input('search') . '%');
            });
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items');
                return view('admin.orders.show', compact('order'));
    }

    public function quickView(Order $order)
    {
        $order->load('items');
        return view('admin.orders._detail', compact('order'));
    }

    public function updateStatus(OrderUpdateRequest $request, Order $order)
    {
        $order->update($request->validated());

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function destroy(Request $request, Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pesanan berhasil dihapus!');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderUpdateRequest;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function __construct(
        protected StockService $stock
    ) {}
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
        Gate::authorize('view', $order);
        $order->load('items');

        return view('admin.orders.show', [
            'order' => $order,
            'qris_image'    => \Illuminate\Support\Facades\Storage::url(setting('qris_image')),
            'qris_merchant' => setting('qris_merchant_name', setting('brand_name', 'NITIP DI END')),
        ]);
    }

    public function quickView(Order $order)
    {
        Gate::authorize('view', $order);
        $order->load('items');

        return view('admin.orders._detail', [
            'order' => $order,
            'qris_image'    => \Illuminate\Support\Facades\Storage::url(setting('qris_image')),
            'qris_merchant' => setting('qris_merchant_name', setting('brand_name', 'NITIP DI END')),
        ]);
    }

    public function updateStatus(OrderUpdateRequest $request, Order $order)
    {
        Gate::authorize('update', $order);
        $previousStatus = $order->status;
        $order->update($request->validated());

        if ($request->boolean('mark_paid') && ! $order->is_paid) {
            $order->update(['paid_at' => now()]);
        }

        // Catat waktu kirim saat transisi ke shipped (untuk tampilan tracking).
        if ($order->status === Order::STATUS_SHIPPED && $previousStatus !== Order::STATUS_SHIPPED && ! $order->shipped_at) {
            $order->update(['shipped_at' => now()]);
        }

        // Stok: kunci saat lunas/dikirim, lepas saat dibatalkan.
        if ($order->is_paid || in_array($order->status, [Order::STATUS_SHIPPED, Order::STATUS_COMPLETED], true)) {
            $this->stock->commit($order);
        } elseif ($order->status === Order::STATUS_CANCELLED && ! $order->is_paid) {
            $this->stock->release($order);
        }

        // Email pengiriman saat transisi ke shipped (opt-out dihormati).
        if ($order->status === Order::STATUS_SHIPPED && $previousStatus !== Order::STATUS_SHIPPED) {
            $this->sendShippedEmail($order);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Status pesanan berhasil diperbarui!');
    }

    /**
     * Kirim email "pesanan dikirim". Diam-gagal: log saja, jangan batalkan update.
     */
    private function sendShippedEmail(Order $order): void
    {
        if (! $order->customer_email) {
            return;
        }

        if ($order->user_id && $order->user && ! $order->user->notify_email) {
            return;
        }

        try {
            Mail::to($order->customer_email)->send(new OrderShipped($order));
        } catch (\Exception $e) {
            Log::error('Failed to send shipped email', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function destroy(Request $request, Order $order)
    {
        Gate::authorize('delete', $order);
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pesanan berhasil dihapus!');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManualOrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
                $q->where('order_number', 'like', '%'.$request->input('search').'%')
                    ->orWhere('customer_name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('customer_whatsapp', 'like', '%'.$request->input('search').'%');
            });
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Form buat invoice manual: pelanggan + item diketik langsung
     * (tidak lewat checkout). Disimpan sebagai Order biasa.
     */
    public function create()
    {
        Gate::authorize('create', Order::class);

        return view('admin.orders.create');
    }

    public function store(ManualOrderStoreRequest $request)
    {
        Gate::authorize('create', Order::class);

        $items = collect($request->validated('items'))->map(fn ($item) => [
            'product_name' => $item['product_name'],
            'unit' => ($item['unit'] ?? null) ?: 'pcs',
            'quantity' => (int) $item['quantity'],
            'product_price' => (int) $item['product_price'],
            'subtotal' => (int) $item['quantity'] * (int) $item['product_price'],
        ]);

        $subtotal = (int) $items->sum('subtotal');
        $shippingCost = (int) $request->input('shipping_cost', 0);
        $fee = (int) $request->input('fee', 0);
        $paid = $request->boolean('mark_paid');
        $status = $request->input('status') ?: ($paid ? Order::STATUS_CONFIRMED : Order::STATUS_AWAITING_PAYMENT);

        // Harga & fee hanya dari input admin (invoice manual, bukan dari produk).
        $order = DB::transaction(function () use ($request, $items, $subtotal, $shippingCost, $fee, $status, $paid) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $request->input('customer_name'),
                'customer_whatsapp' => $request->input('customer_whatsapp'),
                'customer_email' => $request->input('customer_email'),
                'customer_address' => $request->input('customer_address') ?: '-',
                'customer_notes' => $request->input('customer_notes'),
                'admin_notes' => $request->input('admin_notes'),
                'shipping_method' => $request->input('shipping_method'),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'fee' => $fee,
                'total' => $subtotal + $shippingCost + $fee,
                'status' => $status,
                'paid_at' => $paid ? now() : null,
                'payment_method' => 'manual',
            ]);

            $order->payment_hash = $order->generatePaymentHash();
            $order->save();

            foreach ($items as $item) {
                OrderItem::create($item + ['order_id' => $order->id]);
            }

            return $order;
        });

        return redirect()->route('admin.orders.invoice', ['order' => $order, 'print' => 1])
            ->with('success', 'Invoice berhasil dibuat.');
    }

    /**
     * Cari pelanggan lama (dari pesanan sebelumnya) untuk auto-isi form.
     */
    public function customerSearch(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        if ($term === '') {
            return response()->json([]);
        }

        $rows = Order::query()
            ->where(function ($q) use ($term) {
                $q->where('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_whatsapp', 'like', "%{$term}%");
            })
            ->orderByDesc('created_at')
            ->limit(30)
            ->get(['customer_name', 'customer_whatsapp', 'customer_email', 'customer_address'])
            ->unique('customer_whatsapp')
            ->take(8)
            ->values();

        return response()->json($rows);
    }

    /**
     * Cari produk untuk prefill baris item (nama, harga, satuan, id).
     */
    public function productSearch(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        if ($term === '') {
            return response()->json([]);
        }

        $rows = Product::query()
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'price', 'unit']);

        return response()->json($rows);
    }

    public function show(Order $order)
    {
        Gate::authorize('view', $order);
        $order->load('items');

        return view('admin.orders.show', [
            'order' => $order,
            'qris_image' => Storage::url(setting('qris_image')),
            'qris_merchant' => setting('qris_merchant_name', setting('brand_name', 'NITIP DI END')),
        ]);
    }

    public function quickView(Order $order)
    {
        Gate::authorize('view', $order);
        $order->load('items');

        return view('admin.orders._detail', [
            'order' => $order,
            'qris_image' => Storage::url(setting('qris_image')),
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

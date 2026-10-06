<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart
    ) {}

    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Titipan kamu masih kosong. Tambahkan produk dulu ya!');
        }

        $cartItems = $this->cart->getCart();
        $subtotal = $this->cart->subtotal();
        $fee = $this->cart->fee();
        $minOrder = (int) setting('minimum_order', 0);

        if ($minOrder > 0 && $subtotal < $minOrder) {
            return redirect()->route('cart.index')
                ->with('error', 'Minimal titip ' . format_price($minOrder) . '. Subtotal kamu masih ' . format_price($subtotal) . '.');
        }

        return view('frontend.checkout.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'fee' => $fee,
        ]);
    }

    public function store(CheckoutRequest $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Titipan kamu masih kosong.');
        }

        // Rate limiting
        $executed = RateLimiter::attempt(
            'checkout:' . $request->ip(),
            $perMinute = 5,
            fn() => true
        );

        if (!$executed) {
            return back()->withInput()
                ->with('error', 'Terlalu banyak permintaan. Silakan coba lagi nanti.');
        }

        $cartItems = $this->cart->getCart();
        $subtotal = $this->cart->subtotal();
        $minOrder = (int) setting('minimum_order', 0);

        if ($minOrder > 0 && $subtotal < $minOrder) {
            return redirect()->route('cart.index')
                ->with('error', 'Minimal titip ' . format_price($minOrder) . '. Subtotal kamu masih ' . format_price($subtotal) . '.');
        }

        $shippingCost = (int) setting('shipping_cost', 0);
        $fee = $this->cart->fee();
        $total = $subtotal + $shippingCost + $fee;
        $ownerName = setting('brand_owner', 'Nabila Adriyana');

        $order = DB::transaction(function () use ($request, $cartItems, $subtotal, $shippingCost, $fee, $total) {
            $orderNumber = Order::generateOrderNumber();

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $request->input('name'),
                'customer_whatsapp' => $request->input('whatsapp'),
                'customer_address' => $request->input('address'),
                'customer_notes' => $request->input('notes'),
                'shipping_method' => $request->input('shipping_method'),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'fee' => $fee,
                'total' => $total,
                'status' => Order::STATUS_AWAITING_PAYMENT,
                'payment_method' => 'qris',
            ]);

            // Generate payment hash for integrity checking
            $order->payment_hash = $order->generatePaymentHash();
            $order->save();

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_price' => $item['price'],
                    'unit' => $item['unit'] ?? 'pcs',
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $order;
        });

        // Generate WhatsApp URL
        $orderData = [
            'name' => $request->input('name'),
            'whatsapp' => $request->input('whatsapp'),
            'address' => $request->input('address'),
            'notes' => $request->input('notes'),
            'shipping_method' => $request->input('shipping_method'),
            'items' => $cartItems->map(fn($item) => [
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ])->toArray(),
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'fee' => $fee,
            'total' => $total,
            'owner_name' => $ownerName,
            'order_number' => $order->order_number,
            'qris_image' => \Illuminate\Support\Facades\Storage::url(setting('qris_image')),
            'qris_merchant' => setting('qris_merchant_name', setting('brand_name', 'NITIP DI END')),
        ];

        $whatsappUrl = WhatsappService::orderUrl($orderData);

        // Store order data in session for confirmation page
        session(['order_data' => $orderData]);

        // Clear the cart
        $this->cart->clear();

        return redirect()->route('payment.waiting', ['order' => $order->id]);
    }

    public function confirmation()
    {
        $orderData = session('order_data');

        if (!$orderData) {
            return redirect()->route('home');
        }

        // Fetch order for status info
        $order = Order::where('order_number', $orderData['order_number'] ?? null)->first();

        return view('frontend.checkout.confirmation', [
            'orderData' => $orderData,
            'order' => $order,
        ]);
    }
}
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
        ];

        $whatsappUrl = WhatsappService::orderUrl($orderData);

        // Clear the cart
        $this->cart->clear();

        // Store order data in session for confirmation page
        session()->flash('order_data', [
            'order_number'  => $order->order_number,
            'order_id'      => $order->id,
            'subtotal'      => $subtotal,
            'shipping_cost' => $shippingCost,
            'fee'           => $fee,
            'total'         => $total,
            'whatsapp_url'  => $whatsappUrl,
            'qris_image'    => \Illuminate\Support\Facades\Storage::url(setting('qris_image')),
            'qris_merchant' => setting('qris_merchant_name', setting('brand_name', 'NITIP DI END')),
        ]);

        return redirect()->route('checkout.confirmation');
    }

    public function confirmation()
    {
        $orderData = session('order_data');

        if (!$orderData) {
            return redirect()->route('home');
        }

        return view('frontend.checkout.confirmation', ['orderData' => $orderData]);
    }
}
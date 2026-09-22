<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cart
    ) {}

    public function index()
    {
        $cartItems = $this->cart->getCart();

        return view('frontend.cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $this->cart->subtotal(),
            'fee' => $this->cart->fee(),
            'count' => $this->cart->count(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $quantity = (int) $request->input('quantity', 1);

        if ($quantity < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah minimal adalah 1.',
            ], 422);
        }

        if (!$product->isInStock()) {
            return response()->json([
                'success' => false,
                'message' => 'Produk ini sedang tidak tersedia.',
            ], 422);
        }

        if ($quantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah melebihi stok tersedia.',
            ], 422);
        }

        $this->cart->add($product->id, $quantity);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil ditambahkan ke titipan!',
            'count' => $this->cart->count(),
            'subtotal' => format_price($this->cart->subtotal()),
            'fee' => $this->cart->fee(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $quantity = (int) $request->input('quantity');

        if ($quantity < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah minimal adalah 1.',
            ], 422);
        }

        if ($quantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah melebihi stok tersedia.',
            ], 422);
        }

        $this->cart->update($product->id, $quantity);

        return response()->json([
            'success' => true,
            'message' => 'Titipan diperbarui.',
            'count' => $this->cart->count(),
            'subtotal' => format_price($this->cart->subtotal()),
            'fee' => $this->cart->fee(),
        ]);
    }

    public function remove(Product $product)
    {
        $this->cart->remove($product->id);

        return response()->json([
            'success' => true,
            'message' => 'Produk dihapus dari titipan.',
            'count' => $this->cart->count(),
            'subtotal' => $this->cart->subtotal() > 0 ? format_price($this->cart->subtotal()) : 'Rp 0',
            'fee' => $this->cart->fee(),
        ]);
    }

    public function clear()
    {
        $this->cart->clear();

        return response()->json([
            'success' => true,
            'message' => 'Titipan berhasil dikosongkan.',
            'count' => 0,
            'subtotal' => 'Rp 0',
            'fee' => 0,
        ]);
    }
}
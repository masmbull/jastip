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
            return $this->cartResponse($request, false, 'Jumlah minimal adalah 1.', 422);
        }

        try {
            if (!$product->isInStock()) {
                return $this->cartResponse($request, false, 'Produk ini sedang tidak tersedia.', 422);
            }

            if ($quantity > $product->stock) {
                return $this->cartResponse($request, false, 'Jumlah melebihi stok tersedia.', 422);
            }

            $this->cart->add($product->id, $quantity);

            return $this->cartResponse($request, true, 'Berhasil ditambahkan ke titipan!');
        } catch (\Exception $e) {
            return $this->cartResponse($request, false, 'Gagal memproses permintaan.', 500);
        }
    }

    public function update(Request $request, Product $product)
    {
        $quantity = (int) $request->input('quantity');

        if ($quantity < 1) {
            return $this->cartResponse($request, false, 'Jumlah minimal adalah 1.', 422);
        }

        try {
            if ($quantity > $product->stock) {
                return $this->cartResponse($request, false, 'Jumlah melebihi stok tersedia.', 422);
            }

            $this->cart->update($product->id, $quantity);

            return $this->cartResponse($request, true, 'Titipan diperbarui.');
        } catch (\Exception $e) {
            return $this->cartResponse($request, false, 'Gagal memproses permintaan.', 500);
        }
    }

    public function remove(Request $request, Product $product)
    {
        $this->cart->remove($product->id);

        return $this->cartResponse($request, true, 'Produk dihapus dari titipan.');
    }

    public function clear(Request $request)
    {
        $this->cart->clear();

        return $this->cartResponse($request, true, 'Titipan berhasil dikosongkan.');
    }

    private function cartResponse(Request $request, bool $success, string $message, int $status = 200)
    {
        $payload = [
            'success' => $success,
            'message' => $message,
            'count' => $this->cart->count(),
            'subtotal' => $this->cart->subtotal() > 0 ? format_price($this->cart->subtotal()) : 'Rp 0',
            'fee' => $this->cart->fee(),
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($payload, $success ? $status : max($status, 422));
        }

        return redirect()
            ->route('cart.index')
            ->with($success ? 'success' : 'error', $message);
    }
}

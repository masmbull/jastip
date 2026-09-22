<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const CART_SESSION_KEY = 'titipan_cart';

    /**
     * Get all cart items from session.
     */
    public function getCart(): Collection
    {
        return collect(session()->get(self::CART_SESSION_KEY, []));
    }

    /**
     * Add a product to the cart.
     */
    public function add(int $productId, int $quantity = 1): void
    {
        $product = Product::findOrFail($productId);

        if (!$product->isInStock()) {
            throw new \Exception('Produk tidak tersedia');
        }

        if ($quantity > $product->stock) {
            throw new \Exception('Jumlah melebihi stok tersedia');
        }

        $cart = $this->getCart();

        $existingItem = $cart->firstWhere('product_id', $productId);

        if ($existingItem) {
            $newQuantity = $existingItem['quantity'] + $quantity;
            if ($newQuantity > $product->stock) {
                throw new \Exception('Jumlah melebihi stok tersedia');
            }
            $cart->transform(function ($item) use ($productId, $newQuantity) {
                if ($item['product_id'] === $productId) {
                    $item['quantity'] = $newQuantity;
                    $item['subtotal'] = $item['price'] * $newQuantity;
                }
                return $item;
            });
        } else {
            $cart->push([
                'product_id'   => $productId,
                'name'         => $product->name,
                'price'        => $product->price,
                'setbiaya_fee' => $product->setbiaya_fee ?? 0,
                'quantity'     => $quantity,
                'image'        => $product->image,
                'unit'         => $product->unit,
                'subtotal'     => $product->price * $quantity,
            ]);
        }

        session()->put(self::CART_SESSION_KEY, $cart->toArray());
    }

    /**
     * Update a product quantity in the cart.
     */
    public function update(int $productId, int $quantity): void
    {
        if ($quantity < 1) {
            throw new \Exception('Jumlah minimal adalah 1');
        }

        $product = Product::findOrFail($productId);

        if ($quantity > $product->stock) {
            throw new \Exception('Jumlah melebihi stok tersedia');
        }

        $cart = $this->getCart();

        $cart->transform(function ($item) use ($productId, $quantity) {
            if ($item['product_id'] === $productId) {
                $item['quantity'] = $quantity;
                $item['subtotal'] = $item['price'] * $quantity;
            }
            return $item;
        });

        session()->put(self::CART_SESSION_KEY, $cart->toArray());
    }

    /**
     * Remove a product from the cart.
     */
    public function remove(int $productId): void
    {
        $cart = $this->getCart();
        $cart = $cart->reject(fn ($item) => $item['product_id'] === $productId);
        session()->put(self::CART_SESSION_KEY, $cart->toArray());
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): void
    {
        session()->forget(self::CART_SESSION_KEY);
    }

    /**
     * Get the cart count.
     */
    public function count(): int
    {
        return $this->getCart()->sum('quantity');
    }

    /**
     * Get the cart subtotal (sum of price * quantity).
     */
    public function subtotal(): int
    {
        return (int) $this->getCart()->sum('subtotal');
    }

    /**
     * Get the total service fee across all cart items.
     */
    public function fee(): int
    {
        return (int) $this->getCart()->sum(fn ($item) => ($item['setbiaya_fee'] ?? 0) * $item['quantity']);
    }

    /**
     * Get the cart total (subtotal + fee).
     */
    public function total(): int
    {
        return $this->subtotal() + $this->fee();
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
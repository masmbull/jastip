<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * Stok pesanan: tahan (reserve) saat checkout, kunci (commit) saat lunas,
 * lepas (release) saat dibatalkan. Stok tersedia = stock - reserved.
 */
class StockService
{
    /**
     * Tahan stok untuk item pesanan. Lempar exception bila tersedia kurang.
     *
     * @param  \App\Models\OrderItem[]|\Illuminate\Support\Collection  $items
     */
    public function reserve(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = Product::lockForUpdate()->find($item->product_id);
            if (! $product) {
                continue;
            }

            if ($product->availableStock() < $item->quantity) {
                throw new \Exception("Stok {$product->name} tidak mencukupi.");
            }

            $product->increment('reserved', $item->quantity);
        }
    }

    /**
     * Kunci stok: buang dari stock & reserved (pesanan lunas). Idempoten.
     */
    public function commit(Order $order): void
    {
        if ($order->stock_committed) {
            return;
        }

        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if (! $product) {
                continue;
            }

            $product->stock = max(0, $product->stock - $item->quantity);
            $product->reserved = max(0, $product->reserved - $item->quantity);
            $product->save();
        }

        $order->update(['stock_committed' => true]);
    }

    /**
     * Lepas tahanan (pesanan dibatalkan sebelum lunas). Idempoten.
     */
    public function release(Order $order): void
    {
        if ($order->stock_committed) {
            return;
        }

        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if (! $product) {
                continue;
            }

            $product->reserved = max(0, $product->reserved - $item->quantity);
            $product->save();
        }
    }
}

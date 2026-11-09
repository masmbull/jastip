<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use RuntimeException;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('Demo order data cannot be seeded in production.');
        }

        $products = Product::all();
        if ($products->isEmpty()) {
            return;
        }

        $shipping = [
            'pickup' => 0,
            'jne' => 12000,
            'jnt' => 9000,
            'sicepat' => 10000,
            'grab' => 15000,
        ];

        $samples = [
            ['name' => 'Sample Customer 01', 'wa' => '00000000001', 'addr' => 'Alamat Contoh 1, Kota Contoh', 'notes' => 'Contoh catatan pesanan', 'status' => Order::STATUS_PENDING, 'courier' => 'pickup', 'idx' => [0, 2]],
            ['name' => 'Sample Customer 02', 'wa' => '00000000002', 'addr' => 'Alamat Contoh 2, Kota Contoh', 'notes' => '', 'status' => Order::STATUS_PENDING, 'courier' => 'jne', 'idx' => [1]],
            ['name' => 'Sample Customer 03', 'wa' => '00000000003', 'addr' => 'Alamat Contoh 3, Kota Contoh', 'notes' => 'Contoh catatan pesanan', 'status' => Order::STATUS_CONFIRMED, 'courier' => 'jnt', 'idx' => [3, 4]],
            ['name' => 'Sample Customer 04', 'wa' => '00000000004', 'addr' => 'Alamat Contoh 4, Kota Contoh', 'notes' => 'Contoh catatan pesanan', 'status' => Order::STATUS_PROCESSING, 'courier' => 'sicepat', 'idx' => [5, 6]],
            ['name' => 'Sample Customer 05', 'wa' => '00000000005', 'addr' => 'Alamat Contoh 5, Kota Contoh', 'notes' => '', 'status' => Order::STATUS_READY, 'courier' => 'pickup', 'idx' => [7]],
            ['name' => 'Sample Customer 06', 'wa' => '00000000006', 'addr' => 'Alamat Contoh 6, Kota Contoh', 'notes' => 'Contoh catatan pesanan', 'status' => Order::STATUS_SHIPPED, 'courier' => 'grab', 'idx' => [0, 1, 8]],
            ['name' => 'Sample Customer 07', 'wa' => '00000000007', 'addr' => 'Alamat Contoh 7, Kota Contoh', 'notes' => '', 'status' => Order::STATUS_COMPLETED, 'courier' => 'jne', 'idx' => [2, 4]],
            ['name' => 'Sample Customer 08', 'wa' => '00000000008', 'addr' => 'Alamat Contoh 8, Kota Contoh', 'notes' => 'Contoh catatan pesanan', 'status' => Order::STATUS_CANCELLED, 'courier' => 'pickup', 'idx' => [5]],
        ];

        $n = 1;
        foreach ($samples as $s) {
            $orderNumber = 'ND' . now()->format('Ymd') . '-' . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
            $n++;

            $order = Order::firstOrCreate(
                ['order_number' => $orderNumber],
                [
                    'customer_name' => $s['name'],
                    'customer_whatsapp' => $s['wa'],
                    'customer_address' => $s['addr'],
                    'customer_notes' => $s['notes'],
                    'shipping_method' => $s['courier'],
                    'status' => $s['status'],
                    'admin_notes' => '',
                ]
            );

            if (! $order->wasRecentlyCreated) {
                continue;
            }

            $subtotal = 0;
            foreach ($s['idx'] as $i) {
                $product = $products[$i] ?? $products->first();
                $qty = rand(1, 3);
                $line = $product->price * $qty;
                $subtotal += $line;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'unit' => $product->unit ?? 'pcs',
                    'quantity' => $qty,
                    'subtotal' => $line,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping[$s['courier']] ?? 0,
                'total' => $subtotal + ($shipping[$s['courier']] ?? 0),
            ]);
        }
    }
}

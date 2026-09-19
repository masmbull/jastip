<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
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
            ['name' => 'Budi Santoso', 'wa' => '081234567001', 'addr' => 'Jl. Mawar No. 12, Bandung', 'notes' => 'Bungkus rapi kak, makasih', 'status' => Order::STATUS_PENDING, 'courier' => 'pickup', 'idx' => [0, 2]],
            ['name' => 'Sari Dewi', 'wa' => '081234567002', 'addr' => 'Jl. Melati No. 5, Jakarta Selatan', 'notes' => '', 'status' => Order::STATUS_PENDING, 'courier' => 'jne', 'idx' => [1]],
            ['name' => 'Andi Pratama', 'wa' => '081234567003', 'addr' => 'Jl. Anggrek No. 30, Surabaya', 'notes' => 'Kirim cepat ya', 'status' => Order::STATUS_CONFIRMED, 'courier' => 'jnt', 'idx' => [3, 4]],
            ['name' => 'Rina Marlina', 'wa' => '081234567004', 'addr' => 'Jl. Flamboyan No. 8, Yogyakarta', 'notes' => 'Pakai gift wrap', 'status' => Order::STATUS_PROCESSING, 'courier' => 'sicepat', 'idx' => [5, 6]],
            ['name' => 'Dedi Kurniawan', 'wa' => '081234567005', 'addr' => 'Jl. Kenanga No. 18, Semarang', 'notes' => '', 'status' => Order::STATUS_READY, 'courier' => 'pickup', 'idx' => [7]],
            ['name' => 'Nita Anggraini', 'wa' => '081234567006', 'addr' => 'Jl. Cendana No. 22, Medan', 'notes' => 'Tolong kasih kartu ucapan', 'status' => Order::STATUS_SHIPPED, 'courier' => 'grab', 'idx' => [0, 1, 8]],
            ['name' => 'Fajar Wibowo', 'wa' => '081234567007', 'addr' => 'Jl. Dahlia No. 3, Denpasar', 'notes' => '', 'status' => Order::STATUS_COMPLETED, 'courier' => 'jne', 'idx' => [2, 4]],
            ['name' => 'Maya Sintia', 'wa' => '081234567008', 'addr' => 'Jl. Teratai No. 9, Makassar', 'notes' => 'Batal pesanan, salah pilih', 'status' => Order::STATUS_CANCELLED, 'courier' => 'pickup', 'idx' => [5]],
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

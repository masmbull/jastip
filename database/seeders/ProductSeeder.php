<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name')->toArray();

        $products = [
            [
                'name' => 'My Konos Perfume',
                'category' => 'Parfum',
                'description' => 'Parfum My Konos yang membawa aroma keindahan dan kehangatan. Cocok untuk acara formal maupun santai.',
                'price' => 149000,
                'image' => 'products/my-konos-perfume.jpg',
                'stock' => 50,
                'sku' => 'PERF-MYKONOS-001',
                'unit' => 'botol',
                'is_featured' => true,
            ],
            [
                'name' => 'My Konos Body Mist',
                'category' => 'Parfum',
                'description' => 'Body mist segar My Konos untuk penggunaan sehari-hari. Memberikan kesegaran yang halus dan tahan lama.',
                'price' => 99000,
                'image' => 'products/my-konos-body-mist.jpg',
                'stock' => 30,
                'sku' => 'PERF-MYKONOS-BM',
                'unit' => 'botol',
                'is_featured' => false,
            ],
            [
                'name' => 'Tyeso Tumbler',
                'category' => 'Tumbler',
                'description' => 'Tumbler stainless steel Tyeso dengan desain elegan dan isolasi suhu yang optimal. Bisa pakai untuk minuman panas maupun dingin.',
                'price' => 189000,
                'image' => 'products/tyeso-tumbler.jpg',
                'stock' => 25,
                'sku' => 'TUM-TYESO-001',
                'unit' => 'buah',
                'is_featured' => true,
            ],
            [
                'name' => 'Tyeso Bottle',
                'category' => 'Tumbler',
                'description' => 'Botol minum Tyeso premium dengan kapasitas large. Desain ergonomic dan tahan banting.',
                'price' => 129000,
                'image' => 'products/tyeso-bottle.jpg',
                'stock' => 40,
                'sku' => 'TUM-TYESO-BTL',
                'unit' => 'buah',
                'is_featured' => false,
            ],
            [
                'name' => 'Local Beauty Product',
                'category' => 'Beauty',
                'description' => 'Produk keindahan lokal Indonesia yang terbukti kualitasnya. Formulanya ramah kulit dan tidak mengandung bahan kimia berbahaya.',
                'price' => 59000,
                'image' => 'products/local-beauty.jpg',
                'stock' => 100,
                'sku' => 'BEAUTY-001',
                'unit' => 'pcs',
                'is_featured' => true,
            ],
            [
                'name' => 'Fashion Item',
                'category' => 'Fashion',
                'description' => 'Item fashion trendy dari lokal brand. Desain modern yang nyaman dipakai sehari-hari.',
                'price' => 139000,
                'image' => 'products/fashion-item.jpg',
                'stock' => 60,
                'sku' => 'FASHION-001',
                'unit' => 'pcs',
                'is_featured' => false,
            ],
            [
                'name' => 'Accessories',
                'category' => 'Fashion',
                'description' => 'Aksesoris fashion yang melengkapi penampilan. Desain minimalis dan elegan.',
                'price' => 49000,
                'image' => 'products/accessories.jpg',
                'stock' => 0,
                'sku' => 'FASHION-ACC-01',
                'unit' => 'pcs',
                'is_featured' => false,
            ],
            [
                'name' => 'Lifestyle Item',
                'category' => 'Lifestyle',
                'description' => 'Item lifestyle yang memudahkan kehidupan sehari-hari. Praktis dan multifungsi.',
                'price' => 79000,
                'image' => 'products/lifestyle-item.jpg',
                'stock' => 80,
                'sku' => 'LIFE-001',
                'unit' => 'pcs',
                'is_featured' => false,
            ],
            [
                'name' => 'Viral Product',
                'category' => 'Lifestyle',
                'description' => 'Produk yang lagi viral dan trending di media sosial. Banyak dibutuhkan oleh para pecinta barang baru.',
                'price' => 199000,
                'image' => 'products/viral-product.jpg',
                'stock' => 15,
                'sku' => 'VIRAL-001',
                'unit' => 'pcs',
                'is_featured' => true,
            ],
            [
                'name' => 'Other Product',
                'category' => 'Lifestyle',
                'description' => 'Produk lain yang tersedia di toko kami. Silakan tanyakan stok dan ketersediaan.',
                'price' => 29000,
                'image' => 'products/other-product.jpg',
                'stock' => 200,
                'sku' => 'OTHER-001',
                'unit' => 'pcs',
                'is_featured' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                [
                    'category_id' => $categories[$product['category']],
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']),
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'stock' => $product['stock'],
                    'unit' => $product['unit'],
                    'is_active' => true,
                    'is_featured' => $product['is_featured'],
                ]
            );
        }
    }
}
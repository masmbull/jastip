<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Parfum', 'icon' => '🧴'],
            ['name' => 'Tumbler', 'icon' => '🥤'],
            ['name' => 'Fashion', 'icon' => '👕'],
            ['name' => 'Beauty', 'icon' => '✨'],
            ['name' => 'Lifestyle', 'icon' => '🏠'],
            ['name' => 'Makanan', 'icon' => '🍰'],
            ['name' => 'Accessories', 'icon' => '⌚'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => 'Produk ' . $category['name'] . ' pilihan kami',
                    'icon' => $category['icon'],
                    'sort_order' => 0,
                    'is_active' => true,
                ]
            );
        }
    }
}
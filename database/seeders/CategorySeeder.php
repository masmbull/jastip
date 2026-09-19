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
            ['name' => 'Parfum', 'icon' => 'perfume'],
            ['name' => 'Tumbler', 'icon' => 'tumbler'],
            ['name' => 'Fashion', 'icon' => 'shirt'],
            ['name' => 'Beauty', 'icon' => 'sparkles'],
            ['name' => 'Lifestyle', 'icon' => 'home'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => 'Produk ' . $category['name'] . ' pilihan kami',
                'icon' => $category['icon'],
                'sort_order' => 0,
                'is_active' => true,
            ]);
        }
    }
}
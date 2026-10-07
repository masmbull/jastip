<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache homepage data for 1 hour (3600 seconds)
        $featuredProducts = Cache::remember('home.featured_products', 3600, function () {
            return Product::active()
                ->featured()
                ->with('category')
                ->latest()
                ->take(8)
                ->get();
        });

        $categories = Cache::remember('home.categories', 3600, function () {
            return Category::active()
                ->withCount('products')
                ->with(['products' => function ($q) {
                    $q->select('id', 'category_id', 'image', 'is_active', 'is_featured')
                        ->where('is_active', true)
                        ->orderByDesc('is_featured')
                        ->take(1);
                }])
                ->whereHas('products', function ($q) {
                    $q->where('is_active', true);
                })
                ->orderBy('sort_order')
                ->get();
        });

        $popularProducts = Cache::remember('home.popular_products', 3600, function () {
            return Product::active()
                ->with('category')
                ->orderBy('stock', 'desc')
                ->take(6)
                ->get();
        });

        $viralProducts = Cache::remember('home.viral_products', 3600, function () {
            return Product::viral()
                ->with('category')
                ->take(4)
                ->get();
        });

        return view('frontend.home', compact('featuredProducts', 'categories', 'popularProducts', 'viralProducts'));
    }
}
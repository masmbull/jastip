<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::active()
            ->featured()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::active()
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

        $popularProducts = Product::active()
            ->with('category')
            ->orderBy('stock', 'desc')
            ->take(6)
            ->get();

        $viralProducts = Product::viral()
            ->with('category')
            ->take(4)
            ->get();

        return view('frontend.home', compact('featuredProducts', 'categories', 'popularProducts', 'viralProducts'));
    }
}
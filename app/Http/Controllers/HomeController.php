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

        return view('frontend.home', compact('featuredProducts', 'categories', 'popularProducts'));
    }
}
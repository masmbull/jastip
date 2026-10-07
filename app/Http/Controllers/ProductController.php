<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Validate pagination limit (max 100 items per page to prevent DOS)
        $limit = min((int) $request->input('limit', 12), 100);
        $limit = max($limit, 1); // Minimum 1 item per page

        $query = Product::active()->with('category');

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->input('category'));
        }

        // Search by name
        if ($request->filled('search') && $request->input('search') !== '') {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // Filter by stock
        if ($request->filled('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $products = $query->orderBy('name')
            ->orderBy('created_at', 'desc')
            ->paginate($limit)
            ->withQueryString();

        $categories = Cache::remember('products.active_categories', 3600, function () {
            return Category::active()
                ->withCount('products')
                ->whereHas('products', function ($q) {
                    $q->where('is_active', true);
                })
                ->orderBy('sort_order')
                ->get();
        });

        return view('frontend.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
        }

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('category')
            ->take(4)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Halaman "Produk Viral" — daftar produk tren Indonesia 2024–2025.
     * Bisa difilter per kategori dan diurutkan sesuai peringkat viral.
     */
    public function viral(Request $request)
    {
        $query = Product::viral()->with('category');

        if ($request->filled('category')) {
            $query->byCategory($request->input('category'));
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Cache::remember('products.viral_categories', 3600, function () {
            return Category::active()
                ->withCount(['products as viral_count' => function ($q) {
                    $q->where('is_viral', true)->where('is_active', true);
                }])
                ->whereHas('products', function ($q) {
                    $q->where('is_viral', true)->where('is_active', true);
                })
                ->orderBy('sort_order')
                ->get();
        });

        $totalViral = Cache::remember('products.viral_count', 3600, function () {
            return Product::viral()->count();
        });
        
        $totalSold = Cache::remember('products.viral_sold', 3600, function () {
            return (int) Product::viral()->sum('sold_count');
        });

        return view('frontend.products.viral', compact('products', 'categories', 'totalViral', 'totalSold'));
    }
}

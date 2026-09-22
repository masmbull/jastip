<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
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
            ->paginate(12)
            ->withQueryString();

        $categories = Category::active()
            ->withCount('products')
            ->whereHas('products', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('sort_order')
            ->get();

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

        $categories = Category::active()
            ->withCount(['products as viral_count' => function ($q) {
                $q->where('is_viral', true)->where('is_active', true);
            }])
            ->has('products')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($category) => $category->viral_count > 0)
            ->values();

        $totalViral = Product::viral()->count();
        $totalSold = (int) Product::viral()->sum('sold_count');

        return view('frontend.products.viral', compact('products', 'categories', 'totalViral', 'totalSold'));
    }
}

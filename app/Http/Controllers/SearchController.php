<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search and filter products
     */
    public function index(Request $request)
    {
        $query = Product::query()->active();

        // Full-text search
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // Price range filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (int) $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (int) $request->input('price_max'));
        }

        // Stock filter
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Featured filter
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('sold_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'newest':
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = \App\Models\Category::all();

        return view('frontend.search', compact('products', 'categories'));
    }

    /**
     * Auto-complete search suggestions
     */
    public function suggestions(Request $request)
    {
        if (!$request->filled('q')) {
            return response()->json([]);
        }

        $search = $request->input('q');

        $products = Product::where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'slug', 'image', 'price')
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    /**
     * Get price range for slider
     */
    public function priceRange()
    {
        $min = Product::where('is_active', true)->min('price') ?? 0;
        $max = Product::where('is_active', true)->max('price') ?? 1000000;

        return response()->json(compact('min', 'max'));
    }
}

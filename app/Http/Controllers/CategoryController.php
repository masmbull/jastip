<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->withCount('products')
            ->whereHas('products', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('sort_order')
            ->get();

        return view('frontend.categories.index', compact('categories'));
    }

    public function show(Category $category, Request $request)
    {
        if (!$category->is_active) {
            return redirect()->route('categories.index')->with('error', 'Kategori tidak ditemukan.');
        }

        $query = $category->products()->where('is_active', true)->with('category');

        if ($request->filled('search') && $request->input('search') !== '') {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $products = $query->orderBy('name')
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.categories.show', compact('category', 'products'));
    }
}
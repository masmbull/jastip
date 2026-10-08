<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Show the authenticated user's wishlist.
     */
    public function index()
    {
        $wishlists = Auth::user()->wishlists()
            ->with('product.category')
            ->latest()
            ->paginate(12);

        return view('frontend.profile.wishlist', compact('wishlists'));
    }

    /**
     * Toggle a product in/out of the user's wishlist.
     */
    public function toggle(Request $request, Product $product)
    {
        $userId = Auth::id();

        // Delete-first: a rapid double POST resolves to one insert max.
        $removed = Wishlist::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->delete();

        if (!$removed) {
            Wishlist::firstOrCreate([
                'user_id' => $userId,
                'product_id' => $product->id,
            ]);
        }

        $added = (bool) !$removed;

        return response()->json([
            'success' => true,
            'wishlisted' => $added,
            'message' => $added ? 'Ditambahkan ke wishlist' : 'Dihapus dari wishlist',
            'count' => Auth::user()->wishlists()->count(),
        ]);
    }
}

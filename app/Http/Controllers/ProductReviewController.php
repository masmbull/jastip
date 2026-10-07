<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Login diperlukan'], 401);
        }

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->exists();

        if ($existingReview) {
            return response()->json(['message' => 'Anda sudah memberikan ulasan untuk produk ini'], 422);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:1000',
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => 'pending', // Moderation required
        ]);

        return response()->json([
            'message' => 'Ulasan Anda akan ditinjau oleh admin',
            'review' => $review,
        ], 201);
    }

    /**
     * Get product reviews
     */
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->where('status', 'approved')
            ->with('user')
            ->latest()
            ->paginate(10);

        $ratingDistribution = [
            '5' => $product->reviews()->where('status', 'approved')->where('rating', 5)->count(),
            '4' => $product->reviews()->where('status', 'approved')->where('rating', 4)->count(),
            '3' => $product->reviews()->where('status', 'approved')->where('rating', 3)->count(),
            '2' => $product->reviews()->where('status', 'approved')->where('rating', 2)->count(),
            '1' => $product->reviews()->where('status', 'approved')->where('rating', 1)->count(),
        ];

        $averageRating = $product->getAverageRating();
        $reviewCount = $product->getReviewCount();

        return view('frontend.product.reviews', compact(
            'product',
            'reviews',
            'ratingDistribution',
            'averageRating',
            'reviewCount'
        ));
    }

    /**
     * Toggle helpful vote
     */
    public function toggleHelpful(Review $review)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Login diperlukan'], 401);
        }

        // Votes only count on published reviews.
        if ($review->status !== 'approved') {
            return response()->json(['message' => 'Ulasan belum dipublikasikan'], 404);
        }

        if ($review->user_id === Auth::id()) {
            return response()->json(['message' => 'Tidak bisa menilai ulasan sendiri'], 422);
        }

        // Atomic increment: prevents lost updates from rapid double-clicks.
        $review->increment('helpful_count');

        return response()->json([
            'message' => 'Terima kasih atas penilaian Anda',
            'helpful_count' => $review->helpful_count,
        ]);
    }
}

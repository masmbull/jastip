<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display list of pending reviews
     */
    public function index()
    {
        $reviews = Review::with(['user', 'product', 'order'])
            ->latest()
            ->paginate(15);

        $stats = [
            'pending' => Review::where('status', 'pending')->count(),
            'approved' => Review::where('status', 'approved')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
            'total' => Review::count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Show review details
     */
    public function show(Review $review)
    {
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve a review
     */
    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Ulasan telah disetujui');
    }

    /**
     * Reject a review
     */
    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Ulasan telah ditolak');
    }

    /**
     * Delete a review
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->back()->with('success', 'Ulasan telah dihapus');
    }

    /**
     * Get reviews by status filter
     */
    public function filterByStatus(Request $request)
    {
        $status = $request->query('status', 'pending');

        $reviews = Review::with(['user', 'product'])
            ->where('status', $status)
            ->latest()
            ->paginate(15);

        $stats = [
            'pending' => Review::where('status', 'pending')->count(),
            'approved' => Review::where('status', 'approved')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
            'total' => Review::count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats', 'status'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('reviewable');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $reviews = $query->latest()->paginate(20)->withQueryString();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        if ($review->reviewable && method_exists($review->reviewable, 'recalculateRating')) {
            $review->reviewable->recalculateRating();
        }
        return back()->with('success', 'Review approved and published publicly.');
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        if ($review->reviewable && method_exists($review->reviewable, 'recalculateRating')) {
            $review->reviewable->recalculateRating();
        }
        return back()->with('success', 'Review marked as rejected.');
    }

    public function reply(Request $request, Review $review)
    {
        $data = $request->validate([
            'admin_reply' => 'required|string|min:2|max:1500',
            'approve_too' => 'nullable|boolean',
        ]);

        $review->update([
            'admin_reply' => $data['admin_reply'],
            'replied_at' => now(),
            'status' => $request->boolean('approve_too', true) ? 'approved' : $review->status,
        ]);

        if ($review->reviewable && method_exists($review->reviewable, 'recalculateRating')) {
            $review->reviewable->recalculateRating();
        }

        return back()->with('success', 'Admin reply saved.');
    }

    public function destroy(Review $review)
    {
        $target = $review->reviewable;
        $review->delete();
        if ($target && method_exists($target, 'recalculateRating')) {
            $target->recalculateRating();
        }
        return back()->with('success', 'Review deleted.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'project'])->latest()->paginate(10);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleApprove(Review $review)
    {
        $review->update([
            'is_approved' => !$review->is_approved,
        ]);

        $statusText = $review->is_approved ? 'disetujui dan tampil di website' : 'disembunyikan dari website';
        return back()->with('success', "Review berhasil {$statusText}.");
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review berhasil dihapus.');
    }
}


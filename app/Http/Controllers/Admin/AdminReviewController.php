<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'project'])->latest();

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('nama_proyek', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->paginate(10)->withQueryString();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleApprove(Review $review)
    {
        $review->update([
            'is_approved' => !$review->is_approved,
        ]);

        $statusText = $review->is_approved ? 'disetujui dan ditayangkan di beranda' : 'ditarik dari beranda';
        return back()->with('success', "Ulasan berhasil {$statusText}.");
    }

    public function destroy(Review $review)
    {
        if ($review->image && Storage::disk('public')->exists($review->image)) {
            Storage::disk('public')->delete($review->image);
        }

        $review->delete();
        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}

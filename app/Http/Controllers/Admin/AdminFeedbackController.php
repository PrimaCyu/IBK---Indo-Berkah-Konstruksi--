<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserFeedback;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = UserFeedback::with(['user', 'project'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('nama_proyek', 'like', "%{$search}%");
                  });
            });
        }

        $feedbacks = $query->paginate(10)->withQueryString();
        return view('admin.feedbacks.index', compact('feedbacks'));
    }

    public function updateStatus(Request $request, UserFeedback $userFeedback)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai',
        ]);

        $userFeedback->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status catatan lapangan berhasil diubah.');
    }

    public function destroy(UserFeedback $userFeedback)
    {
        $userFeedback->delete();
        return back()->with('success', 'Catatan lapangan berhasil dihapus.');
    }
}

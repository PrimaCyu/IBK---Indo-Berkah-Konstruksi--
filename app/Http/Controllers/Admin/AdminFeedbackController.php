<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserFeedback;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = UserFeedback::with(['user', 'project'])->latest()->paginate(10);
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


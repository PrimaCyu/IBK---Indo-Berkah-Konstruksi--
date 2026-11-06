<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        abort_unless(Auth::check(), 401, 'Silakan login terlebih dahulu untuk memberikan review.');

        $request->validate([
            'message'    => 'required|max:500',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        if ($request->filled('project_id')) {
            $ownsProject = \App\Models\Project::where('id', $request->project_id)
                ->where('user_id', Auth::id())
                ->exists();

            abort_unless($ownsProject, 403, 'Proyek tidak valid.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        Review::create([
            'user_id'     => Auth::id(),
            'project_id'  => $request->project_id,
            'message'     => $request->message,
            'is_approved' => false,
            'image'       => $imagePath,
        ]);

        return back()->with('success', 'Review berhasil dikirim.');
    }
}
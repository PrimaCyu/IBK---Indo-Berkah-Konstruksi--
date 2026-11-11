<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'message'    => 'required|string|max:500',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'project_id' => 'nullable|exists:projects,id',
        ];

        if (! Auth::check()) {
            $rules['name'] = 'required|string|max:100';
            $rules['email'] = 'nullable|email|max:100';
        }

        $request->validate($rules);

        if ($request->filled('project_id') && Auth::check()) {
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
            'guest_name'  => Auth::check() ? null : $request->input('name'),
            'guest_email' => Auth::check() ? null : $request->input('email'),
            'project_id'  => $request->project_id,
            'message'     => $request->message,
            'is_approved' => false,
            'image'       => $imagePath,
        ]);

        return redirect()->to(url()->previous() . '#tambah-review')->with('success', 'Terima kasih! Testimonial Anda telah berhasil dikirim.');
    }
}
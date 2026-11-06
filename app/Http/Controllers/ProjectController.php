<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Store a new project (created by user from dashboard).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'alamat_proyek' => 'nullable|string|max:500',
            'deskripsi' => 'nullable|string|max:2000',
            'kategori' => 'nullable|string|in:Pembangunan Rumah,Gedung Komersial,Renovasi Bangunan,Konsultasi Konstruksi',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->projects()->create([
            'nama_proyek' => $request->nama_proyek,
            'alamat_proyek' => $request->alamat_proyek,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
        ]);

        return back()->with('project_success', 'Proyek baru berhasil ditambahkan!');
    }

    /**
     * Show project sub-dashboard.
     */
    public function show(Project $project)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure user can only see their own projects, unless admin
        abort_if($user->role !== 'admin' && $project->user_id !== $user->id, 403, 'Anda tidak memiliki akses ke proyek ini.');

        $designFiles = $project->files()->designs()->latest()->get();
        $progressFiles = $project->files()->progress()->latest()->get();
        $feedbacks = $project->feedbacks()->latest()->get();
        $reviews = $project->reviews()->latest()->get();

        $projectOwner = $project->user;
        $suratPerjanjians = collect();
        if ($projectOwner) {
            $suratPerjanjians = \App\Models\SuratPerjanjian::where(function ($query) use ($projectOwner) {
                if (!empty($projectOwner->email)) {
                    $query->where('email', $projectOwner->email);
                }
                if (!empty($projectOwner->name)) {
                    $query->orWhere('nama', $projectOwner->name);
                }
            })
            ->latest()
            ->get();
        }

        return view('user.project-detail', compact(
            'project',
            'designFiles',
            'progressFiles',
            'feedbacks',
            'reviews',
            'suratPerjanjians'
        ));
    }
}

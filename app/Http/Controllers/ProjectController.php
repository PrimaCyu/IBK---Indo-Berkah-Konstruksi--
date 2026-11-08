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

        // Klien wajib melalui alur penawaran terlebih dahulu
        if ($user->role !== 'admin') {
            \App\Models\Penawaran::create([
                'nama'      => $user->name,
                'telepon'   => $user->phone ?: '08xxxxxxxxxx',
                'email'     => $user->email,
                'alamat'    => $request->alamat_proyek,
                'deskripsi' => "Pengajuan Proyek Baru: {$request->nama_proyek} (" . ($request->kategori ?: 'Konstruksi') . "). " . ($request->deskripsi ?: ''),
                'budget'    => 'Menunggu Penawaran RAB',
                'status'    => 'pending',
            ]);

            return back()->with('penawaran_success', true);
        }

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

        // Fetch SPK specific to this project, with fallback for unlinked legacy SPK by owner
        $projectOwner = $project->user;
        $suratPerjanjians = \App\Models\SuratPerjanjian::where('project_id', $project->id)
            ->orWhere(function ($query) use ($projectOwner) {
                $query->whereNull('project_id');
                if ($projectOwner) {
                    $query->where(function ($inner) use ($projectOwner) {
                        if (!empty($projectOwner->email)) {
                            $inner->where('email', $projectOwner->email);
                        }
                        if (!empty($projectOwner->name)) {
                            $inner->orWhere('nama', $projectOwner->name);
                        }
                    });
                } else {
                    $query->whereRaw('0 = 1');
                }
            })
            ->latest()
            ->get();

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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_proyek', 'like', "%{$search}%")
                  ->orWhere('alamat_proyek', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $projects = $query->paginate(10)->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $users = User::where('role', '!=', 'accounting')->orderBy('name')->get();
        return view('admin.projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_proyek'         => 'required|string|max:255',
            'user_id'             => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', '!=', 'accounting'))],
            'kategori'            => 'required|string',
            'alamat_proyek'       => 'nullable|string|max:500',
            'deskripsi'           => 'nullable|string',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'status'              => 'required|in:aktif,selesai,dibatalkan',
            'portfolio_approved'  => 'nullable|boolean',
            'main_image'          => 'nullable|image|max:3072',
        ]);

        $imagePath = null;
        if ($request->hasFile('main_image')) {
            $imagePath = $request->file('main_image')->store('projects', 'public');
        }

        Project::create([
            'nama_proyek'         => $request->nama_proyek,
            'user_id'             => $request->user_id,
            'kategori'            => $request->kategori,
            'alamat_proyek'       => $request->alamat_proyek,
            'deskripsi'           => $request->deskripsi,
            'progress_percentage' => $request->progress_percentage,
            'status'              => $request->status,
            'portfolio_approved'  => $request->boolean('portfolio_approved'),
            'main_image'          => $imagePath,
            'completed_at'        => $request->status === 'selesai' ? now() : null,
        ]);

        return redirect()->route('admin.projects.index')->with('success', 'Proyek baru berhasil ditambahkan.');
    }

    public function edit(Project $project)
    {
        $users = User::where('role', '!=', 'accounting')->orderBy('name')->get();
        return view('admin.projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'nama_proyek'         => 'required|string|max:255',
            'user_id'             => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', '!=', 'accounting'))],
            'alamat_proyek'       => 'nullable|string|max:500',
            'kategori'            => 'required|string',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'status'              => 'required|in:aktif,selesai,dibatalkan',
            'portfolio_approved'  => 'nullable|boolean',
            'deskripsi'           => 'nullable|string',
            'main_image'          => 'nullable|image|max:3072',
        ]);

        $imagePath = $project->main_image;
        if ($request->hasFile('main_image')) {
            if ($project->main_image && Storage::disk('public')->exists($project->main_image)) {
                Storage::disk('public')->delete($project->main_image);
            }
            $imagePath = $request->file('main_image')->store('projects', 'public');
        }

        $project->update([
            'nama_proyek'         => $request->nama_proyek,
            'user_id'             => $request->user_id,
            'alamat_proyek'       => $request->alamat_proyek,
            'kategori'            => $request->kategori,
            'progress_percentage' => $request->progress_percentage,
            'status'              => $request->status,
            'portfolio_approved'  => $request->boolean('portfolio_approved'),
            'deskripsi'           => $request->deskripsi,
            'main_image'          => $imagePath,
            'completed_at'        => $request->status === 'selesai' ? ($project->completed_at ?? now()) : null,
        ]);

        return redirect()->route('admin.projects.index')->with('success', 'Data proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        if ($project->cashflows()->withTrashed()->exists()) {
            return back()->with('error', 'Proyek memiliki riwayat buku kas dan tidak dapat dihapus. Ubah status proyek bila sudah tidak berjalan.');
        }

        if ($project->main_image && Storage::disk('public')->exists($project->main_image)) {
            Storage::disk('public')->delete($project->main_image);
        }
        $project->delete();
        return back()->with('success', 'Proyek berhasil dihapus.');
    }
}

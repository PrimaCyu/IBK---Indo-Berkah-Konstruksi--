<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

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
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $projects = $query->paginate(10)->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    public function edit(Project $project)
    {
        $users = User::where('role', 'owner')->get();
        return view('admin.projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'nama_proyek'         => 'required|string|max:255',
            'alamat_proyek'       => 'nullable|string|max:500',
            'kategori'            => 'required|string',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'status'              => 'required|in:aktif,selesai,dibatalkan',
            'portfolio_approved'  => 'nullable|boolean',
            'deskripsi'           => 'nullable|string',
        ]);

        $project->update([
            'nama_proyek'         => $request->nama_proyek,
            'alamat_proyek'       => $request->alamat_proyek,
            'kategori'            => $request->kategori,
            'progress_percentage' => $request->progress_percentage,
            'status'              => $request->status,
            'portfolio_approved'  => $request->boolean('portfolio_approved'),
            'deskripsi'           => $request->deskripsi,
            'completed_at'        => $request->status === 'selesai' ? ($project->completed_at ?? now()) : null,
        ]);

        return redirect()->route('admin.projects.index')->with('success', 'Data proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return back()->with('success', 'Proyek berhasil dihapus.');
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPortfolioController extends Controller
{
    public function index(Request $request)
    {
        $query = Portfolio::latest();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('program', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        $portfolios = $query->paginate(10)->withQueryString();
        $projects = Project::where('status', 'selesai')->orderBy('nama_proyek')->get();

        return view('admin.portfolios.index', compact('portfolios', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program'          => 'required|string|max:255',
            'kategori'         => 'required|string|max:100',
            'deskripsi'        => 'nullable|string',
            'waktu_pengerjaan' => 'nullable|date',
            'main_image'       => 'required|image|max:3072',
            'project_id'       => 'nullable|exists:projects,id',
        ]);

        $imagePath = $request->file('main_image')->store('portfolios', 'public');

        Portfolio::create([
            'program'          => $request->program,
            'kategori'         => $request->kategori,
            'deskripsi'        => $request->deskripsi,
            'waktu_pengerjaan' => $request->waktu_pengerjaan,
            'main_image'       => $imagePath,
            'project_id'       => $request->project_id,
        ]);

        return back()->with('success', 'Item portofolio baru berhasil ditambahkan.');
    }

    public function destroy(Portfolio $portfolio)
    {
        if ($portfolio->main_image && Storage::disk('public')->exists($portfolio->main_image)) {
            Storage::disk('public')->delete($portfolio->main_image);
        }
        $portfolio->delete();
        return back()->with('success', 'Portofolio berhasil dihapus.');
    }
}

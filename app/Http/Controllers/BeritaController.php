<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\KategoriBerita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $kategoriId = $request->get('kategori');
        $search = $request->get('search');

        $query = Berita::with('kategori')->where('is_published', true);

        if ($kategoriId) {
            $query->where('kategori_berita_id', $kategoriId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $beritas = $query->latest()->paginate(9)->withQueryString();

        $kategoris = KategoriBerita::withCount(['beritas' => function ($q) {
            $q->where('is_published', true);
        }])->get();

        $popularBeritas = Berita::with('kategori')
            ->where('is_published', true)
            ->orderByDesc('views')
            ->take(3)
            ->get();

        $totalViews = Berita::where('is_published', true)->sum('views');
        $totalArticles = Berita::where('is_published', true)->count();

        return view('frontend.berita.index', compact('beritas', 'kategoris', 'popularBeritas', 'totalViews', 'totalArticles'));
    }

    public function show($slug)
    {
        $berita = Berita::with('kategori')->where('slug', $slug)->where('is_published', true)->firstOrFail();

        // Naikkan jumlah pembaca / views setiap artikel dibuka
        $berita->increment('views');

        // Dapatkan berita terkait
        $relatedBeritas = Berita::with('kategori')
            ->where('kategori_berita_id', $berita->kategori_berita_id)
            ->where('id', '!=', $berita->id)
            ->where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        return view('frontend.berita.show', compact('berita', 'relatedBeritas'));
    }
}

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
        
        $beritas = Berita::where('is_published', true)
            ->when($kategoriId, function ($query, $kategoriId) {
                return $query->where('kategori_berita_id', $kategoriId);
            })
            ->latest()
            ->paginate(9);

        $kategoris = KategoriBerita::all();

        return view('frontend.berita.index', compact('beritas', 'kategoris'));
    }

    public function show($slug)
    {
        $berita = Berita::where('slug', $slug)->where('is_published', true)->firstOrFail();
        
        // Increment views
        $berita->increment('views');

        // Dapatkan berita terkait
        $relatedBeritas = Berita::where('kategori_berita_id', $berita->kategori_berita_id)
            ->where('id', '!=', $berita->id)
            ->where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        return view('frontend.berita.show', compact('berita', 'relatedBeritas'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;

class AdminBeritaController extends Controller
{
    public function index()
    {
        $beritas = Berita::with('kategori')->latest()->paginate(10);
        return view('admin.berita.index', compact('beritas'));
    }

    public function togglePublish(Berita $berita)
    {
        $berita->update([
            'is_published' => !$berita->is_published,
        ]);

        $status = $berita->is_published ? 'diterbitkan' : 'disimpan sebagai draf';
        return back()->with('success', "Artikel berhasil {$status}.");
    }

    public function destroy(Berita $berita)
    {
        $berita->delete();
        return back()->with('success', 'Berita berhasil dihapus.');
    }
}


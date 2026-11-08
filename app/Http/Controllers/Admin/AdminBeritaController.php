<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\KategoriBerita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminBeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::with('kategori')->latest();

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_berita_id', $request->kategori_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $beritas = $query->paginate(10)->withQueryString();
        $categories = KategoriBerita::all();

        return view('admin.berita.index', compact('beritas', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'kategori_berita_id' => 'required|exists:kategori_beritas,id',
            'content'            => 'required|string',
            'image'              => 'nullable|image|max:3072',
            'is_published'       => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('berita', 'public');
        }

        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $counter = 1;
        while (Berita::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        Berita::create([
            'title'              => $request->title,
            'slug'               => $slug,
            'kategori_berita_id' => $request->kategori_berita_id,
            'content'            => $request->content,
            'image'              => $imagePath,
            'is_published'       => $request->boolean('is_published', true),
            'views'              => 0,
        ]);

        return back()->with('success', 'Artikel berita baru berhasil dipublikasikan.');
    }

    public function update(Request $request, Berita $berita)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'kategori_berita_id' => 'required|exists:kategori_beritas,id',
            'content'            => 'required|string',
            'image'              => 'nullable|image|max:3072',
            'is_published'       => 'nullable|boolean',
            'views'              => 'nullable|integer|min:0',
        ]);

        $data = [
            'title'              => $request->title,
            'kategori_berita_id' => $request->kategori_berita_id,
            'content'            => $request->content,
            'is_published'       => $request->boolean('is_published', false),
        ];

        if ($request->filled('views')) {
            $data['views'] = (int) $request->views;
        }

        if ($request->title !== $berita->title) {
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $counter = 1;
            while (Berita::where('slug', $slug)->where('id', '!=', $berita->id)->exists()) {
                $slug = "{$originalSlug}-{$counter}";
                $counter++;
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('image')) {
            if ($berita->image && Storage::disk('public')->exists($berita->image)) {
                Storage::disk('public')->delete($berita->image);
            }
            $data['image'] = $request->file('image')->store('berita', 'public');
        }

        $berita->update($data);

        return back()->with('success', 'Artikel berita berhasil diperbarui.');
    }

    public function togglePublish(Berita $berita)
    {
        $berita->update([
            'is_published' => !$berita->is_published,
        ]);

        $status = $berita->is_published ? 'diterbitkan di website' : 'disimpan sebagai draf';
        return back()->with('success', "Artikel berhasil {$status}.");
    }

    public function destroy(Berita $berita)
    {
        if ($berita->image && Storage::disk('public')->exists($berita->image)) {
            Storage::disk('public')->delete($berita->image);
        }
        $berita->delete();
        return back()->with('success', 'Artikel berita berhasil dihapus.');
    }
}

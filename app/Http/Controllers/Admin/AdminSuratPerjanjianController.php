<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratPerjanjian;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSuratPerjanjianController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratPerjanjian::with('project')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('catatan_admin', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('nama_proyek', 'like', "%{$search}%");
                  });
            });
        }

        $suratPerjanjians = $query->paginate(10)->withQueryString();
        $projects = Project::orderBy('nama_proyek')->get();

        return view('admin.surat-perjanjian.index', compact('suratPerjanjians', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'nama'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'telepon'       => 'nullable|string|max:20',
            'keterangan'    => 'nullable|string',
            'catatan_admin' => 'nullable|string',
            'status'        => 'required|in:pending,diterima,ditolak',
            'file_surat'    => 'required|file|mimes:pdf,doc,docx|max:5120',
        ], [
            'project_id.required' => 'Wajib memilih proyek untuk dokumen SPK ini.',
            'file_surat.required' => 'Berkas SPK wajib diunggah.',
            'file_surat.mimes'    => 'Format file harus PDF, DOC, atau DOCX.',
            'file_surat.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $path = $request->file('file_surat')->store('surat-perjanjian', 'public');

        SuratPerjanjian::create([
            'project_id'    => $request->project_id,
            'nama'          => $request->nama,
            'email'         => $request->email,
            'telepon'       => $request->telepon,
            'keterangan'    => $request->keterangan,
            'catatan_admin' => $request->catatan_admin,
            'status'        => $request->status,
            'file_surat'    => $path,
        ]);

        return back()->with('success', 'Dokumen SPK berhasil diunggah dan ditautkan ke proyek.');
    }

    public function updateStatus(Request $request, SuratPerjanjian $suratPerjanjian)
    {
        $request->validate([
            'status'        => 'required|in:pending,diterima,ditolak',
            'project_id'    => 'nullable|exists:projects,id',
            'catatan_admin' => 'nullable|string',
        ]);

        $suratPerjanjian->update([
            'status'        => $request->status,
            'project_id'    => $request->project_id,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', 'Status & data surat perjanjian berhasil diperbarui.');
    }

    public function destroy(SuratPerjanjian $suratPerjanjian)
    {
        if ($suratPerjanjian->file_surat && Storage::disk('public')->exists($suratPerjanjian->file_surat)) {
            Storage::disk('public')->delete($suratPerjanjian->file_surat);
        }

        $suratPerjanjian->delete();
        return back()->with('success', 'Surat perjanjian berhasil dihapus.');
    }
}

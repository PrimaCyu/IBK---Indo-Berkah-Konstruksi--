<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratPerjanjian;
use Illuminate\Http\Request;

class AdminSuratPerjanjianController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratPerjanjian::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('catatan_admin', 'like', "%{$search}%");
            });
        }

        $suratPerjanjians = $query->paginate(10)->withQueryString();
        return view('admin.surat-perjanjian.index', compact('suratPerjanjians'));
    }

    public function updateStatus(Request $request, SuratPerjanjian $suratPerjanjian)
    {
        $request->validate([
            'status' => 'required|in:pending,diterima,ditolak',
            'catatan_admin' => 'nullable|string',
        ]);

        $suratPerjanjian->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', 'Status surat perjanjian berhasil diperbarui.');
    }

    public function destroy(SuratPerjanjian $suratPerjanjian)
    {
        $suratPerjanjian->delete();
        return back()->with('success', 'Surat perjanjian berhasil dihapus.');
    }
}

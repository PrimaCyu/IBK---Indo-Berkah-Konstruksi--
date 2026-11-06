<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penawaran;
use Illuminate\Http\Request;

class AdminPenawaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Penawaran::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('budget', 'like', "%{$search}%");
            });
        }

        $penawarans = $query->paginate(10)->withQueryString();
        return view('admin.penawaran.index', compact('penawarans'));
    }

    public function destroy(Penawaran $penawaran)
    {
        $penawaran->delete();
        return back()->with('success', 'Data penawaran berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penawaran;
use Illuminate\Http\Request;

class AdminPenawaranController extends Controller
{
    public function index()
    {
        $penawarans = Penawaran::latest()->paginate(10);
        return view('admin.penawaran.index', compact('penawarans'));
    }

    public function destroy(Penawaran $penawaran)
    {
        $penawaran->delete();
        return back()->with('success', 'Data penawaran berhasil dihapus.');
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Penawaran;
use Illuminate\Http\Request;

class PenawaranController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'telepon'   => 'required|string|max:20',
            'email'     => 'nullable|email|max:255',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'deskripsi' => 'required|string',
            'budget'    => 'required|string',
            'alamat'    => 'nullable|string|max:500',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('penawaran-foto', 'public');
        }

        Penawaran::create([
            'nama'      => $request->nama,
            'telepon'   => $request->telepon,
            'email'     => $request->email,
            'foto'      => $fotoPath,
            'deskripsi' => $request->deskripsi,
            'budget'    => $request->budget,
            'alamat'    => $request->alamat,

        ]);

        return back()->with('penawaran_success', true);
    }
           public function index()
    {
        return view('frontend.penawaran'); 
    }
}
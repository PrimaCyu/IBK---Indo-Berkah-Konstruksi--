<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeamMember;

class JoinUsController extends Controller
{
    public function index()
    {
        return view('frontend.join-us');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_pendek' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'telepon' => 'required|string|max:20',
            'profil_singkat' => 'required|string',
            'alamat' => 'nullable|string',
            'posisi' => 'required|string|in:Arsitek,Pekerja Konstruksi,Konsultan,Kepala Tukang,Pengawas Lapangan,Administrator,Pengawas Pemeriksa,Tukang,Asisten Tukang',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        $data['is_visible'] = false; // Memastikan data baru disembunyikan sampai di-ACC admin
        $data['status'] = 'pending'; // Status pelamar baru

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('team', 'public');
        }

        TeamMember::create($data);

        return redirect()->back()->with('success', 'Pendaftaran berhasil dikirim. Menunggu persetujuan admin.');
    }
}

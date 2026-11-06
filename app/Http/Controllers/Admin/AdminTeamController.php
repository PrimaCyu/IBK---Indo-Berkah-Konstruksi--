<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTeamController extends Controller
{
    public function index(Request $request)
    {
        $query = TeamMember::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('posisi', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%");
            });
        }

        $teamMembers = $query->paginate(10)->withQueryString();
        return view('admin.team.index', compact('teamMembers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'posisi'         => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'telepon'        => 'nullable|string|max:30',
            'profil_singkat' => 'nullable|string',
            'foto'           => 'nullable|image|max:3072',
            'is_visible'     => 'nullable|boolean',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('team', 'public');
        }

        TeamMember::create([
            'nama'           => $request->nama,
            'nama_pendek'    => explode(' ', $request->nama)[0],
            'posisi'         => $request->posisi,
            'email'          => $request->email,
            'telepon'        => $request->telepon,
            'profil_singkat' => $request->profil_singkat,
            'foto'           => $fotoPath,
            'status'         => 'accepted',
            'is_visible'     => $request->boolean('is_visible', true),
        ]);

        return back()->with('success', 'Anggota tim baru berhasil ditambahkan.');
    }

    public function toggleVisibility(TeamMember $teamMember)
    {
        $teamMember->update([
            'is_visible' => !$teamMember->is_visible,
        ]);

        $status = $teamMember->is_visible ? 'ditampilkan di website' : 'disembunyikan dari website';
        return back()->with('success', "Anggota tim berhasil {$status}.");
    }

    public function updateStatus(Request $request, TeamMember $teamMember)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $teamMember->update([
            'status'     => $request->status,
            'is_visible' => $request->status === 'accepted',
        ]);

        return back()->with('success', 'Status pendaftar tim berhasil diperbarui.');
    }

    public function destroy(TeamMember $teamMember)
    {
        if ($teamMember->foto && Storage::disk('public')->exists($teamMember->foto)) {
            Storage::disk('public')->delete($teamMember->foto);
        }
        $teamMember->delete();
        return back()->with('success', 'Data tim berhasil dihapus.');
    }
}

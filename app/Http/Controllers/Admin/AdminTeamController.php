<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class AdminTeamController extends Controller
{
    public function index()
    {
        $teamMembers = TeamMember::latest()->paginate(10);
        return view('admin.team.index', compact('teamMembers'));
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
            'status' => $request->status,
            'is_visible' => $request->status === 'accepted',
        ]);

        return back()->with('success', 'Status lamaran anggota tim berhasil diperbarui.');
    }

    public function destroy(TeamMember $teamMember)
    {
        $teamMember->delete();
        return back()->with('success', 'Data tim berhasil dihapus.');
    }
}


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function index()
    {
        $teamMembers = \App\Models\TeamMember::where('is_visible', true)
            ->where('status', 'accepted')
            ->get()
            ->map(function ($member) {
                // Di frontend hanya menampilkan nama pendek untuk publik
                $member->nama = $member->nama_pendek ?: $member->nama;
                return $member;
            })
            ->makeHidden(['email', 'telepon', 'alamat', 'rejection_reason']);

        return view('frontend.our-team', compact('teamMembers'));
    }
}

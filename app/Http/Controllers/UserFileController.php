<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserFileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file'        => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'project_id'  => 'required|exists:projects,id',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $project = \App\Models\Project::findOrFail($request->project_id);
        abort_if($user->role !== 'admin' && $project->user_id !== $user->id, 403, 'Anda tidak memiliki akses ke proyek ini.');

        $path = $request->file('file')->store('user-designs/' . $user->id, 'public');

        UserFile::create([
            'user_id'     => $project->user_id, // tautkan ke pemilik proyek
            'project_id'  => $request->project_id,
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $path,
            'type'        => 'design',
        ]);

        return back()->with('upload_success', 'Desain berhasil diunggah! Tim kami akan segera meninjaunya.');
    }

    public function destroy(UserFile $userFile)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_if($user->role !== 'admin' && ($userFile->user_id !== $user->id || $userFile->type !== 'design'), 403);

        Storage::disk('public')->delete($userFile->file_path);
        $userFile->delete();

        return back()->with('upload_success', 'File berhasil dihapus.');
    }
}
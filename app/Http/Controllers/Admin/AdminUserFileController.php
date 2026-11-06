<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminUserFileController extends Controller
{
    public function index(Request $request)
    {
        $query = UserFile::with(['user', 'project'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('nama_proyek', 'like', "%{$search}%");
                  });
            });
        }

        $files = $query->paginate(12)->withQueryString();
        $projects = Project::orderBy('nama_proyek')->get();
        $users = User::orderBy('name')->get();

        return view('admin.user-files.index', compact('files', 'projects', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'user_id'     => 'required|exists:users,id',
            'project_id'  => 'nullable|exists:projects,id',
            'type'        => 'required|in:design,progress',
            'description' => 'nullable|string',
            'file'        => 'required|file|max:10240', // Maks 10MB
        ]);

        $filePath = $request->file('file')->store('user_files', 'public');

        UserFile::create([
            'title'       => $request->title,
            'user_id'     => $request->user_id,
            'project_id'  => $request->project_id,
            'type'        => $request->type,
            'description' => $request->description,
            'file_path'   => $filePath,
        ]);

        return back()->with('success', 'Berkas berhasil diunggah.');
    }

    public function destroy(UserFile $userFile)
    {
        if ($userFile->file_path && Storage::disk('public')->exists($userFile->file_path)) {
            Storage::disk('public')->delete($userFile->file_path);
        }
        $userFile->delete();
        return back()->with('success', 'Berkas berhasil dihapus.');
    }
}

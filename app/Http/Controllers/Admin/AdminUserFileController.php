<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserFile;
use Illuminate\Http\Request;

class AdminUserFileController extends Controller
{
    public function index()
    {
        $files = UserFile::with(['user', 'project'])->latest()->paginate(12);
        return view('admin.user-files.index', compact('files'));
    }

    public function destroy(UserFile $userFile)
    {
        $userFile->delete();
        return back()->with('success', 'File berhasil dihapus.');
    }
}


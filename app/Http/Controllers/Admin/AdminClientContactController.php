<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminClientContactController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'owner')
            ->with(['projects' => function ($q) {
                $q->select('id', 'user_id', 'nama_proyek', 'status', 'alamat_proyek');
            }])
            ->withCount('projects')
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $clients = $query->paginate(15)->withQueryString();
        $totalClientsCount = User::where('role', 'owner')->count();

        return view('admin.cashbook.clients.index', compact('clients', 'totalClientsCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'phone'    => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama klien wajib diisi.',
            'email.required' => 'Email klien wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar pada sistem.',
            'password.min' => 'Password minimal 6 karakter jika diisi.',
        ]);

        $password = !empty($validated['password']) ? $validated['password'] : 'ibk12345';

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => 'owner',
            'password' => Hash::make($password),
        ]);

        return back()->with('success', 'Kontak klien "' . $validated['name'] . '" berhasil ditambahkan.');
    }
}


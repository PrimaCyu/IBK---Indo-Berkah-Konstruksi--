@extends('admin.layouts.app')

@section('title', 'Data Klien & User')
@section('page-title', 'Data Klien & Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Pengguna</li>
@endsection

@section('content')

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="/admin/users" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama, email, atau nomor telepon...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">Semua Hak Akses</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Klien / User Biasa</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="/admin/users" class="btn btn-sm btn-outline-secondary" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold"><i class="fas fa-users me-2 text-primary"></i>Daftar Akun Klien & Staf Terdaftar</span>
            <span class="badge bg-secondary-subtle text-secondary border font-monospace">{{ $users->total() }} Data</span>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-1"></i> Tambah Pengguna
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Nama Pengguna</th>
                        <th>Email & Kontak</th>
                        <th>Hak Akses (Role)</th>
                        <th>Proyek Terkait</th>
                        <th>Terdaftar</th>
                        <th class="text-end" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                        <tr>
                            <td class="text-muted">{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $u->name }}</div>
                                        @if($u->id === auth()->id())
                                            <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.65rem;">Akun Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $u->email }}</div>
                                @if($u->phone)
                                    <small class="text-muted">{{ $u->phone }}</small>
                                @endif
                            </td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge bg-dark font-monospace">ADMINISTRATOR</span>
                                @else
                                    <span class="badge bg-secondary font-monospace">KLIEN</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $u->projects_count }} Proyek</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $u->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end">
                                @if($u->id !== auth()->id())
                                    <form action="/admin/users/{{ $u->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun ini beserta seluruh data miliknya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-users fs-3 text-secondary"></i></div>
                                <div>Belum ada data pengguna yang terdaftar.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer py-2">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal Tambah Pengguna Baru -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-start">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2 text-primary"></i>Tambah Pengguna / Klien Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="Contoh: budi@gmail.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor WhatsApp / Telepon</label>
                        <input type="text" name="phone" class="form-control form-control-sm" placeholder="Contoh: 08123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hak Akses (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select form-select-sm" required>
                            <option value="user" selected>Klien / User Biasa</option>
                            <option value="admin">Administrator (Akses Penuh Backend)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kata Sandi (Password) <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Minimal 6 karakter" minlength="6" required>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

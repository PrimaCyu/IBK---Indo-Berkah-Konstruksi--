@extends('admin.layouts.app')

@section('title', 'Data Klien & User')
@section('page-title', 'Data Klien & Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Pengguna</li>
@endsection

@section('content')

@if($errors->any())
    <div class="alert alert-danger small" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Filter Bar (Reference Minimalist Standard) -->
<div class="card mb-3">
    <div class="card-body p-4">
        <form method="GET" action="/admin/users" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="filter-search" class="form-label">CARI PENGGUNA</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filter-search" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari nama, email, atau nomor telepon...">
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <label for="filter-role" class="form-label">HAK AKSES (ROLE)</label>
                <select id="filter-role" name="role" class="form-select">
                    <option value="">Semua Hak Akses</option>
                    <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Operasional</option>
                    <option value="accounting" {{ request('role') === 'accounting' ? 'selected' : '' }}>Accounting</option>
                    <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Klien</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100" style="height: 38px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="/admin/users" class="btn-filter-reset" title="Reset">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-dark"><i class="fas fa-users me-1.5 text-primary"></i> Daftar Akun Klien & Staf Terdaftar</span>
            <span class="badge-soft-neutral font-monospace">{{ $users->total() }} Data</span>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-1"></i> Tambah Pengguna
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 45px;">NO</th>
                        <th>NAMA PENGGUNA</th>
                        <th>EMAIL & KONTAK</th>
                        <th>HAK AKSES</th>
                        <th>PROYEK TERKAIT</th>
                        <th>TERDAFTAR</th>
                        <th class="text-end" style="width: 100px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                        <tr>
                            <td class="text-muted small">{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light border text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $u->name }}</div>
                                        @if($u->id === auth()->id())
                                            <span class="badge-soft-primary" style="font-size: 0.65rem; padding: 2px 6px;">Akun Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark small">{{ $u->email }}</div>
                                @if($u->phone)
                                    <small class="text-muted">{{ $u->phone }}</small>
                                @endif
                            </td>
                            <td>
                                @if($u->role === 'superadmin')
                                    <span class="badge-soft-danger font-monospace">SUPER ADMIN</span>
                                @elseif($u->role === 'admin')
                                    <span class="badge-soft-primary font-monospace">ADMIN OPERASIONAL</span>
                                @elseif($u->role === 'accounting')
                                    <span class="badge-soft-warning font-monospace">ACCOUNTING</span>
                                @else
                                    <span class="badge-soft-neutral font-monospace">KLIEN</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-soft-neutral">{{ $u->projects_count }} Proyek</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $u->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end">
                                @if($u->id !== auth()->id())
                                    <form action="/admin/users/{{ $u->id }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Hapus akun ini beserta seluruh data miliknya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon danger" title="Hapus Pengguna">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Tidak ada akun pengguna yang sesuai dengan pencarian.
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
                            <option value="owner" selected>Klien (Pemilik Proyek)</option>
                            <option value="admin">Admin Biasa (Operasional & Konten)</option>
                            <option value="accounting">Accounting (Staf Keuangan)</option>
                            <option value="superadmin">Super Admin (Akses Penuh)</option>
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

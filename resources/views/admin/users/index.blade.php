@extends('admin.layouts.app')

@section('title', 'Data Pengguna & Klien')
@section('page-title', 'Manajemen Pengguna & Klien')
@section('page-subtitle', 'Daftar akun terdaftar yang memiliki akses ke Client Portal dan Admin')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Pengguna</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-people-fill text-info me-2"></i> Pengguna Terdaftar ({{ $users->total() }})
        </h5>
        <span class="badge badge-gold px-3 py-2 rounded-pill">Total: {{ $users->total() }} Akun</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Pengguna</th>
                        <th>Email & Kontak</th>
                        <th>Role Akses</th>
                        <th>Jumlah Proyek</th>
                        <th>Terdaftar Sejak</th>
                        <th class="text-end" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                        <tr>
                            <td class="text-muted">{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                         style="width: 38px; height: 38px; background: #242424; font-size: 0.85rem; border: 2px solid var(--ibk-gold);">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div class="fw-bold text-dark">{{ $u->name }}</div>
                                </div>
                            </td>
                            <td>
                                <div><i class="bi bi-envelope text-muted me-1"></i> {{ $u->email }}</div>
                                @if($u->phone)
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i> {{ $u->phone }}</small>
                                @endif
                            </td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                                        <i class="bi bi-shield-lock-fill me-1"></i> Administrator
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                                        <i class="bi bi-person-fill me-1"></i> Klien (Owner)
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    {{ $u->projects_count }} Proyek
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $u->created_at->format('d M Y') }}</small>
                            </td>
                            <td class="text-end">
                                @if($u->id !== auth()->id())
                                    <form action="/admin/users/{{ $u->id }}" method="POST" onsubmit="return confirm('Hapus akun pengguna ini? Seluruh data proyek miliknya akan ikut terhapus!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 32px; height: 32px;" title="Hapus Akun">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-light text-muted border">Anda</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection


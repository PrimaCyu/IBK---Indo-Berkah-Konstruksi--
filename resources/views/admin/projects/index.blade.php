@extends('admin.layouts.app')

@section('title', 'Manajemen Proyek')
@section('page-title', 'Daftar Proyek Konstruksi')
@section('page-subtitle', 'Pantau tahapan pengerjaan, persentase progres, dan status proyek klien')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Proyek</li>
@endsection

@section('content')
<!-- Filter & Search Bar -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="/admin/projects" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Cari nama proyek, lokasi, atau klien...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif (On-Going)</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    <option value="Pembangunan Rumah" {{ request('kategori') === 'Pembangunan Rumah' ? 'selected' : '' }}>Pembangunan Rumah</option>
                    <option value="Gedung Komersial" {{ request('kategori') === 'Gedung Komersial' ? 'selected' : '' }}>Gedung Komersial</option>
                    <option value="Renovasi Bangunan" {{ request('kategori') === 'Renovasi Bangunan' ? 'selected' : '' }}>Renovasi Bangunan</option>
                    <option value="Konsultasi Konstruksi" {{ request('kategori') === 'Konsultasi Konstruksi' ? 'selected' : '' }}>Konsultasi Konstruksi</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="bi bi-filter"></i> Filter</button>
                @if(request()->hasAny(['search', 'status', 'kategori']))
                    <a href="/admin/projects" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-buildings-fill text-warning me-2"></i> Data Seluruh Proyek ({{ $projects->total() }})
        </h5>
        <span class="badge badge-gold px-3 py-2 rounded-pill">Total: {{ $projects->total() }} Proyek</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Proyek</th>
                        <th>Klien / Owner</th>
                        <th>Kategori</th>
                        <th>Persentase Progres</th>
                        <th>Status</th>
                        <th>Portofolio</th>
                        <th class="text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $project)
                        @php
                            $pct = $project->progress_percentage ?? 0;
                            $bgClass = $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-warning');
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $projects->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $project->nama_proyek }}</div>
                                <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                    <i class="bi bi-geo-alt"></i> {{ $project->alamat_proyek ?? 'Alamat belum diatur' }}
                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold text-secondary">{{ $project->user->name ?? 'Belum ada klien' }}</div>
                                <small class="text-muted">{{ $project->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    {{ $project->kategori }}
                                </span>
                            </td>
                            <td style="min-width: 150px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar {{ $bgClass }} progress-bar-striped progress-bar-animated" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="small fw-bold">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($project->status === 'selesai')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Selesai</span>
                                @elseif($project->status === 'aktif')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-1">{{ ucfirst($project->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($project->portfolio_approved)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                        <i class="bi bi-check-circle-fill"></i> Tampil
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill">Tidak</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="/admin/projects/{{ $project->id }}/edit" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Edit Progres & Detail">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="/admin/projects/{{ $project->id }}" method="POST" onsubmit="return confirm('Hapus proyek ini? Seluruh file dan portofolio terkait akan ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 32px; height: 32px;" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-buildings fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada proyek yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($projects->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $projects->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection


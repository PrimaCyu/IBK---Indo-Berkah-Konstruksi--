@extends('admin.layouts.app')

@section('title', 'Daftar Proyek')
@section('page-title', 'Daftar Proyek')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Proyek</li>
@endsection

@section('content')

<!-- Filter Card (Reference Minimalist Standard) -->
<div class="card mb-3">
    <div class="card-body p-4">
        <form method="GET" action="/admin/projects" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="filter-search" class="form-label">CARI PROYEK</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filter-search" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari nama proyek, lokasi, atau klien...">
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <label for="filter-status" class="form-label">STATUS</label>
                <select id="filter-status" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-3">
                <label for="filter-kategori" class="form-label">KATEGORI</label>
                <select id="filter-kategori" name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    <option value="Pembangunan Rumah" {{ request('kategori') === 'Pembangunan Rumah' ? 'selected' : '' }}>Pembangunan Rumah</option>
                    <option value="Gedung Komersial" {{ request('kategori') === 'Gedung Komersial' ? 'selected' : '' }}>Gedung Komersial</option>
                    <option value="Renovasi Bangunan" {{ request('kategori') === 'Renovasi Bangunan' ? 'selected' : '' }}>Renovasi Bangunan</option>
                    <option value="Konsultasi Konstruksi" {{ request('kategori') === 'Konsultasi Konstruksi' ? 'selected' : '' }}>Konsultasi Konstruksi</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100" style="height: 38px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'kategori']))
                    <a href="/admin/projects" class="btn-filter-reset" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Data Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-dark"><i class="fas fa-building me-1.5 text-primary"></i> Data Proyek Konstruksi</span>
            <span class="badge-soft-neutral font-monospace">{{ $projects->total() }} Data</span>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Proyek
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 45px;">NO</th>
                        <th style="width: 60px;">FOTO</th>
                        <th>NAMA PROYEK</th>
                        <th>KLIEN</th>
                        <th>KATEGORI</th>
                        <th>PROGRES</th>
                        <th>STATUS</th>
                        <th>PORTOFOLIO</th>
                        <th class="text-end" style="min-width: 170px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $project)
                        @php
                            $pct = $project->progress_percentage ?? 0;
                            $barClass = $pct >= 100 ? 'emerald' : ($pct >= 50 ? 'blue' : 'amber');
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $projects->firstItem() + $index }}</td>
                            <td style="width: 55px;">
                                @if($project->main_image)
                                    <img src="{{ asset('storage/' . $project->main_image) }}" alt="Foto" class="rounded border object-fit-cover shadow-2xs" style="width: 44px; height: 36px;">
                                @else
                                    <div class="rounded bg-light border text-muted d-flex align-items-center justify-content-center" style="width: 44px; height: 36px; font-size: 0.75rem;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $project->nama_proyek }}</div>
                                <small class="text-muted text-truncate d-block" style="max-width: 260px;">
                                    {{ $project->alamat_proyek ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <div class="text-dark small">{{ $project->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $project->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge-soft-neutral">{{ $project->kategori }}</span>
                            </td>
                            <td style="min-width: 130px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress-clean flex-grow-1">
                                        <div class="progress-clean-bar {{ $barClass }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <small class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $pct }}%</small>
                                </div>
                            </td>
                            <td>
                                @if($project->status === 'selesai')
                                    <span class="badge-soft-success">Selesai</span>
                                @elseif($project->status === 'aktif')
                                    <span class="badge-soft-primary">Aktif</span>
                                @else
                                    <span class="badge-soft-neutral">{{ ucfirst($project->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($project->portfolio_approved)
                                    <span class="badge-soft-success">Ya</span>
                                @else
                                    <span class="badge-soft-neutral">Tidak</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <a href="{{ route('admin.cashbook.show', $project) }}" class="btn-action-icon primary" title="Buka Buku Kas {{ $project->nama_proyek }}">
                                        <i class="fas fa-book"></i>
                                    </a>
                                    <a href="/admin/projects/{{ $project->id }}/edit" class="btn-action-icon" title="Edit Proyek">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="/admin/projects/{{ $project->id }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Hapus proyek ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon danger" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-folder-open fs-3 text-secondary"></i></div>
                                <div>Tidak ada data proyek yang sesuai dengan kriteria.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($projects->hasPages())
        <div class="card-footer py-2">
            {{ $projects->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection

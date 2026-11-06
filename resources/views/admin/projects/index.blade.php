@extends('admin.layouts.app')

@section('title', 'Daftar Proyek')
@section('page-title', 'Daftar Proyek')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Proyek</li>
@endsection

@section('content')

<!-- Filter Card -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="/admin/projects" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama proyek, lokasi, atau klien...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <option value="Pembangunan Rumah" {{ request('kategori') === 'Pembangunan Rumah' ? 'selected' : '' }}>Pembangunan Rumah</option>
                    <option value="Gedung Komersial" {{ request('kategori') === 'Gedung Komersial' ? 'selected' : '' }}>Gedung Komersial</option>
                    <option value="Renovasi Bangunan" {{ request('kategori') === 'Renovasi Bangunan' ? 'selected' : '' }}>Renovasi Bangunan</option>
                    <option value="Konsultasi Konstruksi" {{ request('kategori') === 'Konsultasi Konstruksi' ? 'selected' : '' }}>Konsultasi Konstruksi</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'kategori']))
                    <a href="/admin/projects" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Data Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold"><i class="fas fa-building me-2 text-primary"></i>Data Proyek Konstruksi</span>
            <span class="badge bg-secondary-subtle text-secondary border font-monospace">{{ $projects->total() }} Data</span>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Proyek
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Foto</th>
                        <th>Nama Proyek</th>
                        <th>Klien</th>
                        <th>Kategori</th>
                        <th>Progres</th>
                        <th>Status</th>
                        <th>Portofolio</th>
                        <th class="text-end" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $project)
                        @php
                            $pct = $project->progress_percentage ?? 0;
                            $barClass = $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-warning');
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $projects->firstItem() + $index }}</td>
                            <td style="width: 55px;">
                                @if($project->main_image)
                                    <img src="{{ asset('storage/' . $project->main_image) }}" alt="Foto" class="rounded border object-fit-cover" style="width: 45px; height: 35px;">
                                @else
                                    <div class="rounded bg-light border text-muted d-flex align-items-center justify-content-center" style="width: 45px; height: 35px; font-size: 0.75rem;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $project->nama_proyek }}</div>
                                <small class="text-muted text-truncate d-block" style="max-width: 260px;">
                                    {{ $project->alamat_proyek ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <div>{{ $project->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $project->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $project->kategori }}</span>
                            </td>
                            <td style="min-width: 130px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar {{ $barClass }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <small class="fw-bold">{{ $pct }}%</small>
                                </div>
                            </td>
                            <td>
                                @if($project->status === 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($project->status === 'aktif')
                                    <span class="badge bg-primary">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($project->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($project->portfolio_approved)
                                    <span class="badge bg-success-subtle text-success border">Ya</span>
                                @else
                                    <span class="badge bg-light text-muted border">Tidak</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="/admin/projects/{{ $project->id }}/edit" class="btn btn-xs btn-outline-primary py-1 px-2" title="Edit Proyek">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/admin/projects/{{ $project->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus proyek ini secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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

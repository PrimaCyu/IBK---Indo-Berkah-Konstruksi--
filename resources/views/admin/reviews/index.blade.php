@extends('admin.layouts.app')

@section('title', 'Ulasan Klien')
@section('page-title', 'Review & Testimoni Klien')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Ulasan</li>
@endsection

@section('content')

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="/admin/reviews" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari isi ulasan, klien, atau nama proyek...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status Publikasi</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Ditayangkan di Website</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Draf / Menunggu</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="/admin/reviews" class="btn btn-sm btn-outline-secondary" title="Reset">
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
            <span class="fw-bold"><i class="fas fa-star me-2 text-primary"></i>Daftar Moderasi Testimoni Klien</span>
            <span class="badge bg-secondary-subtle text-secondary border font-monospace">{{ $reviews->total() }} Data</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Foto</th>
                        <th>Klien</th>
                        <th>Proyek</th>
                        <th>Isi Ulasan Testimoni</th>
                        <th>Status Publikasi</th>
                        <th>Tanggal</th>
                        <th class="text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $reviews->firstItem() + $index }}</td>
                            <td style="width: 50px;">
                                @if($item->image)
                                    <a href="{{ asset('storage/' . $item->image) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="Foto" class="rounded border object-fit-cover" style="width: 40px; height: 40px;">
                                    </a>
                                @else
                                    <div class="rounded-circle bg-light border text-muted d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 0.75rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $item->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->project->nama_proyek ?? 'Umum' }}</span>
                            </td>
                            <td>
                                <div class="text-secondary small fst-italic" style="max-width: 320px;">
                                    "{{ $item->message }}"
                                </div>
                            </td>
                            <td>
                                @if($item->is_approved)
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Tayang</span>
                                @else
                                    <span class="badge bg-secondary">Draf</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end">
                                <form action="/admin/reviews/{{ $item->id }}/toggle" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($item->is_approved)
                                        <button type="submit" class="btn btn-xs btn-outline-warning py-1 px-2" title="Tarik dari Beranda Publik">
                                            <i class="fas fa-eye-slash me-1"></i> Draf
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-xs btn-outline-success py-1 px-2" title="Tayangkan di Beranda">
                                            <i class="fas fa-check me-1"></i> Tayang
                                        </button>
                                    @endif
                                </form>
                                <form action="/admin/reviews/{{ $item->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus testimoni ini?')">
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
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-star fs-3 text-secondary"></i></div>
                                <div>Belum ada ulasan atau testimoni dari klien.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reviews->hasPages())
        <div class="card-footer py-2">
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection

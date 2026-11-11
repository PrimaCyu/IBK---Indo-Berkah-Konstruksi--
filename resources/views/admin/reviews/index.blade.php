@extends('admin.layouts.app')

@section('title', 'Ulasan Klien')
@section('page-title', 'Review & Testimoni Klien')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Ulasan</li>
@endsection

@section('content')

<!-- Filter Bar (Reference Minimalist Standard) -->
<div class="card mb-3">
    <div class="card-body p-4">
        <form method="GET" action="/admin/reviews" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="filter-search" class="form-label">CARI ULASAN</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filter-search" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari isi ulasan, klien, atau nama proyek...">
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <label for="filter-status" class="form-label">STATUS PUBLIKASI</label>
                <select id="filter-status" name="status" class="form-select">
                    <option value="">Semua Status Publikasi</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Ditayangkan di Website</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Draf / Menunggu</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100" style="height: 38px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="/admin/reviews" class="btn-filter-reset" title="Reset">
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
            <span class="fw-bold text-dark"><i class="fas fa-star me-1.5 text-warning"></i> Moderasi Testimoni Klien</span>
            <span class="badge-soft-neutral font-monospace">{{ $reviews->total() }} Data</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 45px;">NO</th>
                        <th style="width: 60px;">FOTO</th>
                        <th>KLIEN</th>
                        <th>PROYEK</th>
                        <th>ISI ULASAN TESTIMONI</th>
                        <th>STATUS PUBLIKASI</th>
                        <th>TANGGAL</th>
                        <th class="text-end" style="width: 140px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $index => $item)
                        <tr>
                            <td class="text-muted small">{{ $reviews->firstItem() + $index }}</td>
                            <td style="width: 50px;">
                                @if($item->image)
                                    <a href="{{ asset('storage/' . $item->image) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="Foto" class="rounded-circle border object-fit-cover shadow-2xs" style="width: 38px; height: 38px;">
                                    </a>
                                @else
                                    <div class="rounded-circle bg-light border text-muted d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 0.75rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                 <div class="fw-bold text-dark">{{ $item->reviewer_name }}</div>
                                 <small class="text-muted">{{ $item->reviewer_email ?? ($item->user_id ? 'Akun Terdaftar' : 'Tamu / Umum') }}</small>
                            </td>
                            <td>
                                <span class="badge-soft-neutral">{{ $item->project->nama_proyek ?? 'Umum' }}</span>
                            </td>
                            <td>
                                <div class="text-secondary small fst-italic" style="max-width: 320px;">
                                    "{{ $item->message }}"
                                </div>
                            </td>
                            <td>
                                @if($item->is_approved)
                                    <span class="badge-soft-success"><i class="fas fa-check me-1"></i> Tayang</span>
                                @else
                                    <span class="badge-soft-neutral">Draf</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <form action="/admin/reviews/{{ $item->id }}/toggle" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('PATCH')
                                        @if($item->is_approved)
                                            <button type="submit" class="btn btn-sm btn-outline-warning py-1 px-2" title="Tarik dari Beranda Publik">
                                                <i class="fas fa-eye-slash me-1"></i> Draf
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2" title="Tayangkan di Beranda">
                                                <i class="fas fa-check me-1"></i> Tayang
                                            </button>
                                        @endif
                                    </form>
                                    <form action="/admin/reviews/{{ $item->id }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Hapus testimoni ini?')">
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

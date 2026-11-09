@extends('admin.layouts.app')

@section('title', 'Keuangan proyek')
@section('page-title', 'Keuangan proyek')

@section('page-actions')
    <nav aria-label="Breadcrumb">
        <ol class="breadcrumb mb-0 bg-white px-3 py-1 rounded-pill border small">
            <li class="breadcrumb-item active" aria-current="page">Keuangan proyek</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body p-3">
        <p class="small text-body-secondary mb-3">Pilih proyek untuk mencatat penerimaan, pengeluaran, dan melihat saldo kas internal.</p>
        <form method="GET" action="{{ route('admin.cashbook.index') }}" class="row g-2 align-items-end">
            <div class="col-md-7 col-lg-5">
                <label for="cashbook-search" class="form-label">Cari proyek atau klien</label>
                <input type="search" id="cashbook-search" name="search" value="{{ is_string(request('search')) ? request('search') : '' }}" class="form-control" placeholder="Nama proyek, lokasi, atau klien">
            </div>
            <div class="col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1" aria-hidden="true"></i> Cari
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.cashbook.index') }}" class="btn btn-outline-secondary">Reset pencarian</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center flex-wrap gap-2">
        <h2 class="fs-6 fw-bold mb-0"><i class="fas fa-book me-2" aria-hidden="true"></i>Buku kas per proyek</h2>
        <span class="badge bg-light text-dark border">{{ $projects->total() }} proyek</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" tabindex="0" role="region" aria-label="Daftar proyek, geser untuk melihat semua kolom">
            <table class="table table-hover mb-0">
                <caption class="visually-hidden">Pilih buku kas pada proyek yang ingin dikelola.</caption>
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Proyek</th>
                        <th scope="col">Klien</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $project)
                        <tr>
                            <td>{{ $projects->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold text-break">{{ $project->nama_proyek }}</div>
                                <small class="text-body-secondary text-break">{{ $project->alamat_proyek ?: 'Lokasi belum diisi' }}</small>
                            </td>
                            <td>
                                <div class="text-break">{{ $project->user->name ?? 'Klien tidak tersedia' }}</div>
                                <small class="text-body-secondary text-break">{{ $project->user->email ?? '' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ ucfirst($project->status) }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.cashbook.show', $project) }}" class="btn btn-sm btn-outline-primary text-nowrap" aria-label="Buka buku kas {{ $project->nama_proyek }}">
                                    <i class="fas fa-book-open me-1" aria-hidden="true"></i> Buka buku kas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                @if(request()->filled('search'))
                                    <p class="fw-semibold mb-1">Tidak ada proyek yang cocok.</p>
                                    <p class="small text-body-secondary mb-3">Coba kata kunci lain atau reset pencarian untuk melihat semua proyek.</p>
                                    <a href="{{ route('admin.cashbook.index') }}" class="btn btn-sm btn-outline-secondary">Reset pencarian</a>
                                @else
                                    <p class="fw-semibold mb-1">Belum ada proyek untuk dicatat.</p>
                                    <p class="small text-body-secondary mb-0">Minta admin membuat proyek terlebih dahulu. Setelah tersedia, pilih buku kas proyek untuk menambah transaksi.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($projects->hasPages())
        <div class="card-footer py-2">
            {{ $projects->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

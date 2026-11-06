@extends('admin.layouts.app')

@section('title', 'Portofolio Proyek')
@section('page-title', 'Portofolio Proyek')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Portofolio</li>
@endsection

@section('content')

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="/admin/portfolios" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama bangunan, deskripsi, atau kategori...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <option value="Rumah Tinggal" {{ request('kategori') === 'Rumah Tinggal' ? 'selected' : '' }}>Rumah Tinggal</option>
                    <option value="Villa & Resort" {{ request('kategori') === 'Villa & Resort' ? 'selected' : '' }}>Villa & Resort</option>
                    <option value="Gedung Komersial" {{ request('kategori') === 'Gedung Komersial' ? 'selected' : '' }}>Gedung Komersial</option>
                    <option value="Interior & Renovasi" {{ request('kategori') === 'Interior & Renovasi' ? 'selected' : '' }}>Interior & Renovasi</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'kategori']))
                    <a href="/admin/portfolios" class="btn btn-sm btn-outline-secondary" title="Reset">
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
            <span class="fw-bold"><i class="fas fa-images me-2 text-primary"></i>Galeri Portofolio Hasil Karya</span>
            <span class="badge bg-secondary-subtle text-secondary border font-monospace">{{ $portfolios->total() }} Data</span>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPortfolioModal">
            <i class="fas fa-plus me-1"></i> Tambah Portofolio
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Foto</th>
                        <th>Nama Bangunan / Program</th>
                        <th>Kategori</th>
                        <th>Deskripsi Ringkas</th>
                        <th>Waktu Pengerjaan</th>
                        <th class="text-end" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($portfolios as $index => $item)
                        @php
                            $img = $item->main_image;
                            $imgUrl = !empty($img) ? (str_starts_with($img, 'image/') ? asset($img) : asset('storage/' . $img)) : asset('image/ElementProgram/rumah2.jpg');
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $portfolios->firstItem() + $index }}</td>
                            <td style="width: 70px;">
                                <a href="{{ $imgUrl }}" target="_blank">
                                    <img src="{{ $imgUrl }}" alt="Foto" class="rounded border object-fit-cover" style="width: 60px; height: 42px;">
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->program }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->kategori }}</span>
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 280px;" title="{{ $item->deskripsi }}">
                                    {{ $item->deskripsi }}
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->waktu_pengerjaan ? $item->waktu_pengerjaan->format('m/Y') : '-' }}</small>
                            </td>
                            <td class="text-end">
                                <form action="/admin/portfolios/{{ $item->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus portofolio ini?')">
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
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-images fs-3 text-secondary"></i></div>
                                <div>Belum ada data portofolio yang ditampilkan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($portfolios->hasPages())
        <div class="card-footer py-2">
            {{ $portfolios->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal Tambah Portofolio -->
<div class="modal fade" id="addPortfolioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-start">
        <div class="modal-content">
            <form action="{{ route('admin.portfolios.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Portofolio Karya Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Bangunan / Program <span class="text-danger">*</span></label>
                        <input type="text" name="program" class="form-control form-control-sm" placeholder="Contoh: Modern Tropical Residence BSD" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori Bangunan <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select form-select-sm" required>
                            <option value="Rumah Tinggal" selected>Rumah Tinggal</option>
                            <option value="Villa & Resort">Villa & Resort</option>
                            <option value="Gedung Komersial">Gedung Komersial</option>
                            <option value="Interior & Renovasi">Interior & Renovasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Waktu Selesai Pengerjaan</label>
                        <input type="date" name="waktu_pengerjaan" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Utama Portofolio <span class="text-danger">*</span></label>
                        <input type="file" name="main_image" class="form-control form-control-sm" accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG, WebP (Maks. 3 MB)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi & Konsep Bangunan</label>
                        <textarea name="deskripsi" class="form-control form-control-sm" rows="3" placeholder="Jelaskan konsep arsitektur, luas lahan, jumlah lantai..."></textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Portofolio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

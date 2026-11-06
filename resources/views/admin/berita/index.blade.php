@extends('admin.layouts.app')

@section('title', 'Berita & Artikel')
@section('page-title', 'Manajemen Berita & Artikel')
@section('page-subtitle', 'Publikasi artikel edukasi konstruksi dan kabar terbaru PT Indo Berkah Konstruksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Berita</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-newspaper text-info me-2"></i> Daftar Berita & Artikel ({{ $beritas->total() }})
        </h5>
        <a href="/berita" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Halaman Berita
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Gambar</th>
                        <th>Judul Artikel</th>
                        <th>Kategori</th>
                        <th>Dibaca</th>
                        <th>Status Publikasi</th>
                        <th>Tanggal</th>
                        <th class="text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($beritas as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $beritas->firstItem() + $index }}</td>
                            <td>
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="Img" class="rounded object-fit-cover shadow-sm" style="width: 60px; height: 45px;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" style="width: 60px; height: 45px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 320px;">
                                    {{ $item->title }}
                                </div>
                                <small class="text-muted">Slug: /berita/{{ $item->slug }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->kategori->name ?? 'Umum' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    <i class="bi bi-eye"></i> {{ $item->views ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if($item->is_published)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Tayang</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-1">Draf</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <form action="/admin/berita/{{ $item->id }}/publish" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($item->is_published)
                                            <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2" title="Tarik ke Draf">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Terbitkan Sekarang">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        @endif
                                    </form>

                                    <form action="/admin/berita/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus artikel ini?')">
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
                                Belum ada artikel berita.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($beritas->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $beritas->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection


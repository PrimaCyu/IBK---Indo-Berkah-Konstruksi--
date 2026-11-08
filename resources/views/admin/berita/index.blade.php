@extends('admin.layouts.app')

@section('title', 'Berita & Artikel')
@section('page-title', 'Berita & Artikel')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Berita</li>
@endsection

@section('content')

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="/admin/berita" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari judul artikel atau isi berita...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status Publikasi</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Tayang di Website</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draf</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="/admin/berita" class="btn btn-sm btn-outline-secondary" title="Reset">
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
            <span class="fw-bold"><i class="fas fa-newspaper me-2 text-primary"></i>Daftar Berita, Edukasi & Artikel Website</span>
            <span class="badge bg-secondary-subtle text-secondary border font-monospace">{{ $beritas->total() }} Data</span>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
            <i class="fas fa-pen-nib me-1"></i> Tulis Artikel Baru
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Gambar</th>
                        <th>Judul Artikel</th>
                        <th>Kategori</th>
                        <th>Dibaca</th>
                        <th>Status</th>
                        <th>Tanggal Terbit</th>
                        <th class="text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($beritas as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $beritas->firstItem() + $index }}</td>
                            <td style="width: 60px;">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="Gambar" class="rounded border object-fit-cover" style="width: 50px; height: 38px;">
                                @else
                                    <div class="rounded bg-light border text-muted d-flex align-items-center justify-content-center" style="width: 50px; height: 38px; font-size: 0.75rem;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-truncate" style="max-width: 320px;">{{ $item->title }}</div>
                                <small class="text-muted">Slug: {{ $item->slug }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->kategori->nama ?? 'Umum' }}</span>
                            </td>
                            <td>
                                <small class="text-muted"><i class="far fa-eye me-1"></i>{{ $item->views ?? 0 }}</small>
                            </td>
                            <td>
                                @if($item->is_published)
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Tayang</span>
                                @else
                                    <span class="badge bg-secondary">Draf</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end">
                                <a href="/berita/{{ $item->slug }}" target="_blank" class="btn btn-xs btn-outline-secondary py-1 px-2" title="Lihat di Website">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#editNewsModal{{ $item->id }}" title="Edit Artikel">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="/admin/berita/{{ $item->id }}/publish" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($item->is_published)
                                        <button type="submit" class="btn btn-xs btn-outline-warning py-1 px-2" title="Tarik ke Draf">
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-xs btn-outline-success py-1 px-2" title="Terbitkan">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </form>
                                <form action="/admin/berita/{{ $item->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus artikel ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Berita {{ $item->id }} -->
                        <div class="modal fade text-start" id="editNewsModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('admin.berita.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold">
                                                <i class="fas fa-edit me-2 text-primary"></i>Edit Artikel Berita
                                            </h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" value="{{ $item->title }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Kategori Berita <span class="text-danger">*</span></label>
                                                    <select name="kategori_berita_id" class="form-select form-select-sm" required>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ $item->kategori_berita_id == $cat->id ? 'selected' : '' }}>
                                                                {{ $cat->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Ganti Gambar Sampul (Opsional)</label>
                                                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                                    <small class="text-muted d-block">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                                                    @if($item->image)
                                                        <div class="mt-2 d-flex align-items-center gap-2">
                                                            <img src="{{ asset('storage/' . $item->image) }}" class="rounded border object-fit-cover" style="width: 48px; height: 36px;">
                                                            <span class="text-muted small">Gambar aktif</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Jumlah Views</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text"><i class="far fa-eye"></i></span>
                                                        <input type="number" min="0" name="views" value="{{ $item->views ?? 0 }}" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 d-flex align-items-center pt-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublished{{ $item->id }}" {{ $item->is_published ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="isPublished{{ $item->id }}">
                                                            Tayang di Website
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Konten / Isi Artikel <span class="text-danger">*</span></label>
                                                    <textarea name="content" class="form-control form-control-sm" rows="8" required>{{ $item->content }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-newspaper fs-3 text-secondary"></i></div>
                                <div>Belum ada artikel berita yang dipublikasikan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($beritas->hasPages())
        <div class="card-footer py-2">
            {{ $beritas->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal Tulis Berita Baru -->
<div class="modal fade" id="addNewsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered text-start">
        <div class="modal-content">
            <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="fas fa-pen-nib me-2 text-primary"></i>Tulis Artikel Berita Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-sm" placeholder="Contoh: Tips Memilih Material Konstruksi Tahan Gempa" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori Berita <span class="text-danger">*</span></label>
                            <select name="kategori_berita_id" class="form-select form-select-sm" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Gambar Sampul (Thumbnail)</label>
                            <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, WebP (Maks. 3 MB)</small>
                        </div>
                        <div class="col-md-4 d-flex align-items-center pt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublished" checked>
                                <label class="form-check-label small" for="isPublished">
                                    Langsung Terbitkan ke Publik
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Konten / Isi Artikel <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control form-control-sm" rows="6" placeholder="Tuliskan isi artikel edukasi konstruksi di sini..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Simpan & Terbitkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

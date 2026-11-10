@extends('admin.layouts.app')

@section('title', 'File & Desain Proyek')
@section('page-title', 'File & Desain Proyek')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Berkas Proyek</li>
@endsection

@section('content')

<!-- Filter Bar (Reference Minimalist Standard) -->
<div class="card mb-3">
    <div class="card-body p-4">
        <form method="GET" action="/admin/user-files" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="filter-search" class="form-label">CARI BERKAS</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filter-search" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari judul berkas, nama proyek, atau klien...">
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <label for="filter-type" class="form-label">TIPE BERKAS</label>
                <select id="filter-type" name="type" class="form-select">
                    <option value="">Semua Tipe Berkas</option>
                    <option value="design" {{ request('type') === 'design' ? 'selected' : '' }}>Gambar Kerja / Desain 2D/3D</option>
                    <option value="progress" {{ request('type') === 'progress' ? 'selected' : '' }}>Foto / Laporan Progres Lapangan</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100" style="height: 38px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'type']))
                    <a href="/admin/user-files" class="btn-filter-reset" title="Reset">
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
            <span class="fw-bold text-dark"><i class="fas fa-folder-open me-1.5 text-primary"></i> Daftar Berkas & Desain Arsitektur</span>
            <span class="badge-soft-neutral font-monospace">{{ $files->total() }} Data</span>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
            <i class="fas fa-upload me-1"></i> Unggah Berkas Baru
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 45px;">NO</th>
                        <th style="width: 60px;">PRATINJAU</th>
                        <th>JUDUL BERKAS</th>
                        <th>KATEGORI</th>
                        <th>PROYEK TERKAIT</th>
                        <th>KLIEN / PEMILIK</th>
                        <th>TANGGAL UNGGAH</th>
                        <th class="text-end" style="width: 120px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($files as $index => $file)
                        @php
                            $isPdf = str_ends_with(strtolower($file->file_path), '.pdf');
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $files->firstItem() + $index }}</td>
                            <td style="width: 55px;">
                                @if($isPdf)
                                    <div class="rounded bg-light border text-danger d-flex align-items-center justify-content-center shadow-2xs" style="width: 45px; height: 35px; font-size: 1.1rem;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                @else
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $file->file_path) }}" alt="{{ $file->title }}" class="rounded border object-fit-cover shadow-2xs" style="width: 45px; height: 35px;">
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $file->title }}</div>
                                @if($file->description)
                                    <small class="text-muted text-truncate d-block" style="max-width: 250px;">{{ $file->description }}</small>
                                @endif
                            </td>
                            <td>
                                @if($file->type === 'design')
                                    <span class="badge-soft-primary">Desain 2D/3D</span>
                                @else
                                    <span class="badge-soft-success">Progres Lapangan</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-soft-neutral">{{ $file->project->nama_proyek ?? 'Umum' }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $file->user->name ?? '-' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $file->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn-action-icon" title="Buka Dokumen">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <form action="/admin/user-files/{{ $file->id }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Hapus file ini?')">
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
                                <div class="mb-2"><i class="fas fa-folder-open fs-3 text-secondary"></i></div>
                                <div>Belum ada berkas atau desain yang diunggah.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($files->hasPages())
        <div class="card-footer py-2">
            {{ $files->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal Unggah Berkas Baru -->
<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-start">
        <div class="modal-content">
            <form action="{{ route('admin.user-files.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="fas fa-upload me-2 text-primary"></i>Unggah Berkas / Desain Proyek</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Berkas <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="Contoh: Gambar Kerja Arsitektur Lantai 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Klien / Pemilik <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Klien --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Terkait Proyek (Opsional)</label>
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">-- Proyek Umum / Tanpa Proyek --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_proyek }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori Berkas <span class="text-danger">*</span></label>
                        <select name="type" class="form-select form-select-sm" required>
                            <option value="design" selected>Gambar Kerja / Desain (2D/3D)</option>
                            <option value="progress">Laporan Progres / Foto Lapangan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Dokumen / File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control form-control-sm" required>
                        <small class="text-muted">Mendukung PDF, JPG, PNG, CAD (Maks. 10 MB)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan Tambahan</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Catatan teknis mengenai berkas ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-upload me-1"></i> Unggah Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

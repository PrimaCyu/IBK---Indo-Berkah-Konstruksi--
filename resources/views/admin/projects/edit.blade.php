@extends('admin.layouts.app')

@section('title', 'Edit Proyek: ' . $project->nama_proyek)
@section('page-title', 'Edit Proyek')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="/admin/projects">Proyek</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark fs-6"><i class="fas fa-edit me-2 text-primary"></i>Ubah Data Proyek: {{ $project->nama_proyek }}</span>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.cashbook.show', $project) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-book me-1" aria-hidden="true"></i>Buku Kas
                    </a>
                    <a href="/admin/projects" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Nama Proyek -->
                        <div class="col-md-8">
                            <label class="form-label text-muted small fw-bold">NAMA PROYEK <span class="text-danger">*</span></label>
                            <input type="text" name="nama_proyek" class="form-control @error('nama_proyek') is-invalid @enderror" value="{{ old('nama_proyek', $project->nama_proyek) }}" required>
                            @error('nama_proyek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Klien -->
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">KLIEN / PEMILIK <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $project->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">KATEGORI PROYEK <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                <option value="Pembangunan Rumah" {{ old('kategori', $project->kategori) === 'Pembangunan Rumah' ? 'selected' : '' }}>Pembangunan Rumah</option>
                                <option value="Gedung Komersial" {{ old('kategori', $project->kategori) === 'Gedung Komersial' ? 'selected' : '' }}>Gedung Komersial</option>
                                <option value="Renovasi Bangunan" {{ old('kategori', $project->kategori) === 'Renovasi Bangunan' ? 'selected' : '' }}>Renovasi Bangunan</option>
                                <option value="Konsultasi Konstruksi" {{ old('kategori', $project->kategori) === 'Konsultasi Konstruksi' ? 'selected' : '' }}>Konsultasi Konstruksi</option>
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">STATUS PROYEK <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="aktif" {{ old('status', $project->status) === 'aktif' ? 'selected' : '' }}>Aktif (Berjalan)</option>
                                <option value="selesai" {{ old('status', $project->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ old('status', $project->status) === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">LOKASI / ALAMAT PROYEK</label>
                            <input type="text" name="alamat_proyek" class="form-control @error('alamat_proyek') is-invalid @enderror" value="{{ old('alamat_proyek', $project->alamat_proyek) }}">
                            @error('alamat_proyek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Progres Slider -->
                        <div class="col-12">
                            <label class="form-label d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold">PERSENTASE PROGRES PENGERJAAN</span>
                                <span class="badge bg-primary fs-6 px-3" id="progDisplay">{{ old('progress_percentage', $project->progress_percentage) }}%</span>
                            </label>
                            <input type="range" class="form-range" min="0" max="100" step="1" id="progRange" 
                                   value="{{ old('progress_percentage', $project->progress_percentage) }}"
                                   oninput="document.getElementById('progInput').value = this.value; document.getElementById('progDisplay').innerText = this.value + '%'">
                            <div class="input-group input-group-sm mt-1" style="max-width: 140px;">
                                <input type="number" name="progress_percentage" id="progInput" min="0" max="100" class="form-control @error('progress_percentage') is-invalid @enderror" 
                                       value="{{ old('progress_percentage', $project->progress_percentage) }}"
                                       oninput="document.getElementById('progRange').value = this.value; document.getElementById('progDisplay').innerText = this.value + '%'" required>
                                <span class="input-group-text bg-light text-muted fw-bold">%</span>
                            </div>
                            @error('progress_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Foto Utama -->
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">FOTO UTAMA PROYEK</label>
                            @if($project->main_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $project->main_image) }}" alt="Foto Proyek" class="rounded-3 border object-fit-cover shadow-sm" style="width: 120px; height: 80px;">
                                    <small class="d-block text-muted mt-1">Foto saat ini</small>
                                </div>
                            @endif
                            <input type="file" name="main_image" class="form-control @error('main_image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto</small>
                            @error('main_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tampilkan di Portofolio -->
                        <div class="col-md-6 d-flex align-items-center pt-4">
                            <div class="form-check p-2 border rounded-3 bg-light w-100">
                                <input class="form-check-input ms-1" type="checkbox" name="portfolio_approved" value="1" id="portfolioApproved" {{ old('portfolio_approved', $project->portfolio_approved) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold ms-2 text-dark" for="portfolioApproved">
                                    Tampilkan di Portofolio Publik Website
                                </label>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">DESKRIPSI & RUANG LINGKUP</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4">{{ old('deskripsi', $project->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <a href="/admin/projects" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Perbarui Proyek
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

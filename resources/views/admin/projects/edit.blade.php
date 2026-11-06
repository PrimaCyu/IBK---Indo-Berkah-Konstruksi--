@extends('admin.layouts.app')

@section('title', 'Edit Proyek: ' . $project->nama_proyek)
@section('page-title', 'Perbarui Proyek')
@section('page-subtitle', 'Update persentase progres, status penyelesaian, dan izin tampil di portofolio')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="/admin/projects">Proyek</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i> Form Perubahan Data Proyek
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="/admin/projects/{{ $project->id }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Klien / Pemilik Proyek</label>
                        <input type="text" class="form-control bg-light" value="{{ $project->user->name ?? 'Belum terhubung' }} ({{ $project->user->email ?? '-' }})" disabled>
                        <small class="text-muted">Klien terikat otomatis dari pendaftaran proyek.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Proyek <span class="text-danger">*</span></label>
                        <input type="text" name="nama_proyek" class="form-control @error('nama_proyek') is-invalid @enderror" value="{{ old('nama_proyek', $project->nama_proyek) }}" required>
                        @error('nama_proyek') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat / Lokasi Proyek</label>
                        <textarea name="alamat_proyek" class="form-control" rows="2">{{ old('alamat_proyek', $project->alamat_proyek) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori Layanan <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select" required>
                                <option value="Pembangunan Rumah" {{ old('kategori', $project->kategori) === 'Pembangunan Rumah' ? 'selected' : '' }}>Pembangunan Rumah</option>
                                <option value="Gedung Komersial" {{ old('kategori', $project->kategori) === 'Gedung Komersial' ? 'selected' : '' }}>Gedung Komersial</option>
                                <option value="Renovasi Bangunan" {{ old('kategori', $project->kategori) === 'Renovasi Bangunan' ? 'selected' : '' }}>Renovasi Bangunan</option>
                                <option value="Konsultasi Konstruksi" {{ old('kategori', $project->kategori) === 'Konsultasi Konstruksi' ? 'selected' : '' }}>Konsultasi Konstruksi</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status Proyek <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status', $project->status) === 'aktif' ? 'selected' : '' }}>Aktif (Sedang Dikerjakan)</option>
                                <option value="selesai" {{ old('status', $project->status) === 'selesai' ? 'selected' : '' }}>Selesai (100% Selesai)</option>
                                <option value="dibatalkan" {{ old('status', $project->status) === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Progress Slider & Input -->
                    <div class="card p-3 bg-light border-0 mb-4 rounded-3">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            <span>Persentase Progres Lapangan</span>
                            <span class="text-primary fw-bolder fs-5" id="progressValDisplay">{{ old('progress_percentage', $project->progress_percentage) }}%</span>
                        </label>
                        <input type="range" class="form-range" min="0" max="100" step="5" id="progressRange" 
                               value="{{ old('progress_percentage', $project->progress_percentage) }}"
                               oninput="document.getElementById('progressInput').value = this.value; document.getElementById('progressValDisplay').innerText = this.value + '%'">
                        <div class="input-group mt-2" style="max-width: 180px;">
                            <input type="number" name="progress_percentage" id="progressInput" min="0" max="100" class="form-control" 
                                   value="{{ old('progress_percentage', $project->progress_percentage) }}"
                                   oninput="document.getElementById('progressRange').value = this.value; document.getElementById('progressValDisplay').innerText = this.value + '%'" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted mt-2 d-block">Ubah persentase sesuai capaian fisik tukang di lapangan.</small>
                    </div>

                    <!-- Portofolio Toggle -->
                    <div class="form-check form-switch p-3 bg-light rounded-3 mb-4 ps-5">
                        <input class="form-check-input ms-n5" type="checkbox" name="portfolio_approved" id="portfolio_approved" value="1" {{ old('portfolio_approved', $project->portfolio_approved) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-dark" for="portfolio_approved">
                            Tampilkan Otomatis di Portofolio Website Publik
                        </label>
                        <small class="d-block text-muted">
                            Jika dicentang dan status proyek adalah <strong>Selesai</strong>, sistem otomatis membuat portofolio publik beserta foto galeri proyek ini.
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Deskripsi / Catatan Proyek</label>
                        <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $project->deskripsi) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/admin/projects" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-warning px-4 fw-bold" style="background-color: var(--ibk-gold); border-color: var(--ibk-gold); color: #111;">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


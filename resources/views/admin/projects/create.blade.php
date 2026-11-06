@extends('admin.layouts.app')

@section('title', 'Tambah Proyek Baru')
@section('page-title', 'Tambah Proyek Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="/admin/projects">Proyek</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Formulir Data Proyek Baru</span>
                <a href="/admin/projects" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Nama Proyek -->
                        <div class="col-md-8">
                            <label class="form-label">Nama Proyek <span class="text-danger">*</span></label>
                            <input type="text" name="nama_proyek" class="form-control form-control-sm @error('nama_proyek') is-invalid @enderror" value="{{ old('nama_proyek') }}" placeholder="Contoh: Pembangunan Villa Modern Canggu" required>
                            @error('nama_proyek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Klien / Pemilik -->
                        <div class="col-md-4">
                            <label class="form-label">Klien / Pemilik <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select form-select-sm @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Klien --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori Proyek -->
                        <div class="col-md-6">
                            <label class="form-label">Kategori Proyek <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select form-select-sm @error('kategori') is-invalid @enderror" required>
                                <option value="Pembangunan Rumah" {{ old('kategori') === 'Pembangunan Rumah' ? 'selected' : '' }}>Pembangunan Rumah</option>
                                <option value="Gedung Komersial" {{ old('kategori') === 'Gedung Komersial' ? 'selected' : '' }}>Gedung Komersial</option>
                                <option value="Renovasi Bangunan" {{ old('kategori') === 'Renovasi Bangunan' ? 'selected' : '' }}>Renovasi Bangunan</option>
                                <option value="Konsultasi Konstruksi" {{ old('kategori') === 'Konsultasi Konstruksi' ? 'selected' : '' }}>Konsultasi Konstruksi</option>
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Pengerjaan -->
                        <div class="col-md-3">
                            <label class="form-label">Status Proyek <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-sm @error('status') is-invalid @enderror" required>
                                <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif (Berjalan)</option>
                                <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ old('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Persentase Progres -->
                        <div class="col-md-3">
                            <label class="form-label">Progres (%) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="progress_percentage" min="0" max="100" class="form-control @error('progress_percentage') is-invalid @enderror" value="{{ old('progress_percentage', 0) }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('progress_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Alamat Proyek -->
                        <div class="col-12">
                            <label class="form-label">Lokasi / Alamat Proyek</label>
                            <input type="text" name="alamat_proyek" class="form-control form-control-sm @error('alamat_proyek') is-invalid @enderror" value="{{ old('alamat_proyek') }}" placeholder="Contoh: Jl. Sunset Road No. 88, Kuta, Bali">
                            @error('alamat_proyek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Foto Utama Proyek -->
                        <div class="col-md-6">
                            <label class="form-label">Foto Utama Proyek</label>
                            <input type="file" name="main_image" class="form-control form-control-sm @error('main_image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, WebP (Maks. 3 MB)</small>
                            @error('main_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Opsi Portofolio -->
                        <div class="col-md-6 d-flex align-items-center pt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="portfolio_approved" value="1" id="portfolioApproved" {{ old('portfolio_approved') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="portfolioApproved">
                                    Tampilkan di Portofolio Publik Website
                                </label>
                            </div>
                        </div>

                        <!-- Deskripsi Proyek -->
                        <div class="col-12">
                            <label class="form-label">Deskripsi & Ruang Lingkup Pengerjaan</label>
                            <textarea name="deskripsi" class="form-control form-control-sm @error('deskripsi') is-invalid @enderror" rows="4" placeholder="Jelaskan spesifikasi bangunan, material utama, atau catatan penting proyek...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="/admin/projects" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Proyek
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


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
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Ubah Data Proyek: {{ $project->nama_proyek }}</span>
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
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Nama Proyek -->
                        <div class="col-md-8">
                            <label class="form-label">Nama Proyek <span class="text-danger">*</span></label>
                            <input type="text" name="nama_proyek" class="form-control form-control-sm @error('nama_proyek') is-invalid @enderror" value="{{ old('nama_proyek', $project->nama_proyek) }}" required>
                            @error('nama_proyek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Klien -->
                        <div class="col-md-4">
                            <label class="form-label">Klien / Pemilik <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select form-select-sm @error('user_id') is-invalid @enderror" required>
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
                            <label class="form-label">Kategori Proyek <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select form-select-sm @error('kategori') is-invalid @enderror" required>
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
                            <label class="form-label">Status Proyek <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-sm @error('status') is-invalid @enderror" required>
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
                            <label class="form-label">Lokasi / Alamat Proyek</label>
                            <input type="text" name="alamat_proyek" class="form-control form-control-sm @error('alamat_proyek') is-invalid @enderror" value="{{ old('alamat_proyek', $project->alamat_proyek) }}">
                            @error('alamat_proyek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Progres Slider -->
                        <div class="col-12">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Persentase Progres Pengerjaan</span>
                                <span class="fw-bold text-primary fs-6" id="progDisplay">{{ old('progress_percentage', $project->progress_percentage) }}%</span>
                            </label>
                            <input type="range" class="form-range" min="0" max="100" step="5" id="progRange" 
                                   value="{{ old('progress_percentage', $project->progress_percentage) }}"
                                   oninput="document.getElementById('progInput').value = this.value; document.getElementById('progDisplay').innerText = this.value + '%'">
                            <div class="input-group input-group-sm mt-1" style="max-width: 140px;">
                                <input type="number" name="progress_percentage" id="progInput" min="0" max="100" class="form-control @error('progress_percentage') is-invalid @enderror" 
                                       value="{{ old('progress_percentage', $project->progress_percentage) }}"
                                       oninput="document.getElementById('progRange').value = this.value; document.getElementById('progDisplay').innerText = this.value + '%'" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('progress_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Foto Utama -->
                        <div class="col-md-6">
                            <label class="form-label">Foto Utama Proyek</label>
                            @if($project->main_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $project->main_image) }}" alt="Foto Proyek" class="rounded border object-fit-cover" style="width: 120px; height: 80px;">
                                    <small class="d-block text-muted">Foto saat ini</small>
                                </div>
                            @endif
                            <input type="file" name="main_image" class="form-control form-control-sm @error('main_image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto</small>
                            @error('main_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tampilkan di Portofolio -->
                        <div class="col-md-6 d-flex align-items-center pt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="portfolio_approved" value="1" id="portfolioApproved" {{ old('portfolio_approved', $project->portfolio_approved) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="portfolioApproved">
                                    Tampilkan di Portofolio Publik Website
                                </label>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12">
                            <label class="form-label">Deskripsi & Ruang Lingkup</label>
                            <textarea name="deskripsi" class="form-control form-control-sm @error('deskripsi') is-invalid @enderror" rows="4">{{ old('deskripsi', $project->deskripsi) }}</textarea>
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
                        <i class="fas fa-save me-1"></i> Perbarui Proyek
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

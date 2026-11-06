@extends('admin.layouts.app')

@section('title', 'File & Desain Proyek')
@section('page-title', 'Galeri Berkas & Desain Proyek')
@section('page-subtitle', 'Dokumen gambar arsitektur, foto progres mingguan, dan denah dari klien')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Berkas Proyek</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-images text-primary me-2"></i> Semua Berkas Terunggah ({{ $files->total() }})
        </h5>
        <span class="badge badge-gold px-3 py-2 rounded-pill">Total: {{ $files->total() }} File</span>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            @forelse($files as $file)
                @php
                    $isPdf = str_ends_with(strtolower($file->file_path), '.pdf');
                @endphp
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <div class="card h-100 border shadow-none rounded-3 overflow-hidden">
                        <div class="position-relative bg-light text-center" style="height: 180px;">
                            @if($isPdf)
                                <div class="d-flex flex-column align-items-center justify-content-center h-100 p-3">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger display-4 mb-2"></i>
                                    <span class="small fw-bold text-truncate w-100 px-2">{{ $file->title }}</span>
                                </div>
                            @else
                                <img src="{{ asset('storage/' . $file->file_path) }}" alt="{{ $file->title }}" class="w-100 h-100 object-fit-cover">
                            @endif

                            <span class="position-absolute top-2 start-2 badge {{ $file->type === 'design' ? 'bg-primary' : 'bg-success' }} shadow-sm">
                                {{ strtoupper($file->type) }}
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-1 text-truncate" title="{{ $file->title }}">{{ $file->title }}</h6>
                            <small class="text-muted d-block text-truncate mb-2">
                                <i class="bi bi-building"></i> {{ $file->project->nama_proyek ?? 'Proyek Umum' }}
                            </small>
                            <small class="text-secondary d-block mb-3">
                                <i class="bi bi-person"></i> {{ $file->user->name ?? 'Klien' }}
                            </small>
                            
                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                    <i class="bi bi-eye"></i> Buka
                                </a>
                                <form action="/admin/user-files/{{ $file->id }}" method="POST" onsubmit="return confirm('Hapus file ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 30px; height: 30px;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-folder2-open fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada file desain atau dokumentasi progres yang diunggah.
                </div>
            @endforelse
        </div>
    </div>
    @if($files->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $files->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection


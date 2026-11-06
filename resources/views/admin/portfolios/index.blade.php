@extends('admin.layouts.app')

@section('title', 'Portofolio Publik')
@section('page-title', 'Galeri Portofolio Website')
@section('page-subtitle', 'Daftar mahakarya arsitektur & konstruksi yang ditampilkan di website publik')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Portofolio</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-award-fill text-warning me-2"></i> Portofolio Ditayangkan ({{ $portfolios->total() }})
        </h5>
        <a href="/portofolio" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Galeri Publik
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Foto Utama</th>
                        <th>Nama Program / Bangunan</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Waktu Pengerjaan</th>
                        <th class="text-end" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($portfolios as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $portfolios->firstItem() + $index }}</td>
                            <td>
                                @php
                                    $img = $item->main_image;
                                    $imgUrl = !empty($img) ? (str_starts_with($img, 'image/') ? asset($img) : asset('storage/' . $img)) : asset('image/ElementProgram/rumah2.jpg');
                                @endphp
                                <a href="{{ $imgUrl }}" target="_blank">
                                    <img src="{{ $imgUrl }}" alt="Main" class="rounded shadow-sm object-fit-cover" style="width: 70px; height: 50px;">
                                </a>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->program }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->kategori }}</span>
                            </td>
                            <td>
                                <small class="text-secondary text-truncate d-inline-block" style="max-width: 300px;">
                                    {{ $item->deskripsi }}
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->waktu_pengerjaan ? $item->waktu_pengerjaan->format('M Y') : '-' }}</small>
                            </td>
                            <td class="text-end">
                                <form action="/admin/portfolios/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus portofolio ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 32px; height: 32px;" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Belum ada portofolio yang disetujui.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($portfolios->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $portfolios->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection


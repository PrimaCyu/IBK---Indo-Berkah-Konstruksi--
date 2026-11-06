@extends('admin.layouts.app')

@section('title', 'Ulasan & Testimoni')
@section('page-title', 'Moderasi Ulasan Klien')
@section('page-subtitle', 'Setujui atau sembunyikan testimoni yang akan ditampilkan di halaman depan website')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Review</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-star-fill text-warning me-2"></i> Daftar Testimoni & Ulasan Klien
        </h5>
        <span class="badge badge-gold px-3 py-2 rounded-pill">Total: {{ $reviews->total() }} Ulasan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Foto Lampiran</th>
                        <th>Klien</th>
                        <th>Proyek</th>
                        <th>Isi Ulasan Testimoni</th>
                        <th>Status Tampil</th>
                        <th>Waktu</th>
                        <th class="text-end" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $reviews->firstItem() + $index }}</td>
                            <td>
                                @if($item->image)
                                    <a href="{{ asset('storage/' . $item->image) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="Foto" class="rounded shadow-sm object-fit-cover" style="width: 50px; height: 50px;">
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->user->name ?? 'Klien' }}</div>
                                <small class="text-muted">{{ $item->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $item->project->nama_proyek ?? 'Umum' }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-secondary" style="max-width: 320px;">
                                    "{{ $item->message }}"
                                </div>
                            </td>
                            <td>
                                @if($item->is_approved)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Ditampilkan
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 py-1">
                                        Disembunyikan
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <form action="/admin/reviews/{{ $item->id }}/toggle" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($item->is_approved)
                                            <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2" title="Sembunyikan dari Website">
                                                <i class="bi bi-eye-slash"></i> Tarik
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Tampilkan di Website">
                                                <i class="bi bi-check2"></i> Tayangkan
                                            </button>
                                        @endif
                                    </form>

                                    <form action="/admin/reviews/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus ulasan ini?')">
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
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border" style="width: 70px; height: 70px;">
                                        <i class="bi bi-star-fill text-warning fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Belum Ada Review Testimoni</h6>
                                    <p class="text-muted small mb-0" style="max-width: 420px; margin: 0 auto;">
                                        Ulasan dari klien akan muncul di sini setelah mereka memberikan testimoni melalui Client Portal ketika proyek selesai.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reviews->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection


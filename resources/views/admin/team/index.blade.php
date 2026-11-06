@extends('admin.layouts.app')

@section('title', 'Tim & Pendaftar Join Us')
@section('page-title', 'Manajemen Tim & Rekrutmen')
@section('page-subtitle', 'Kelola data profesional tim konstruksi dan persetujuan pendaftar kerja baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Tim</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-people-fill text-warning me-2"></i> Anggota Tim & Pelamar Kerja ({{ $teamMembers->total() }})
        </h5>
        <a href="/our-team" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-box-arrow-up-right me-1"></i> Halaman Tim Publik
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Foto</th>
                        <th>Nama & Posisi</th>
                        <th>Kontak</th>
                        <th>Status Lamaran</th>
                        <th>Tampil di Web</th>
                        <th>Waktu Daftar</th>
                        <th class="text-end" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teamMembers as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $teamMembers->firstItem() + $index }}</td>
                            <td>
                                @if($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" class="rounded-circle object-fit-cover shadow-sm" style="width: 44px; height: 44px;">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-muted border" style="width: 44px; height: 44px;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->nama }}</div>
                                <span class="badge bg-light text-dark border">{{ $item->posisi }}</span>
                            </td>
                            <td>
                                <div class="small"><i class="bi bi-telephone text-primary"></i> {{ $item->telepon ?? '-' }}</div>
                                <div class="small text-muted"><i class="bi bi-envelope"></i> {{ $item->email }}</div>
                            </td>
                            <td>
                                @if($item->status === 'accepted')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Diterima</span>
                                @elseif($item->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Ditolak</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_visible)
                                    <span class="badge bg-success-subtle text-success border rounded-pill">Ya</span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill">Tidak</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <form action="/admin/team/{{ $item->id }}/status" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($item->status !== 'accepted')
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Terima Lamaran">
                                                <i class="bi bi-check2"></i> Terima
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2" title="Tolak / Nonaktifkan">
                                                <i class="bi bi-x"></i> Tolak
                                            </button>
                                        @endif
                                    </form>

                                    <form action="/admin/team/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus data pelamar ini?')">
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
                                Belum ada pendaftaran tim atau pelamar kerja.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($teamMembers->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $teamMembers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection


@extends('admin.layouts.app')

@section('title', 'Surat Perjanjian (SPK)')
@section('page-title', 'Surat Perjanjian Kerja (SPK)')
@section('page-subtitle', 'Verifikasi dokumen kesepakatan kontrak antara klien dan PT Indo Berkah Konstruksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Surat Perjanjian</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-file-earmark-check-fill text-success me-2"></i> Dokumen Surat Perjanjian Masuk
        </h5>
        <span class="badge badge-gold px-3 py-2 rounded-pill">Total: {{ $suratPerjanjians->total() }} Dokumen</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Klien</th>
                        <th>Kontak</th>
                        <th>Berkas Dokumen</th>
                        <th>Status Verifikasi</th>
                        <th>Catatan Admin</th>
                        <th>Tanggal Kirim</th>
                        <th class="text-end" style="width: 200px;">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratPerjanjians as $index => $spk)
                        <tr>
                            <td class="text-muted">{{ $suratPerjanjians->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $spk->nama }}</div>
                                @if($spk->keterangan)
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;" title="{{ $spk->keterangan }}">
                                        {{ $spk->keterangan }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="small"><i class="bi bi-telephone text-primary"></i> {{ $spk->telepon ?? '-' }}</div>
                                <div class="small text-muted"><i class="bi bi-envelope"></i> {{ $spk->email ?? '-' }}</div>
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $spk->file_surat) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                    <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Buka File
                                </a>
                            </td>
                            <td>
                                @if($spk->status === 'diterima')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Diterima / Sah</span>
                                @elseif($spk->status === 'ditolak')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Ditolak</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">Menunggu Verifikasi</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-secondary">{{ $spk->catatan_admin ?? '-' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $spk->created_at->format('d M Y, H:i') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Modal Trigger -->
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalStatus{{ $spk->id }}">
                                        <i class="bi bi-check-all"></i> Status
                                    </button>
                                    <form action="/admin/surat-perjanjian/{{ $spk->id }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 32px; height: 32px;" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Status Modal -->
                                <div class="modal fade" id="modalStatus{{ $spk->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog text-start">
                                        <div class="modal-content">
                                            <form action="/admin/surat-perjanjian/{{ $spk->id }}/status" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold">Verifikasi SPK: {{ $spk->nama }}</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Status Surat</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="pending" {{ $spk->status === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                                                            <option value="diterima" {{ $spk->status === 'diterima' ? 'selected' : '' }}>Diterima (Disetujui)</option>
                                                            <option value="ditolak" {{ $spk->status === 'ditolak' ? 'selected' : '' }}>Ditolak (Perlu Revisi)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Catatan Admin untuk Klien</label>
                                                        <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Contoh: Dokumen disetujui, proyek segera dimulai...">{{ $spk->catatan_admin }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Status</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-text fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada berkas surat perjanjian yang diunggah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($suratPerjanjians->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $suratPerjanjians->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection


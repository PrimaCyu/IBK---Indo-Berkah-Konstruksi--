@extends('admin.layouts.app')

@section('title', 'Surat Perjanjian (SPK)')
@section('page-title', 'Surat Perjanjian Kerja (SPK)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Surat Perjanjian</li>
@endsection

@section('content')

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="/admin/surat-perjanjian" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama, kontak, atau keterangan...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="/admin/surat-perjanjian" class="btn btn-sm btn-outline-secondary" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold"><i class="fas fa-file-contract me-2 text-primary"></i>Daftar Berkas Kontrak Kerja (SPK)</span>
            <span class="badge bg-secondary-subtle text-secondary border font-monospace">{{ $suratPerjanjians->total() }} Data</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Nama Pengirim</th>
                        <th>Kontak</th>
                        <th>Berkas SPK</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
                        <th>Tanggal Pengajuan</th>
                        <th class="text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratPerjanjians as $index => $spk)
                        <tr>
                            <td class="text-muted">{{ $suratPerjanjians->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold">{{ $spk->nama }}</div>
                                @if($spk->keterangan)
                                    <small class="text-muted d-block text-truncate" style="max-width: 220px;" title="{{ $spk->keterangan }}">{{ $spk->keterangan }}</small>
                                @endif
                            </td>
                            <td>
                                <small class="d-block">{{ $spk->telepon ?? '-' }}</small>
                                <small class="text-muted d-block">{{ $spk->email ?? '-' }}</small>
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $spk->file_surat) }}" target="_blank" class="btn btn-xs btn-outline-secondary py-1 px-2" title="Unduh Berkas SPK">
                                    <i class="fas fa-file-pdf me-1 text-danger"></i> Unduh Dokumen
                                </a>
                            </td>
                            <td>
                                @if($spk->status === 'diterima')
                                    <span class="badge bg-success">Diterima</span>
                                @elseif($spk->status === 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 180px;">{{ $spk->catatan_admin ?? '-' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $spk->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#spkModal{{ $spk->id }}" title="Ubah Status Verifikasi">
                                    <i class="fas fa-check-double me-1"></i> Verifikasi
                                </button>
                                <form action="/admin/surat-perjanjian/{{ $spk->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus surat perjanjian ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                                <!-- Modal Status Verifikasi -->
                                <div class="modal fade" id="spkModal{{ $spk->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <div class="modal-content">
                                            <form action="/admin/surat-perjanjian/{{ $spk->id }}/status" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold"><i class="fas fa-file-contract me-2 text-primary"></i>Verifikasi SPK: {{ $spk->nama }}</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status Verifikasi <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-select form-select-sm" required>
                                                            <option value="pending" {{ $spk->status === 'pending' ? 'selected' : '' }}>Pending (Menunggu Peninjauan)</option>
                                                            <option value="diterima" {{ $spk->status === 'diterima' ? 'selected' : '' }}>Diterima (Disetujui)</option>
                                                            <option value="ditolak" {{ $spk->status === 'ditolak' ? 'selected' : '' }}>Ditolak (Perlu Revisi)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan Admin / Tindak Lanjut</label>
                                                        <textarea name="catatan_admin" class="form-control form-control-sm" rows="3" placeholder="Tambahkan catatan kepada klien mengenai alasan persetujuan/penolakan...">{{ $spk->catatan_admin }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-between">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-save me-1"></i> Simpan Status
                                                    </button>
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
                                <div class="mb-2"><i class="fas fa-file-contract fs-3 text-secondary"></i></div>
                                <div>Belum ada berkas surat perjanjian kerja yang diajukan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($suratPerjanjians->hasPages())
        <div class="card-footer py-2">
            {{ $suratPerjanjians->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection

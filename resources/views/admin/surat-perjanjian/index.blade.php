@extends('admin.layouts.app')

@section('title', 'Surat Perjanjian (SPK)')
@section('page-title', 'Surat Perjanjian Kerja (SPK)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Surat Perjanjian</li>
@endsection

@section('page-actions')
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalUploadSpk">
        <i class="fas fa-file-upload me-1"></i> + Unggah SPK Proyek
    </button>
@endsection

@section('content')

<!-- Filter Bar (Reference Minimalist Standard) -->
<div class="card mb-3">
    <div class="card-body p-4">
        <form method="GET" action="/admin/surat-perjanjian" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="filter-search" class="form-label">CARI DOKUMEN</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filter-search" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari nama, proyek, kontak, keterangan...">
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <label for="filter-project" class="form-label">PROYEK TERKAIT</label>
                <select id="filter-project" name="project_id" class="form-select">
                    <option value="">Semua Proyek</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_proyek }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-3">
                <label for="filter-status" class="form-label">STATUS</label>
                <select id="filter-status" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                    <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima (Sah)</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak (Perlu Revisi)</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100" style="height: 38px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'project_id', 'status']))
                    <a href="/admin/surat-perjanjian" class="btn-filter-reset" title="Reset">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-dark"><i class="fas fa-file-contract me-1.5 text-primary"></i> Daftar Berkas Kontrak Kerja (SPK)</span>
            <span class="badge-soft-neutral font-monospace">{{ $suratPerjanjians->total() }} Data</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 45px;">NO</th>
                        <th>NAMA & KONTAK</th>
                        <th>TERKAIT PROYEK</th>
                        <th>BERKAS SPK</th>
                        <th>STATUS</th>
                        <th>CATATAN ADMIN</th>
                        <th>TANGGAL</th>
                        <th class="text-end" style="width: 150px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratPerjanjians as $index => $spk)
                        <tr>
                            <td class="text-muted small">{{ $suratPerjanjians->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $spk->nama }}</div>
                                <small class="text-muted d-block">
                                    {{ $spk->telepon ?: '-' }} @if($spk->email) &bull; {{ $spk->email }} @endif
                                </small>
                                @if($spk->keterangan)
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;" title="{{ $spk->keterangan }}">
                                        <em>"{{ $spk->keterangan }}"</em>
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($spk->project)
                                    <a href="/admin/projects/{{ $spk->project->id }}/edit" class="badge-soft-primary text-decoration-none">
                                        <i class="fas fa-building me-1"></i>{{ $spk->project->nama_proyek }}
                                    </a>
                                @else
                                    <span class="badge-soft-neutral">Umum / Belum Ditautkan</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $spk->file_surat) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2.5" title="Unduh Berkas SPK">
                                    <i class="fas fa-file-pdf me-1 text-danger"></i> Unduh
                                </a>
                            </td>
                            <td>
                                @if($spk->status === 'diterima')
                                    <span class="badge-soft-success">Diterima / Sah</span>
                                @elseif($spk->status === 'ditolak')
                                    <span class="badge-soft-danger">Ditolak / Revisi</span>
                                @else
                                    <span class="badge-soft-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 160px;">{{ $spk->catatan_admin ?? '-' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $spk->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#spkModal{{ $spk->id }}" title="Ubah Status / Tautkan Proyek">
                                        <i class="fas fa-check-double me-1"></i> Verifikasi
                                    </button>
                                    <form action="/admin/surat-perjanjian/{{ $spk->id }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Hapus surat perjanjian ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon danger" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>

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
                                                        <label class="form-label">Tautkan ke Proyek</label>
                                                        <select name="project_id" class="form-select form-select-sm">
                                                            <option value="">— Tidak Ditautkan (Umum) —</option>
                                                            @foreach($projects as $p)
                                                                <option value="{{ $p->id }}" {{ $spk->project_id == $p->id ? 'selected' : '' }}>
                                                                    {{ $p->nama_proyek }} ({{ $p->user ? $p->user->name : 'Klien' }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted" style="font-size: 0.75rem;">Menghubungkan SPK ini ke portal proyek klien yang bersangkutan.</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Status Verifikasi <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-select form-select-sm" required>
                                                            <option value="pending" {{ $spk->status === 'pending' ? 'selected' : '' }}>Pending (Menunggu Peninjauan)</option>
                                                            <option value="diterima" {{ $spk->status === 'diterima' ? 'selected' : '' }}>Diterima (Sah / Disetujui)</option>
                                                            <option value="ditolak" {{ $spk->status === 'ditolak' ? 'selected' : '' }}>Ditolak (Perlu Revisi)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan Admin / Catatan Revisi</label>
                                                        <textarea name="catatan_admin" class="form-control form-control-sm" rows="3" placeholder="Tambahkan catatan kepada klien mengenai status SPK ini...">{{ $spk->catatan_admin }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-between">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-save me-1"></i> Simpan Verifikasi
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
                                <div>Belum ada berkas surat perjanjian kerja yang terdata.</div>
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

<!-- Modal Upload Dokumen SPK Baru (Admin) -->
<div class="modal fade" id="modalUploadSpk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.surat-perjanjian.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="fas fa-file-upload me-2 text-primary"></i>Unggah Dokumen SPK Resmi</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Proyek Terkait <span class="text-danger">*</span></label>
                        <select name="project_id" id="spk_project_select" class="form-select form-select-sm" required onchange="fillSpkClientInfo(this)">
                            <option value="">— Pilih Proyek —</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" 
                                    data-name="{{ $p->user ? $p->user->name : '' }}" 
                                    data-email="{{ $p->user ? $p->user->email : '' }}" 
                                    data-phone="{{ $p->user ? $p->user->phone : '' }}">
                                    {{ $p->nama_proyek }} ({{ $p->user ? $p->user->name : 'Klien' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Klien / Pihak Kedua <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="spk_client_name" class="form-control form-control-sm" required placeholder="Nama Klien">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon / WA</label>
                            <input type="text" name="telepon" id="spk_client_phone" class="form-control form-control-sm" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Klien</label>
                        <input type="email" name="email" id="spk_client_email" class="form-control form-control-sm" placeholder="klien@email.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Dokumen <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-select-sm" required>
                            <option value="diterima">Diterima / Sah (Resmi)</option>
                            <option value="pending">Pending (Draft / Menunggu TTD Klien)</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Berkas SPK (PDF, DOC, DOCX) <span class="text-danger">*</span></label>
                        <input type="file" name="file_surat" class="form-control form-control-sm" accept=".pdf,.doc,.docx" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Maksimal 5MB.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan / Deskripsi SPK</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Contoh: SPK Tahap 1 Pembangunan Rumah Tinggal Jl. Merdeka..."></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Catatan Tambahan untuk Klien</label>
                        <textarea name="catatan_admin" class="form-control form-control-sm" rows="2" placeholder="Catatan opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-upload me-1"></i> Simpan & Tautkan SPK
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function fillSpkClientInfo(select) {
    const selected = select.options[select.selectedIndex];
    if (selected && selected.value) {
        document.getElementById('spk_client_name').value = selected.getAttribute('data-name') || '';
        document.getElementById('spk_client_phone').value = selected.getAttribute('data-phone') || '';
        document.getElementById('spk_client_email').value = selected.getAttribute('data-email') || '';
    }
}
</script>

@endsection

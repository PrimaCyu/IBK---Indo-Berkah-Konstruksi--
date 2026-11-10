@extends('admin.layouts.app')

@section('title', 'Tim & Rekrutmen')
@section('page-title', 'Tim & Rekrutmen')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Tim</li>
@endsection

@section('content')

<!-- Filter Bar (Reference Minimalist Standard) -->
<div class="card mb-3">
    <div class="card-body p-4">
        <form method="GET" action="/admin/team" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="filter-search" class="form-label">CARI ANGGOTA / PELAMAR</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filter-search" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari nama, posisi, email, atau telepon...">
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <label for="filter-status" class="form-label">STATUS REKRUTMEN</label>
                <select id="filter-status" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Diterima / Tim Aktif</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Pelamar Baru)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100" style="height: 38px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="/admin/team" class="btn-filter-reset" title="Reset">
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
            <span class="fw-bold text-dark"><i class="fas fa-id-badge me-1.5 text-primary"></i> Daftar Staf Inti & Pelamar Kerja</span>
            <span class="badge-soft-neutral font-monospace">{{ $teamMembers->total() }} Data</span>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTeamModal">
            <i class="fas fa-user-plus me-1"></i> Tambah Anggota Tim
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 45px;">NO</th>
                        <th style="width: 55px;">FOTO</th>
                        <th>NAMA & POSISI</th>
                        <th>KONTAK</th>
                        <th>STATUS</th>
                        <th>TAMPIL PUBLIK</th>
                        <th>TANGGAL TERDAFTAR</th>
                        <th class="text-end" style="width: 140px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teamMembers as $index => $item)
                        <tr>
                            <td class="text-muted small">{{ $teamMembers->firstItem() + $index }}</td>
                            <td style="width: 50px;">
                                @if($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" class="rounded-circle border object-fit-cover shadow-2xs" style="width: 36px; height: 36px;">
                                @else
                                    <div class="rounded-circle bg-light border text-muted d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.8rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->nama }}</div>
                                <span class="badge-soft-neutral">{{ $item->posisi }}</span>
                            </td>
                            <td>
                                <div class="text-dark small">{{ $item->telepon ?? '-' }}</div>
                                <small class="text-muted">{{ $item->email }}</small>
                            </td>
                            <td>
                                @if($item->status === 'accepted')
                                    <span class="badge-soft-success">Diterima</span>
                                @elseif($item->status === 'rejected')
                                    <span class="badge-soft-danger">Ditolak</span>
                                @else
                                    <span class="badge-soft-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_visible)
                                    <span class="badge-soft-success">Ya</span>
                                @else
                                    <span class="badge-soft-neutral">Tidak</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <form action="/admin/team/{{ $item->id }}/visibility" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('PATCH')
                                        @if($item->is_visible)
                                            <button type="submit" class="btn-action-icon" title="Sembunyikan dari Website">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn-action-icon primary" title="Tampilkan di Website">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </form>
                                    <form action="/admin/team/{{ $item->id }}/status" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('PATCH')
                                        @if($item->status !== 'accepted')
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" class="btn-action-icon" style="color: #059669; border-color: #a7f3d0;" title="Terima Lamaran">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn-action-icon" title="Tolak / Nonaktifkan">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </form>
                                    <form action="/admin/team/{{ $item->id }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Hapus data tim ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon danger" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-id-badge fs-3 text-secondary"></i></div>
                                <div>Belum ada data staf tim atau pelamar kerja.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($teamMembers->hasPages())
        <div class="card-footer py-2">
            {{ $teamMembers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal Tambah Anggota Tim -->
<div class="modal fade" id="addTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-start">
        <div class="modal-content">
            <form action="{{ route('admin.team.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2 text-primary"></i>Tambah Anggota Tim Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control form-control-sm" placeholder="Contoh: Ir. Hendra Gunawan, S.T." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Posisi / Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="posisi" class="form-control form-control-sm" placeholder="Contoh: Project Manager / Lead Architect" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="email@perusahaan.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor WhatsApp</label>
                            <input type="text" name="telepon" class="form-control form-control-sm" placeholder="08123456789">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" name="foto" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG (Maks. 3 MB)</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_visible" value="1" id="teamVisible" checked>
                            <label class="form-check-label small" for="teamVisible">
                                Tampilkan di Halaman "Our Team" Website Publik
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profil Singkat / Pengalaman</label>
                        <textarea name="profil_singkat" class="form-control form-control-sm" rows="2" placeholder="Keahlian spesifik, sertifikasi, pengalaman proyek..."></textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Anggota Tim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

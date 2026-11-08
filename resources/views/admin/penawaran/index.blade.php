@extends('admin.layouts.app')

@section('title', 'Penawaran Masuk (RFQ)')
@section('page-title', 'Penawaran Masuk (RFQ)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Penawaran</li>
@endsection

@section('content')

<!-- Notifikasi Berhasil (Auto Hilang dalam 5 Detik) -->
@if(session('approved_success'))
    @php $approved = session('approved_success'); @endphp
    <div class="alert alert-success alert-dismissible fade show p-3 shadow-sm mb-3 auto-dismiss-alert" role="alert">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <strong><i class="fas fa-check-circle me-1"></i> Penawaran Berhasil Disetujui!</strong>
                Akun Klien <strong>{{ $approved['user_name'] }}</strong> (Password: <code class="bg-white px-2 py-0.5 rounded border text-dark">{{ $approved['plain_password'] }}</code>) & <strong>{{ $approved['project_name'] }}</strong> telah aktif.
            </div>
            <div class="d-flex gap-2">
                <a href="{{ $approved['wa_link'] }}" target="_blank" class="btn btn-sm btn-success py-1 px-2 fw-semibold">
                    <i class="fab fa-whatsapp me-1"></i> Kirim Akses via WA
                </a>
                <a href="/admin/projects" class="btn btn-sm btn-outline-dark py-1 px-2">Lihat Proyek</a>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filter & Search Bar (Minimalis & Rapi) -->
<div class="card mb-3">
    <div class="card-body p-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <!-- Status Tabs Minimalis -->
        <div class="btn-group btn-group-sm" role="group">
            <a href="/admin/penawaran{{ request('search') ? '?search=' . urlencode(request('search')) : '' }}" class="btn {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }}">
                Semua <span class="badge {{ !request('status') ? 'bg-secondary' : 'bg-light text-dark' }} ms-1">{{ $counts['total'] }}</span>
            </a>
            <a href="/admin/penawaran?status=pending{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}" class="btn {{ request('status') === 'pending' ? 'btn-dark' : 'btn-outline-secondary' }}">
                Menunggu <span class="badge {{ request('status') === 'pending' ? 'bg-warning text-dark' : 'bg-light text-dark' }} ms-1">{{ $counts['pending'] }}</span>
            </a>
            <a href="/admin/penawaran?status=approved{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}" class="btn {{ request('status') === 'approved' ? 'btn-dark' : 'btn-outline-secondary' }}">
                Disetujui <span class="badge {{ request('status') === 'approved' ? 'bg-success' : 'bg-light text-dark' }} ms-1">{{ $counts['approved'] }}</span>
            </a>
            <a href="/admin/penawaran?status=rejected{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}" class="btn {{ request('status') === 'rejected' ? 'btn-dark' : 'btn-outline-secondary' }}">
                Ditolak <span class="badge {{ request('status') === 'rejected' ? 'bg-danger' : 'bg-light text-dark' }} ms-1">{{ $counts['rejected'] }}</span>
            </a>
        </div>

        <!-- Search Input -->
        <form method="GET" action="/admin/penawaran" class="d-flex align-items-center gap-1">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="input-group input-group-sm" style="min-width: 240px;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama, WA, email, lokasi...">
                @if(request()->filled('search'))
                    <a href="/admin/penawaran{{ request('status') ? '?status=' . request('status') : '' }}" class="btn btn-outline-secondary" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
        <span class="fw-bold small text-muted text-uppercase" style="letter-spacing: 0.05em;">
            <i class="fas fa-file-invoice-dollar me-1 text-primary"></i> Daftar Penawaran Masuk (RFQ)
        </span>
        <span class="badge bg-light text-dark border font-monospace">{{ $penawarans->total() }} Data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Klien & Kontak</th>
                        <th>Estimasi Budget</th>
                        <th>Lokasi</th>
                        <th>Uraian Kebutuhan</th>
                        <th style="width: 70px;" class="text-center">Lampiran</th>
                        <th style="width: 100px;">Tanggal</th>
                        <th style="width: 100px;">Status</th>
                        <th class="text-end" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penawarans as $index => $item)
                        @php
                            $phone = preg_replace('/[^0-9]/', '', $item->telepon);
                            if (str_starts_with($phone, '0')) {
                                $phone = '62' . substr($phone, 1);
                            }
                            $waText = urlencode("Halo Bapak/Ibu {$item->nama}, kami dari PT Indo Berkah Konstruksi ingin menindaklanjuti permohonan penawaran yang Anda ajukan melalui website kami.");
                            $autoPassword = 'IBK' . ($item->created_at ? $item->created_at->format('dmy') : now()->format('dmy'));
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $penawarans->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->nama }}</div>
                                <div class="small text-muted">{{ $item->telepon }}</div>
                                @if($item->email)
                                    <div class="small text-muted">{{ $item->email }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->budget }}</span>
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 140px;" title="{{ $item->alamat }}">
                                    {{ $item->alamat ?: '-' }}
                                </small>
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 180px;" title="{{ strip_tags($item->deskripsi) }}">
                                    {{ strip_tags($item->deskripsi) }}
                                </small>
                            </td>
                            <!-- Lampiran Denah / Foto (Thumbnail yang Tampil Jelas) -->
                            <td class="text-center">
                                @if($item->foto)
                                    <a href="{{ asset('storage/' . $item->foto) }}" target="_blank" title="Klik untuk lihat gambar penuh">
                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="Lampiran" class="rounded border object-fit-cover shadow-sm" style="width: 44px; height: 44px;">
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                @if($item->status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Disetujui</span>
                                @elseif($item->status === 'rejected')
                                    <span class="badge bg-secondary-subtle text-secondary border">Ditolak</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Menunggu</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    @if($item->status !== 'approved')
                                        <!-- Tombol Approve Cepat -->
                                        <button type="button" class="btn btn-sm btn-success py-1 px-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#approveModal{{ $item->id }}" title="Approve & Buat Proyek">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                    @else
                                        <!-- Link ke Proyek -->
                                        <a href="/admin/projects" class="btn btn-sm btn-outline-primary py-1 px-2" title="Lihat di Menu Proyek">
                                            <i class="fas fa-folder me-1"></i> Proyek
                                        </a>
                                    @endif

                                    <!-- Tombol WhatsApp -->
                                    <a href="https://wa.me/{{ $phone }}?text={{ $waText }}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2" title="Hubungi via WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>

                                    <!-- Tombol Detail Modal -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}" title="Lihat Rincian">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form action="/admin/penawaran/{{ $item->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data penawaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal Approve & Buat Proyek -->
                                <div class="modal fade" id="approveModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-success text-white py-2 px-3">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="fas fa-check-circle me-1"></i> Approve Penawaran & Buat Proyek
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="/admin/penawaran/{{ $item->id }}/approve" method="POST">
                                                @csrf
                                                <div class="modal-body p-3">
                                                    <div class="alert alert-info py-2 px-3 small mb-3">
                                                        <i class="fas fa-info-circle me-1"></i> Sistem otomatis membuatkan <strong>Akun Klien</strong> dan mengonversi penawaran ini menjadi <strong>Proyek Aktif</strong>.
                                                    </div>

                                                    <table class="table table-sm table-borderless small mb-0">
                                                        <tr>
                                                            <td class="text-muted" style="width: 120px;">Nama Klien:</td>
                                                            <td class="fw-bold">{{ $item->nama }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">No. WhatsApp:</td>
                                                            <td>{{ $item->telepon }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Email Akun:</td>
                                                            <td class="text-primary">{{ $item->email ?: ($phone . '@klien.indoberkah.com') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Password Klien:</td>
                                                            <td>
                                                                <span class="badge bg-dark font-monospace">{{ $autoPassword }}</span>
                                                                <small class="text-muted ms-1">(Format: IBK + tgl_penawaran)</small>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Nama Proyek:</td>
                                                            <td class="fw-bold text-success">Proyek {{ $item->nama }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="modal-footer bg-light py-2 px-3">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-success fw-bold">
                                                        <i class="fas fa-check me-1"></i> Konfirmasi & Buat Proyek
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Detail Lengkap -->
                                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header py-2 px-3">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="fas fa-file-invoice me-1 text-primary"></i> Detail Penawaran: {{ $item->nama }}
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-3">
                                                <div class="row g-3">
                                                    <div class="col-md-5 border-end">
                                                        <dl class="row small mb-0">
                                                            <dt class="col-5 text-muted">Nama Klien</dt>
                                                            <dd class="col-7 fw-bold">{{ $item->nama }}</dd>

                                                            <dt class="col-5 text-muted">WhatsApp</dt>
                                                            <dd class="col-7">{{ $item->telepon }}</dd>

                                                            <dt class="col-5 text-muted">Email</dt>
                                                            <dd class="col-7">{{ $item->email ?: '-' }}</dd>

                                                            <dt class="col-5 text-muted">Budget</dt>
                                                            <dd class="col-7"><span class="badge bg-light text-dark border">{{ $item->budget }}</span></dd>

                                                            <dt class="col-5 text-muted">Lokasi</dt>
                                                            <dd class="col-7">{{ $item->alamat ?: '-' }}</dd>

                                                            <dt class="col-5 text-muted">Tanggal</dt>
                                                            <dd class="col-7">{{ $item->created_at->format('d/m/Y H:i') }} WIB</dd>

                                                            <dt class="col-5 text-muted">Status</dt>
                                                            <dd class="col-7">
                                                                @if($item->status === 'approved')
                                                                    <span class="badge bg-success">Disetujui</span>
                                                                @elseif($item->status === 'rejected')
                                                                    <span class="badge bg-danger">Ditolak</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                                                @endif
                                                            </dd>
                                                        </dl>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <h6 class="fw-bold text-dark small mb-1">Uraian Kebutuhan:</h6>
                                                        <div class="p-2 bg-light rounded border text-muted small mb-3" style="max-height: 140px; overflow-y: auto;">
                                                            {!! nl2br(e($item->deskripsi)) !!}
                                                        </div>

                                                        @if($item->foto)
                                                            <h6 class="fw-bold text-dark small mb-1">Lampiran Denah / Sketsa:</h6>
                                                            <div class="text-center p-2 bg-light rounded border">
                                                                <a href="{{ asset('storage/' . $item->foto) }}" target="_blank">
                                                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Lampiran" class="img-fluid rounded border shadow-sm" style="max-height: 200px;">
                                                                </a>
                                                                <div class="mt-1">
                                                                    <a href="{{ asset('storage/' . $item->foto) }}" target="_blank" class="small text-primary text-decoration-none">
                                                                        <i class="fas fa-external-link-alt me-1"></i> Buka Gambar Resolusi Penuh
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer py-2 px-3 d-flex justify-content-between">
                                                <div>
                                                    @if($item->status === 'pending')
                                                        <form action="/admin/penawaran/{{ $item->id }}/reject" method="POST" class="d-inline" onsubmit="return confirm('Tolak penawaran ini?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-times me-1"></i> Tolak
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    <a href="https://wa.me/{{ $phone }}?text={{ $waText }}" target="_blank" class="btn btn-sm btn-success">
                                                        <i class="fab fa-whatsapp me-1"></i> Chat WA
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <div class="mb-1"><i class="fas fa-inbox fs-4 text-secondary"></i></div>
                                <div>Tidak ada data penawaran yang sesuai.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($penawarans->hasPages())
        <div class="card-footer py-2 px-3">
            {{ $penawarans->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection

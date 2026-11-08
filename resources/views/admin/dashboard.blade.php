@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('page-actions')
    <a href="/admin/projects/create" class="btn btn-sm btn-primary fw-semibold px-3 shadow-xs">
        <i class="fas fa-plus me-1"></i> Tambah Proyek
    </a>
    <a href="/admin/penawaran" class="btn btn-sm btn-outline-secondary px-3">
        <i class="fas fa-file-invoice-dollar me-1"></i> Tinjau Penawaran
    </a>
@endsection

@section('content')

<style>
    /* Clean Minimalist Stat Card */
    .stat-card-clean {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .stat-card-clean:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px -4px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
        color: inherit;
    }
    .stat-icon-clean {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    /* Clean Table Card */
    .clean-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .clean-table-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 0.85rem 1.15rem;
    }
    .clean-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.725rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        white-space: nowrap;
    }
    .clean-table tbody td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    .clean-table tbody tr:hover {
        background-color: #fafbfd;
    }

    /* Progress Bar */
    .progress-slim {
        height: 5px;
        border-radius: 10px;
        background-color: #f1f5f9;
        overflow: hidden;
    }
</style>

<!-- 4 Clean Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Proyek Berjalan -->
    <div class="col-6 col-lg-3">
        <a href="/admin/projects" class="stat-card-clean">
            <div class="stat-icon-clean" style="background: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                <i class="fas fa-building"></i>
            </div>
            <div class="min-w-0">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Proyek Berjalan</div>
                <div class="fs-4 fw-bold text-dark lh-1 mt-1">{{ $activeProjects }}</div>
                <small class="text-muted" style="font-size: 0.725rem;">dari {{ $totalProjects }} Total Proyek</small>
            </div>
        </a>
    </div>

    <!-- Penawaran Masuk (RFQ) -->
    <div class="col-6 col-lg-3">
        <a href="/admin/penawaran" class="stat-card-clean">
            <div class="stat-icon-clean" style="background: rgba(197, 168, 128, 0.15); color: #926938;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="min-w-0">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Penawaran (RFQ)</div>
                <div class="fs-4 fw-bold text-dark lh-1 mt-1">{{ $totalPenawaran }}</div>
                <small class="text-muted" style="font-size: 0.725rem;">Permintaan Biaya</small>
            </div>
        </a>
    </div>

    <!-- Surat Perjanjian (SPK) -->
    <div class="col-6 col-lg-3">
        <a href="/admin/surat-perjanjian" class="stat-card-clean">
            <div class="stat-icon-clean" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="min-w-0">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Surat Kontrak (SPK)</div>
                <div class="fs-4 fw-bold text-dark lh-1 mt-1">{{ $totalSurat }}</div>
                <small class="text-muted" style="font-size: 0.725rem;">
                    @if($pendingSurat > 0)
                        <span class="text-danger fw-bold">{{ $pendingSurat }} Menunggu</span>
                    @else
                        Terdokumentasi
                    @endif
                </small>
            </div>
        </a>
    </div>

    <!-- Masukan Lapangan -->
    <div class="col-6 col-lg-3">
        <a href="/admin/feedbacks" class="stat-card-clean">
            <div class="stat-icon-clean" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                <i class="fas fa-comment-dots"></i>
            </div>
            <div class="min-w-0">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Masukan Lapangan</div>
                <div class="fs-4 fw-bold text-dark lh-1 mt-1">{{ $totalFeedbacks }}</div>
                <small class="text-muted" style="font-size: 0.725rem;">
                    @if($pendingFeedbacks > 0)
                        <span class="text-warning fw-bold">{{ $pendingFeedbacks }} Belum Direspon</span>
                    @else
                        Semua Terlayani
                    @endif
                </small>
            </div>
        </a>
    </div>
</div>

<!-- Main Split: Penawaran Masuk vs Proyek Berjalan -->
<div class="row g-3">
    <!-- Penawaran Masuk Terbaru (7 Kolom) -->
    <div class="col-lg-7">
        <div class="clean-table-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-dark">
                    <i class="fas fa-inbox me-1.5 text-primary"></i> Penawaran Masuk Terbaru
                </span>
                <a href="/admin/penawaran" class="small text-muted text-decoration-none hover-primary">
                    Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table clean-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Calon Klien</th>
                                <th>Estimasi Budget</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPenawaran as $item)
                                @php
                                    $phone = preg_replace('/[^0-9]/', '', $item->telepon);
                                    if (str_starts_with($phone, '0')) {
                                        $phone = '62' . substr($phone, 1);
                                    }
                                    $waMsg = urlencode("Halo Bapak/Ibu {$item->nama}, kami dari PT Indo Berkah Konstruksi ingin menindaklanjuti permohonan penawaran yang Anda ajukan.");
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->nama }}</div>
                                        <small class="text-muted">{{ $item->telepon }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $item->budget }}</span>
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
                                    <td>
                                        <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="/admin/penawaran" class="btn btn-sm btn-outline-primary py-0 px-2" title="Proses / Tinjau">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="https://wa.me/{{ $phone }}?text={{ $waMsg }}" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2" title="Chat WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        Belum ada data penawaran yang masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Proyek Berjalan (5 Kolom) -->
    <div class="col-lg-5">
        <div class="clean-table-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-dark">
                    <i class="fas fa-hard-hat me-1.5 text-warning"></i> Proyek Sedang Berjalan
                </span>
                <a href="/admin/projects" class="small text-muted text-decoration-none hover-primary">
                    Kelola Proyek <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table clean-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Proyek</th>
                                <th>Progres</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProjects as $project)
                                @php
                                    $pct = $project->progress_percentage ?? 0;
                                    $barColor = $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-warning');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 170px;" title="{{ $project->nama_proyek }}">
                                            {{ $project->nama_proyek }}
                                        </div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 170px;">
                                            {{ $project->user->name ?? 'Klien Umum' }}
                                        </small>
                                    </td>
                                    <td style="min-width: 110px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress progress-slim flex-grow-1">
                                                <div class="progress-bar {{ $barColor }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <small class="fw-semibold text-muted" style="font-size: 0.75rem;">{{ $pct }}%</small>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if($project->status === 'selesai')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Selesai</span>
                                        @elseif($project->status === 'aktif')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border">{{ ucfirst($project->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">
                                        Belum ada data proyek berjalan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

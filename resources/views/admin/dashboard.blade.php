@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('page-actions')
    <a href="/admin/projects/create" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i> Tambah Proyek
    </a>
    <a href="/admin/penawaran" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-file-invoice-dollar me-1"></i> Tinjau Penawaran
    </a>
@endsection

@section('content')

<style>
    /* Clean Minimalist Stat Card matching 16px reference standard */
    .stat-card-clean {
        background: #ffffff;
        border: 1px solid #eef2f6;
        border-radius: 16px;
        padding: 1.25rem 1.4rem;
        display: flex;
        align-items: center;
        gap: 1.15rem;
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .stat-card-clean:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
        color: inherit;
    }
    .stat-icon-clean {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
</style>

<!-- 4 Clean Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Proyek Berjalan -->
    <div class="col-6 col-lg-3">
        <a href="/admin/projects" class="stat-card-clean">
            <div class="stat-icon-clean" style="background: #eff6ff; color: #2563eb;">
                <i class="fas fa-building"></i>
            </div>
            <div class="min-w-0">
                <div class="form-label mb-1" style="font-size: 0.68rem;">Proyek Berjalan</div>
                <div class="fs-4 fw-bold text-dark lh-1">{{ $activeProjects }}</div>
                <small class="text-muted" style="font-size: 0.725rem;">dari {{ $totalProjects }} Total Proyek</small>
            </div>
        </a>
    </div>

    <!-- Penawaran Masuk (RFQ) -->
    <div class="col-6 col-lg-3">
        <a href="/admin/penawaran" class="stat-card-clean">
            <div class="stat-icon-clean" style="background: #fefce8; color: #ca8a04;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="min-w-0">
                <div class="form-label mb-1" style="font-size: 0.68rem;">Penawaran (RFQ)</div>
                <div class="fs-4 fw-bold text-dark lh-1">{{ $totalPenawaran }}</div>
                <small class="text-muted" style="font-size: 0.725rem;">Permintaan Biaya</small>
            </div>
        </a>
    </div>

    <!-- Surat Perjanjian (SPK) -->
    <div class="col-6 col-lg-3">
        <a href="/admin/surat-perjanjian" class="stat-card-clean">
            <div class="stat-icon-clean" style="background: #fff7ed; color: #ea580c;">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="min-w-0">
                <div class="form-label mb-1" style="font-size: 0.68rem;">Kontrak (SPK)</div>
                <div class="fs-4 fw-bold text-dark lh-1">{{ $totalSurat }}</div>
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
            <div class="stat-icon-clean" style="background: #ecfdf5; color: #059669;">
                <i class="fas fa-comment-dots"></i>
            </div>
            <div class="min-w-0">
                <div class="form-label mb-1" style="font-size: 0.68rem;">Masukan Lapangan</div>
                <div class="fs-4 fw-bold text-dark lh-1">{{ $totalFeedbacks }}</div>
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
        <div class="card h-100 mb-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark small">
                    <i class="fas fa-inbox me-1.5 text-primary"></i> Penawaran Masuk Terbaru
                </span>
                <a href="/admin/penawaran" class="small text-muted text-decoration-none hover-primary">
                    Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>CALON KLIEN</th>
                                <th>ESTIMASI BUDGET</th>
                                <th>STATUS</th>
                                <th>TANGGAL</th>
                                <th class="text-end">AKSI</th>
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
                                        <span class="badge-soft-neutral">{{ $item->budget }}</span>
                                    </td>
                                    <td>
                                        @if($item->status === 'approved')
                                            <span class="badge-soft-success">Disetujui</span>
                                        @elseif($item->status === 'rejected')
                                            <span class="badge-soft-danger">Ditolak</span>
                                        @else
                                            <span class="badge-soft-warning">Menunggu</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="/admin/penawaran" class="btn-action-icon" title="Proses / Tinjau">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="https://wa.me/{{ $phone }}?text={{ $waMsg }}" target="_blank" class="btn-action-icon" style="color: #25D366; border-color: #bbf7d0;" title="Chat WhatsApp">
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
        <div class="card h-100 mb-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark small">
                    <i class="fas fa-hard-hat me-1.5 text-warning"></i> Proyek Sedang Berjalan
                </span>
                <a href="/admin/projects" class="small text-muted text-decoration-none hover-primary">
                    Kelola Proyek <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>PROYEK</th>
                                <th>PROGRES</th>
                                <th class="text-end">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProjects as $project)
                                @php
                                    $pct = $project->progress_percentage ?? 0;
                                    $barClass = $pct >= 100 ? 'emerald' : ($pct >= 50 ? 'blue' : 'amber');
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
                                            <div class="progress-clean flex-grow-1">
                                                <div class="progress-clean-bar {{ $barClass }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <small class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $pct }}%</small>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if($project->status === 'selesai')
                                            <span class="badge-soft-success">Selesai</span>
                                        @elseif($project->status === 'aktif')
                                            <span class="badge-soft-primary">Aktif</span>
                                        @else
                                            <span class="badge-soft-neutral">{{ ucfirst($project->status) }}</span>
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

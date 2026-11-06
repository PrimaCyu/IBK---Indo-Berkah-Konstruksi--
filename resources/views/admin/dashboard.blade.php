@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

<!-- INFO BOXES (4 STATISTIK OPERASIONAL) -->
<div class="row">
    <!-- Proyek Aktif -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Proyek Aktif</span>
                <span class="info-box-number">{{ $activeProjects }} <small class="fw-normal text-muted" style="font-size: 0.8rem;">/ {{ $totalProjects }} Total</small></span>
            </div>
        </div>
    </div>

    <!-- Penawaran Masuk -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box">
            <span class="info-box-icon bg-info text-white"><i class="fas fa-file-invoice-dollar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Penawaran Masuk</span>
                <span class="info-box-number">{{ $totalPenawaran }}</span>
            </div>
        </div>
    </div>

    <!-- Surat Perjanjian -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning text-dark"><i class="fas fa-file-contract"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Surat Perjanjian</span>
                <span class="info-box-number">{{ $totalSurat }} @if($pendingSurat > 0)<small class="text-danger fw-normal" style="font-size: 0.8rem;">({{ $pendingSurat }} Pending)</small>@endif</span>
            </div>
        </div>
    </div>

    <!-- Masukan Lapangan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box">
            <span class="info-box-icon bg-secondary"><i class="fas fa-comment-dots"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Masukan Lapangan</span>
                <span class="info-box-number">{{ $totalFeedbacks }} @if($pendingFeedbacks > 0)<small class="text-warning fw-normal" style="font-size: 0.8rem;">({{ $pendingFeedbacks }} Baru)</small>@endif</span>
            </div>
        </div>
    </div>
</div>

<!-- DATA OPERASIONAL TERBARU -->
<div class="row">
    <!-- Tabel Penawaran Masuk Terbaru -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-inbox text-primary me-2"></i>Penawaran Masuk Terbaru</span>
                <a href="/admin/penawaran" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.8rem;">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Pengirim</th>
                                <th>Estimasi Budget</th>
                                <th>Alamat</th>
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
                                    $waMsg = urlencode("Halo Bapak/Ibu {$item->nama}, kami dari PT Indo Berkah Konstruksi ingin menindaklanjuti permohonan penawaran yang Anda kirimkan.");
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->nama }}</div>
                                        <small class="text-muted">{{ $item->telepon }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $item->budget }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 140px;">
                                            {{ $item->alamat ?? '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <a href="https://wa.me/{{ $phone }}?text={{ $waMsg }}" target="_blank" class="btn btn-sm btn-wa px-2 py-1" title="Hubungi via WhatsApp">
                                            <i class="fab fa-whatsapp me-1"></i> WA
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Belum ada data penawaran masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Proyek Berjalan -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-hard-hat text-warning me-2"></i>Proyek Berjalan</span>
                <a href="/admin/projects" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.8rem;">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama Proyek</th>
                                <th>Klien</th>
                                <th>Progres</th>
                                <th>Status</th>
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
                                        <div class="fw-semibold text-truncate" style="max-width: 140px;" title="{{ $project->nama_proyek }}">
                                            {{ $project->nama_proyek }}
                                        </div>
                                        <small class="text-muted">{{ $project->kategori }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $project->user->name ?? '-' }}</small>
                                    </td>
                                    <td style="min-width: 100px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar {{ $barColor }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <small class="fw-bold">{{ $pct }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($project->status === 'selesai')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($project->status === 'aktif')
                                            <span class="badge bg-primary">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($project->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Belum ada data proyek.
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

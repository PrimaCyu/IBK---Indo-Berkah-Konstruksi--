@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Operasional')
@section('page-subtitle', 'Ringkasan performa proyek, penawaran masuk, dan aktivitas konstruksi')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

<!-- ==============================================
     1. WELCOME HERO BANNER
     ============================================== -->
<div class="card border-0 mb-4 overflow-hidden position-relative shadow-sm" style="background: linear-gradient(135deg, #111111 0%, #242424 60%, #362d22 100%);">
    <div class="card-body p-4 p-md-5 position-relative z-1 text-white">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge mb-3 px-3 py-2 text-uppercase fw-bold" style="background-color: rgba(197, 168, 128, 0.2); color: #C5A880; border: 1px solid rgba(197, 168, 128, 0.4); letter-spacing: 1.5px; font-size: 0.75rem;">
                    <i class="bi bi-shield-check me-1"></i> PANEL KONTROL EKSEKUTIF IBK
                </span>
                <h2 class="fw-bold display-6 mb-2">Selamat Datang, {{ auth()->user()->name ?? 'Administrator' }}!</h2>
                <p class="text-white-50 lead fs-6 mb-4 max-w-2xl" style="max-width: 600px;">
                    Kelola seluruh proses konstruksi, verifikasi surat perjanjian, pantau progres tukang di lapangan, dan respon penawaran harga dari calon klien secara cepat.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/admin/penawaran" class="btn btn-warning px-4 py-2 fw-semibold rounded-pill shadow-sm" style="background: #C5A880; border-color: #C5A880; color: #111;">
                        <i class="bi bi-inbox-fill me-1"></i> Cek Penawaran ({{ $totalPenawaran }})
                    </a>
                    <a href="/admin/projects" class="btn btn-outline-light px-4 py-2 fw-semibold rounded-pill">
                        <i class="bi bi-building me-1"></i> Kelola Proyek
                    </a>
                    <a href="/admin/surat-perjanjian" class="btn btn-outline-warning px-4 py-2 fw-semibold rounded-pill" style="border-color: #C5A880; color: #C5A880;">
                        <i class="bi bi-file-earmark-check me-1"></i> SPK Pending ({{ $pendingSurat }})
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-end pe-4">
                <div class="d-inline-flex flex-column align-items-center justify-content-center p-4 rounded-4 shadow" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                    <i class="bi bi-buildings-fill display-3 mb-2" style="color: #C5A880;"></i>
                    <div class="h5 fw-bold mb-0">PT INDO BERKAH</div>
                    <small class="text-warning text-uppercase" style="letter-spacing: 2px; font-size: 0.7rem;">Konstruksi Terpercaya</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     2. STATS OVERVIEW CARDS (4 INFO BOXES)
     ============================================== -->
<div class="row g-3 mb-4">
    <!-- Card 1: Proyek Berjalan -->
    <div class="col-sm-6 col-xl-3">
        <div class="ibk-stat-card bg-grad-gold shadow-sm">
            <i class="bi bi-buildings stat-icon"></i>
            <div class="stat-title">Proyek Aktif</div>
            <div class="stat-value">{{ $activeProjects }}</div>
            <div class="stat-desc d-flex align-items-center justify-content-between">
                <span>Dari {{ $totalProjects }} total proyek</span>
                <span class="badge bg-white text-dark rounded-pill px-2">{{ $completedProjects }} Selesai</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Penawaran Masuk -->
    <div class="col-sm-6 col-xl-3">
        <div class="ibk-stat-card bg-grad-blue shadow-sm">
            <i class="bi bi-cash-stack stat-icon"></i>
            <div class="stat-title">Penawaran (RFQ)</div>
            <div class="stat-value">{{ $totalPenawaran }}</div>
            <div class="stat-desc d-flex align-items-center justify-content-between">
                <span>Calon klien menunggu penawaran</span>
                <a href="/admin/penawaran" class="text-white text-decoration-none fw-bold">
                    Lihat <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 3: Surat Perjanjian (SPK) -->
    <div class="col-sm-6 col-xl-3">
        <div class="ibk-stat-card bg-grad-green shadow-sm">
            <i class="bi bi-file-earmark-check stat-icon"></i>
            <div class="stat-title">Surat Perjanjian</div>
            <div class="stat-value">{{ $totalSurat }}</div>
            <div class="stat-desc d-flex align-items-center justify-content-between">
                <span>Dokumen kontrak masuk</span>
                @if($pendingSurat > 0)
                    <span class="badge bg-warning text-dark rounded-pill">{{ $pendingSurat }} Pending</span>
                @else
                    <span class="badge bg-white text-success rounded-pill">Aman</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Card 4: Klien Terdaftar -->
    <div class="col-sm-6 col-xl-3">
        <div class="ibk-stat-card bg-grad-purple shadow-sm">
            <i class="bi bi-people stat-icon"></i>
            <div class="stat-title">Klien Terdaftar</div>
            <div class="stat-value">{{ $totalClients }}</div>
            <div class="stat-desc d-flex align-items-center justify-content-between">
                <span>Pengguna portal dashboard</span>
                <span class="badge bg-white text-dark rounded-pill">{{ $totalFeedbacks }} Catatan</span>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     3. CHARTS & CATEGORY DISTRIBUTION
     ============================================== -->
<div class="row g-3 mb-4">
    <!-- Left Column: Visualisasi Kategori Proyek & Status -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-fill text-warning fs-5"></i>
                    <h5 class="card-title mb-0 fw-bold">Distribusi Kategori Layanan Konstruksi</h5>
                </div>
                <span class="badge badge-gold px-3 py-2 rounded-pill">Data Realtime</span>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div id="categoryChart" style="min-height: 280px;"></div>
                    </div>
                    <div class="col-md-5">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3">Rincian Per Kategori:</h6>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span><i class="bi bi-house-door-fill text-warning me-1"></i> Pembangunan Rumah</span>
                                <span>{{ $categoriesCount['Pembangunan Rumah'] }} Proyek</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: {{ $totalProjects > 0 ? ($categoriesCount['Pembangunan Rumah'] / $totalProjects * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span><i class="bi bi-buildings-fill text-primary me-1"></i> Gedung Komersial</span>
                                <span>{{ $categoriesCount['Gedung Komersial'] }} Proyek</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: {{ $totalProjects > 0 ? ($categoriesCount['Gedung Komersial'] / $totalProjects * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span><i class="bi bi-tools text-success me-1"></i> Renovasi Bangunan</span>
                                <span>{{ $categoriesCount['Renovasi Bangunan'] }} Proyek</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $totalProjects > 0 ? ($categoriesCount['Renovasi Bangunan'] / $totalProjects * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span><i class="bi bi-clipboard2-pulse-fill text-info me-1"></i> Konsultasi Konstruksi</span>
                                <span>{{ $categoriesCount['Konsultasi Konstruksi'] }} Proyek</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: {{ $totalProjects > 0 ? ($categoriesCount['Konsultasi Konstruksi'] / $totalProjects * 100) : 0 }}%"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Masukan Lapangan Klien Terbaru -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-chat-quote-fill text-danger fs-5"></i>
                    <h5 class="card-title mb-0 fw-bold">Masukan Lapangan</h5>
                </div>
                <a href="/admin/feedbacks" class="small text-decoration-none fw-bold">Semua <i class="bi bi-chevron-right"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentFeedbacks as $fb)
                        <div class="list-group-item p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-bold text-dark text-truncate" style="max-width: 170px;">
                                    {{ $fb->user->name ?? 'Klien' }}
                                </span>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    {{ $fb->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <small class="badge bg-light text-secondary border mb-2 d-inline-block">
                                <i class="bi bi-building me-1"></i> {{ $fb->project->nama_proyek ?? 'Proyek Umum' }}
                            </small>
                            <p class="mb-0 text-muted small text-truncate" style="max-height: 40px; overflow: hidden;">
                                {{ strip_tags($fb->content) }}
                            </p>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-chat-check fs-2 text-success d-block mb-2"></i>
                            <p class="mb-0 small">Belum ada catatan atau masukan dari klien.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     4. TABLES ROW (PENAWARAN TERBARU & PROYEK AKTIF)
     ============================================== -->
<div class="row g-3">
    <!-- Table 1: Penawaran Masuk (RFQ) -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-envelope-paper-fill text-primary fs-5"></i>
                    <h5 class="card-title mb-0 fw-bold">Penawaran Masuk Terbaru (Leads)</h5>
                </div>
                <a href="/admin/penawaran" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    Lihat Semua ({{ $totalPenawaran }})
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Pengirim</th>
                                <th>Budget</th>
                                <th>Alamat / Lokasi</th>
                                <th>Waktu</th>
                                <th class="text-end">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPenawaran as $penawaran)
                                @php
                                    // Format no WhatsApp: ganti awalan 08 / +62 jadi 62
                                    $phone = preg_replace('/[^0-9]/', '', $penawaran->telepon);
                                    if (str_starts_with($phone, '0')) {
                                        $phone = '62' . substr($phone, 1);
                                    }
                                    $waMessage = urlencode("Halo Bapak/Ibu " . $penawaran->nama . ", terima kasih telah menghubungi PT Indo Berkah Konstruksi terkait rencana pembangunan proyek Anda. Kami ingin mendiskusikan penawaran RAB lebih lanjut.");
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $penawaran->nama }}</div>
                                        <small class="text-muted"><i class="bi bi-telephone"></i> {{ $penawaran->telepon }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-bold">
                                            {{ $penawaran->budget }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-secondary text-truncate d-inline-block" style="max-width: 130px;" title="{{ $penawaran->alamat }}">
                                            {{ $penawaran->alamat ?? '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $penawaran->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-end">
                                        <a href="https://wa.me/{{ $phone }}?text={{ $waMessage }}" target="_blank" 
                                           class="btn btn-sm btn-whatsapp rounded-pill px-3 py-1 shadow-sm" title="Chat via WhatsApp">
                                            <i class="bi bi-whatsapp"></i> Chat WA
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-1"></i>
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

    <!-- Table 2: Status Proyek Terbaru -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-kanban-fill text-success fs-5"></i>
                    <h5 class="card-title mb-0 fw-bold">Proyek Berjalan</h5>
                </div>
                <a href="/admin/projects" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    Semua Proyek
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Proyek</th>
                                <th>Klien</th>
                                <th>Progres Fisik</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProjects as $project)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 140px;">
                                            {{ $project->nama_proyek }}
                                        </div>
                                        <small class="badge bg-light text-secondary border">
                                            {{ $project->kategori }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="fw-semibold text-secondary">
                                            {{ $project->user->name ?? '-' }}
                                        </small>
                                    </td>
                                    <td style="min-width: 100px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                @php
                                                    $pct = $project->progress_percentage ?? 0;
                                                    $bgClass = $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-warning');
                                                @endphp
                                                <div class="progress-bar {{ $bgClass }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="small fw-bold">{{ $pct }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($project->status === 'selesai')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Selesai</span>
                                        @elseif($project->status === 'aktif')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">{{ ucfirst($project->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-buildings fs-2 d-block mb-1"></i>
                                        Belum ada proyek yang terdaftar.
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

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Render Donut Chart for Categories
        var options = {
            series: [
                {{ $categoriesCount['Pembangunan Rumah'] }},
                {{ $categoriesCount['Gedung Komersial'] }},
                {{ $categoriesCount['Renovasi Bangunan'] }},
                {{ $categoriesCount['Konsultasi Konstruksi'] }}
            ],
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Source Sans 3, sans-serif'
            },
            labels: ['Pembangunan Rumah', 'Gedung Komersial', 'Renovasi Bangunan', 'Konsultasi'],
            colors: ['#C5A880', '#2563eb', '#10b981', '#06b6d4'],
            legend: {
                position: 'bottom',
                fontSize: '12px'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Proyek',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 2,
                colors: ['#ffffff']
            }
        };

        var chart = new ApexCharts(document.querySelector("#categoryChart"), options);
        chart.render();
    });
</script>
@endpush


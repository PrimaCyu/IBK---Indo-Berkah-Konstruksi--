@extends('admin.layouts.app')

@section('title', 'Keuangan Proyek & Rekapan Pendapatan')
@section('page-title', 'Keuangan Proyek')

@php
    $formatRupiah = static function (string $value): string {
        $negative = str_starts_with($value, '-');
        [$whole, $fraction] = array_pad(explode('.', ltrim($value, '-'), 2), 2, '');
        $whole = ltrim($whole, '0') ?: '0';
        $fraction = str_pad(substr($fraction, 0, 2), 2, '0');
        $sign = $negative && ($whole !== '0' || trim($fraction, '0') !== '') ? '− ' : '';
        $formattedWhole = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $whole);
        $decimalPart = $fraction === '00' ? '' : ',' . $fraction;

        return $sign . 'Rp ' . $formattedWhole . $decimalPart;
    };

    $formatShortRupiah = static function (string $value): string {
        [$whole] = explode('.', ltrim($value, '-'), 2);
        $whole = ltrim($whole, '0') ?: '0';
        $formatted = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $whole);
        return 'Rp ' . $formatted;
    };

    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $monthShorts = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];

    $periodTitle = $selectedMonth
        ? ($months[$selectedMonth] ?? 'Bulan ' . $selectedMonth) . ' ' . $selectedYear
        : 'Tahun ' . $selectedYear . ' (Semua Bulan)';
@endphp

@section('page-actions')
    <div class="d-flex align-items-center gap-2">
        @if(($totalPendingCount ?? 0) > 0)
            <span class="badge-soft-warning px-3 py-1.5 font-monospace d-inline-flex align-items-center gap-1.5">
                <i class="fas fa-clock text-warning"></i>
                <span><strong>{{ $totalPendingCount }}</strong> Transaksi Pending</span>
            </span>
        @endif
        <span class="badge-soft-primary px-3 py-1.5 font-monospace">
            <i class="fas fa-calendar-alt me-1" aria-hidden="true"></i> {{ $periodTitle }}
        </span>
    </div>
@endsection

@section('content')

<style>
    /* Segmented Tab Bar */
    .segmented-nav-wrapper {
        background: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        display: inline-flex;
        gap: 4px;
    }
    .segmented-nav-wrapper .nav-link {
        border-radius: 9px;
        border: none;
        padding: 7px 18px;
        font-size: 0.825rem;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        transition: all 0.15s ease-in-out;
    }
    .segmented-nav-wrapper .nav-link:hover {
        color: #0f172a;
    }
    .segmented-nav-wrapper .nav-link.active {
        background: #ffffff;
        color: #0f172a;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    /* Analytics Inline Progress Row (Reference Match) */
    .analytics-progress-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 7px 0;
    }
    .analytics-progress-row:not(:last-child) {
        border-bottom: 1px solid #f8fafc;
    }
    .analytics-label {
        width: 170px;
        flex-shrink: 0;
        font-size: 0.825rem;
        font-weight: 500;
        color: #334155;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    @media (max-width: 576px) {
        .analytics-label {
            width: 110px;
            font-size: 0.775rem;
        }
        .analytics-amount {
            width: 95px !important;
            font-size: 0.775rem !important;
        }
    }
    .analytics-bar-wrap {
        flex-grow: 1;
        min-width: 80px;
    }
    .analytics-amount {
        width: 130px;
        flex-shrink: 0;
        text-align: right;
        font-size: 0.825rem;
        font-weight: 700;
        color: #0f172a;
        font-variant-numeric: tabular-nums;
    }

    /* Client Avatar Circle */
    .client-avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #f1f5f9;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
</style>

{{-- Slim Pending Notification Banner --}}
@if(($totalPendingCount ?? 0) > 0)
    <div class="card mb-3 border-0" style="background: linear-gradient(90deg, #FEF3C7 0%, #FFFBEB 100%); border-left: 4px solid #F59E0B !important;">
        <div class="card-body py-2.5 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 30px; height: 30px; background-color: #FDE68A; color: #B45309; flex-shrink: 0;">
                    <i class="fas fa-clock small"></i>
                </div>
                <div class="small text-dark">
                    <strong>Persetujuan Transaksi Diperlukan:</strong> Terdapat <strong>{{ $totalPendingCount }} transaksi</strong> kas berstatus <strong>Pending</strong> yang diajukan oleh accounting.
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-xs btn-warning text-dark fw-bold px-3 py-1 shadow-none" style="background-color: #F59E0B; border: none; font-size: 0.775rem;" onclick="document.getElementById('project-list-tab').click(); document.getElementById('project-list').scrollIntoView({behavior: 'smooth'});">
                    <i class="fas fa-arrow-down me-1"></i> Tinjau Proyek Terkait
                </button>
            </div>
        </div>
    </div>
@endif

{{-- 1. Summary Stat Cards (Top Executive Overview - 3 Balanced Cards) --}}
<div class="row g-3 mb-3" aria-label="Ringkasan keuangan semua proyek">
    <div class="col-md-4">
        <div class="card h-100 mb-0">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="form-label mb-0">TOTAL PENERIMAAN (LUNAS)</span>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; background-color: #ECFDF5; color: #10B981;">
                        <i class="fas fa-arrow-down small"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-success cashbook-number mb-1">+ {{ $formatRupiah($grandPeriodIncome) }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted" style="font-size: 0.75rem;">Periode: {{ $periodTitle }}</small>
                    <span class="badge-soft-success font-monospace" style="font-size: 0.7rem;">Masuk</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 mb-0">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="form-label mb-0">TOTAL PENGELUARAN (LUNAS)</span>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; background-color: #FEF2F2; color: #EF4444;">
                        <i class="fas fa-arrow-up small"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-danger cashbook-number mb-1">− {{ $formatRupiah($grandPeriodExpense) }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted" style="font-size: 0.75rem;">Rasio Beban: <strong>{{ $expenseRatio }}%</strong></small>
                    <span class="badge-soft-danger font-monospace" style="font-size: 0.7rem;">Keluar</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 mb-0">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="form-label mb-0">SELISIH KAS / SALDO BERSIH</span>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; background-color: #EFF6FF; color: #2563EB;">
                        <i class="fas fa-wallet small"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-dark cashbook-number mb-1">{{ $formatRupiah($grandPeriodBalance) }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Margin Laba: <strong>{{ (float)$grandPeriodIncome > 0 ? round(((float)$grandPeriodBalance / (float)$grandPeriodIncome) * 100, 1) : 0 }}%</strong>
                    </small>
                    <span class="badge-soft-primary font-monospace" style="font-size: 0.7rem;">Saldo Kas</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. Filter Bar: 3 Pilihan Periode Sederhana (Lengkap, Bulan, Tahun) --}}
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.cashbook.index') }}" id="cashbook-filter-form" class="row g-2 align-items-center">
            <input type="hidden" name="period_mode" id="input-period-mode" value="{{ $activeMode ?? 'bulan' }}">

            {{-- 3 Pilihan Periode: Lengkap, Bulan, Tahun --}}
            <div class="col-12 col-md-auto d-flex align-items-center gap-2">
                <span class="small fw-bold text-muted text-uppercase d-none d-lg-inline" style="font-size: 0.72rem; letter-spacing: 0.5px;">Periode:</span>
                <div class="btn-group btn-group-sm period-segmented-control" role="group" aria-label="Pilihan Periode Waktu">
                    <button type="button" class="btn btn-sm {{ ($activeMode ?? 'bulan') === 'lengkap' ? 'btn-primary active text-white' : 'btn-outline-secondary bg-white text-dark' }}" id="btn-period-lengkap" onclick="setPeriodMode('lengkap')">
                        <i class="fas fa-calendar-week me-1"></i> Lengkap
                    </button>
                    <button type="button" class="btn btn-sm {{ ($activeMode ?? 'bulan') === 'bulan' ? 'btn-primary active text-white' : 'btn-outline-secondary bg-white text-dark' }}" id="btn-period-bulan" onclick="setPeriodMode('bulan')">
                        <i class="fas fa-calendar-day me-1"></i> Bulan
                    </button>
                    <button type="button" class="btn btn-sm {{ ($activeMode ?? 'bulan') === 'tahun' ? 'btn-primary active text-white' : 'btn-outline-secondary bg-white text-dark' }}" id="btn-period-tahun" onclick="setPeriodMode('tahun')">
                        <i class="fas fa-calendar-alt me-1"></i> Tahun
                    </button>
                </div>
            </div>

            {{-- Dynamic Inputs Bulan & Tahun --}}
            <div class="col-6 col-sm-auto" id="filter-col-month">
                <select id="filter-month" name="month" class="form-select form-select-sm" style="min-width: 135px;">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" @selected((int)$selectedMonth === (int)$num)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-sm-auto" id="filter-col-year">
                <select id="filter-year" name="year" class="form-select form-select-sm" style="min-width: 95px;">
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" @selected((int)$selectedYear === (int)$yr)>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dynamic Inputs Rentang Tanggal (Lengkap) --}}
            <div class="col-6 col-sm-auto d-none" id="filter-col-start-date">
                <input type="date" id="filter-start-date" name="start_date" value="{{ $startDate ? (is_string($startDate) ? $startDate : $startDate->format('Y-m-d')) : '' }}" class="form-control form-control-sm" style="width: 140px;" disabled>
            </div>
            <div class="col-6 col-sm-auto d-none" id="filter-col-end-date">
                <input type="date" id="filter-end-date" name="end_date" value="{{ $endDate ? (is_string($endDate) ? $endDate : $endDate->format('Y-m-d')) : '' }}" class="form-control form-control-sm" style="width: 140px;" disabled>
            </div>

            {{-- Cari Proyek / Klien --}}
            <div class="col-12 col-md">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="search" id="cashbook-search" name="search" value="{{ $search }}" class="form-control form-control-sm border-start-0" placeholder="Cari nama proyek, alamat, atau klien...">
                </div>
            </div>

            {{-- Tombol Terapkan & Reset --}}
            <div class="col-auto d-flex align-items-center gap-1.5">
                <button type="submit" class="btn btn-sm btn-primary px-3 text-nowrap d-flex align-items-center gap-1.5" style="height: 31px;">
                    <i class="fas fa-filter"></i> Terapkan
                </button>
                @if(request()->hasAny(['month', 'year', 'search', 'start_date', 'end_date', 'period_mode']))
                    <a href="{{ route('admin.cashbook.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center" title="Reset filter" style="height: 31px; width: 31px; padding: 0;">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    function setPeriodMode(mode) {
        const inputMode = document.getElementById('input-period-mode');
        if (inputMode) inputMode.value = mode;

        const btnLengkap = document.getElementById('btn-period-lengkap');
        const btnBulan = document.getElementById('btn-period-bulan');
        const btnTahun = document.getElementById('btn-period-tahun');

        [btnLengkap, btnBulan, btnTahun].forEach(b => {
            if (!b) return;
            b.classList.remove('btn-primary', 'active', 'text-white');
            b.classList.add('btn-outline-secondary', 'bg-white', 'text-dark');
        });

        const activeBtn = mode === 'lengkap' ? btnLengkap : (mode === 'tahun' ? btnTahun : btnBulan);
        if (activeBtn) {
            activeBtn.classList.remove('btn-outline-secondary', 'bg-white', 'text-dark');
            activeBtn.classList.add('btn-primary', 'active', 'text-white');
        }

        const colMonth = document.getElementById('filter-col-month');
        const colYear = document.getElementById('filter-col-year');
        const colStartDate = document.getElementById('filter-col-start-date');
        const colEndDate = document.getElementById('filter-col-end-date');

        const inputMonth = document.getElementById('filter-month');
        const inputYear = document.getElementById('filter-year');
        const inputStartDate = document.getElementById('filter-start-date');
        const inputEndDate = document.getElementById('filter-end-date');

        if (mode === 'lengkap') {
            if (colMonth) colMonth.classList.add('d-none');
            if (colYear) colYear.classList.add('d-none');
            if (inputMonth) inputMonth.disabled = true;
            if (inputYear) inputYear.disabled = true;

            if (colStartDate) colStartDate.classList.remove('d-none');
            if (colEndDate) colEndDate.classList.remove('d-none');
            if (inputStartDate) inputStartDate.disabled = false;
            if (inputEndDate) inputEndDate.disabled = false;
        } else if (mode === 'tahun') {
            if (colMonth) colMonth.classList.add('d-none');
            if (inputMonth) inputMonth.disabled = true;

            if (colYear) colYear.classList.remove('d-none');
            if (inputYear) inputYear.disabled = false;

            if (colStartDate) colStartDate.classList.add('d-none');
            if (colEndDate) colEndDate.classList.add('d-none');
            if (inputStartDate) inputStartDate.disabled = true;
            if (inputEndDate) inputEndDate.disabled = true;
        } else {
            // Mode 'bulan' (Bulan + Tahun)
            if (colMonth) colMonth.classList.remove('d-none');
            if (colYear) colYear.classList.remove('d-none');
            if (inputMonth) inputMonth.disabled = false;
            if (inputYear) inputYear.disabled = false;

            if (colStartDate) colStartDate.classList.add('d-none');
            if (colEndDate) colEndDate.classList.add('d-none');
            if (inputStartDate) inputStartDate.disabled = true;
            if (inputEndDate) inputEndDate.disabled = true;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const initialMode = document.getElementById('input-period-mode')?.value || 'bulan';
        setPeriodMode(initialMode);
    });
</script>

{{-- 3. Analytics Progress Bars (Horizontal Inline Row - 100% Reference Match) --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100 mb-0">
            <div class="card-header py-2.5 px-3.5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chart-bar text-success" aria-hidden="true"></i>
                    <span class="fw-bold text-dark" style="font-size: 0.875rem;">Pemasukan per Kategori Layanan (Lunas)</span>
                </div>
                <small class="text-muted" style="font-size: 0.75rem;">{{ $periodTitle }}</small>
            </div>
            <div class="card-body p-3.5">
                @forelse($incomeBreakdown as $ib)
                    <div class="analytics-progress-row">
                        <div class="analytics-label" title="{{ $ib['name'] }}">
                            {{ $ib['name'] }}
                        </div>
                        <div class="analytics-bar-wrap">
                            <div class="progress" style="height: 6px; background-color: #F1F5F9; border-radius: 9999px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $ib['percent'] }}%; border-radius: 9999px;" aria-valuenow="{{ $ib['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="analytics-amount text-success">
                            {{ $formatShortRupiah($ib['amount']) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="fas fa-inbox fa-2x mb-1 text-secondary opacity-50 d-block"></i>
                        <div>Belum ada transaksi penerimaan lunas pada periode ini.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100 mb-0">
            <div class="card-header py-2.5 px-3.5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-pie-chart text-warning" aria-hidden="true"></i>
                    <span class="fw-bold text-dark" style="font-size: 0.875rem;">Pengeluaran per Kategori (Lunas)</span>
                </div>
                <small class="text-muted" style="font-size: 0.75rem;">{{ $periodTitle }}</small>
            </div>
            <div class="card-body p-3.5">
                @forelse($expenseBreakdown as $eb)
                    <div class="analytics-progress-row">
                        <div class="analytics-label" title="{{ $eb['name'] }}">
                            {{ $eb['name'] }}
                        </div>
                        <div class="analytics-bar-wrap">
                            <div class="progress" style="height: 6px; background-color: #F1F5F9; border-radius: 9999px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $eb['percent'] }}%; border-radius: 9999px; background-color: #F59E0B !important;" aria-valuenow="{{ $eb['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="analytics-amount text-danger">
                            {{ $formatShortRupiah($eb['amount']) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="fas fa-inbox fa-2x mb-1 text-secondary opacity-50 d-block"></i>
                        <div>Belum ada transaksi pengeluaran lunas pada periode ini.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- 4. Segmented Tab Bar --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div class="segmented-nav-wrapper" role="tablist">
        <button class="nav-link active" id="project-list-tab" data-bs-toggle="tab" data-bs-target="#project-list" type="button" role="tab" aria-controls="project-list" aria-selected="true">
            <i class="fas fa-layer-group me-1.5 text-primary"></i> Rincian & Saldo Kas Per Proyek
        </button>
        <button class="nav-link" id="monthly-recap-tab" data-bs-toggle="tab" data-bs-target="#monthly-recap" type="button" role="tab" aria-controls="monthly-recap" aria-selected="false">
            <i class="fas fa-chart-line me-1.5 text-primary"></i> Rekapan Pendapatan Bulanan (Semua Proyek)
        </button>
    </div>
    <div class="text-muted small">
        Total: <strong>{{ count($projectsData) }}</strong> Proyek
    </div>
</div>

<div class="tab-content" id="cashbook-tab-content">
    {{-- TAB 1: Daftar & Saldo Kas Lengkap Per Proyek (Default Active) --}}
    <div class="tab-pane fade show active" id="project-list" role="tabpanel" aria-labelledby="project-list-tab">
        <div class="card">
            <div class="card-header py-2.5 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h6 fw-bold mb-0 text-dark">Daftar Buku Kas Proyek</h2>
                    <span class="badge-soft-neutral">{{ count($projectsData) }} Proyek Terdaftar</span>
                </div>
                <div class="small text-muted">
                    Periode: <strong>{{ $periodTitle }}</strong>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" tabindex="0" role="region" aria-label="Daftar proyek dan rincian kas">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 45px;" class="text-center">NO</th>
                                <th scope="col" style="min-width: 200px;">PROYEK & LOKASI</th>
                                <th scope="col" style="min-width: 150px;">KLIEN</th>
                                <th scope="col" class="text-center" style="width: 110px;">STATUS PROYEK</th>
                                <th scope="col" class="text-end text-success" style="min-width: 130px;">PENERIMAAN</th>
                                <th scope="col" class="text-end text-danger" style="min-width: 130px;">PENGELUARAN</th>
                                <th scope="col" class="text-end text-primary" style="min-width: 140px;">TOTAL SALDO</th>
                                <th scope="col" class="text-center" style="width: 130px;">VERIFIKASI KAS</th>
                                <th scope="col" class="text-end" style="width: 110px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projectsData as $index => $item)
                                @php
                                    $project = $item['project'];
                                    $hasIncome = $item['period_income'] !== '0.00' && $item['period_income'] !== '0';
                                    $hasExpense = $item['period_expense'] !== '0.00' && $item['period_expense'] !== '0';
                                    $clientName = $project->user->name ?? 'Klien';
                                    $clientInitials = collect(explode(' ', $clientName))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
                                @endphp
                                <tr>
                                    <td class="text-center text-muted small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 260px; font-size: 0.875rem;">{{ $project->nama_proyek }}</div>
                                        <div class="small text-muted text-truncate" style="max-width: 260px;">
                                            <i class="fas fa-map-marker-alt text-secondary opacity-75 me-1"></i>{{ strip_tags((string) $project->alamat_proyek) ?: 'Lokasi belum diisi' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="client-avatar-circle" title="{{ $clientName }}">
                                                {{ strtoupper($clientInitials) }}
                                            </div>
                                            <div class="text-truncate" style="max-width: 140px;">
                                                <div class="text-dark small fw-semibold text-truncate">{{ $clientName }}</div>
                                                <small class="text-muted d-block text-truncate">{{ $project->user->email ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($project->status === 'selesai')
                                            <span class="badge-soft-success">Selesai</span>
                                        @elseif($project->status === 'aktif')
                                            <span class="badge-soft-primary">Aktif</span>
                                        @else
                                            <span class="badge-soft-neutral">{{ ucfirst($project->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($hasIncome)
                                            <span class="fw-semibold text-success font-monospace" style="font-size: 0.85rem;">+ {{ $formatRupiah($item['period_income']) }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($hasExpense)
                                            <span class="fw-semibold text-danger font-monospace" style="font-size: 0.85rem;">− {{ $formatRupiah($item['period_expense']) }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-primary font-monospace" style="font-size: 0.9rem;">
                                            {{ $formatRupiah($item['lifetime_balance']) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if(($item['pending_count'] ?? 0) > 0)
                                            <span class="badge-soft-warning font-monospace" title="{{ $item['pending_count'] }} transaksi kas menunggu persetujuan manager">
                                                <i class="fas fa-clock me-1 text-warning"></i>{{ $item['pending_count'] }} Pending
                                            </span>
                                        @else
                                            <span class="badge-soft-success font-monospace" title="Semua transaksi kas telah diverifikasi lunas">
                                                <i class="fas fa-check-circle me-1 text-success"></i>Lunas
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.cashbook.show', $project) }}" class="btn btn-sm btn-outline-primary text-nowrap py-1 px-2.5" style="font-size: 0.8rem;" aria-label="Buka buku kas {{ $project->nama_proyek }}">
                                            <i class="fas fa-book-open me-1" aria-hidden="true"></i> Buku Kas
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <p class="fw-semibold mb-1 text-dark">Tidak ada proyek yang sesuai kriteria.</p>
                                        <p class="small text-muted mb-3">Coba kata kunci lain atau reset pencarian.</p>
                                        <a href="{{ route('admin.cashbook.index') }}" class="btn btn-sm btn-outline-secondary">Reset filter</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: Matriks Rekapan Pendapatan Bulanan Semua Proyek --}}
    <div class="tab-pane fade" id="monthly-recap" role="tabpanel" aria-labelledby="monthly-recap-tab">
        <div class="card">
            <div class="card-header py-2.5 px-3.5 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h6 fw-bold mb-0 text-dark">Rekapan Pendapatan Bulanan (Semua Proyek) Tahun {{ $selectedYear }}</h2>
                    <span class="badge-soft-neutral">{{ count($monthlyMatrix) }} Proyek</span>
                </div>
                <div class="small text-muted">
                    Nilai dalam Rupiah (Rp). Diambil dari transaksi jenis <strong>Penerimaan</strong> yang telah lunas.
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" tabindex="0" role="region" aria-label="Matriks Pendapatan Bulanan Semua Proyek">
                    <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 0.825rem;">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center" style="width: 45px;">NO</th>
                                <th scope="col" class="text-start" style="min-width: 170px;">PROYEK</th>
                                <th scope="col" class="text-start" style="min-width: 130px;">KLIEN</th>
                                @foreach($monthShorts as $mNum => $mLabel)
                                    <th scope="col" class="text-end px-2 {{ $selectedMonth === $mNum ? 'text-primary fw-bold' : '' }}" style="min-width: 100px;">
                                        {{ strtoupper($mLabel) }}
                                    </th>
                                @endforeach
                                <th scope="col" class="text-end px-2 fw-bold text-dark" style="min-width: 130px;">TOTAL {{ $selectedYear }}</th>
                                <th scope="col" class="text-center" style="width: 90px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyMatrix as $idx => $row)
                                @php
                                    $p = $row['project'];
                                    $yearIncomeStr = $row['year_income'];
                                    $hasYearIncome = $yearIncomeStr !== '0.00' && $yearIncomeStr !== '0';
                                @endphp
                                <tr>
                                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark text-break">{{ $p->nama_proyek }}</div>
                                        <div class="small text-muted text-truncate" style="max-width: 200px;">{{ strip_tags((string) $p->alamat_proyek) ?: '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="text-dark small">{{ $p->user->name ?? 'Klien tidak tersedia' }}</div>
                                    </td>
                                    @foreach($row['months'] as $mNum => $amountStr)
                                        @php
                                            $hasIncome = $amountStr !== '0.00' && $amountStr !== '0';
                                        @endphp
                                        <td class="text-end px-2 {{ $selectedMonth === $mNum ? 'bg-light fw-bold' : '' }}">
                                            @if($hasIncome)
                                                <span class="fw-semibold text-success font-monospace">{{ $formatRupiah($amountStr) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-end px-2 fw-bold text-dark bg-light font-monospace">
                                        {{ $formatRupiah($yearIncomeStr) }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.cashbook.show', $p) }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.775rem;" title="Buka Buku Kas {{ $p->nama_proyek }}">
                                            <i class="fas fa-book-open me-1"></i>Buku Kas
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="text-center py-4 text-muted">
                                        Tidak ada proyek yang sesuai dengan pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="fw-bold bg-light">
                            <tr class="border-top">
                                <td colspan="3" class="text-end text-dark pe-3">TOTAL PENDAPATAN SEMUA PROYEK:</td>
                                @foreach($matrixMonthTotals as $mNum => $totMonth)
                                    @php
                                        $hasMonthTotal = $totMonth !== '0.00' && $totMonth !== '0';
                                    @endphp
                                    <td class="text-end px-2 {{ $selectedMonth === $mNum ? 'bg-light' : '' }}">
                                        @if($hasMonthTotal)
                                            <span class="text-success font-monospace">{{ $formatRupiah($totMonth) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-end px-2 text-primary fw-bold font-monospace">
                                    {{ $formatRupiah($matrixGrandIncome) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <small class="text-muted"><i class="fas fa-info-circle me-1 text-primary"></i> Tabel rekapan ini merangkum total penerimaan/pendapatan kas untuk setiap proyek per bulan pada tahun {{ $selectedYear }}. Klik <strong>Buku Kas</strong> pada baris proyek untuk melihat rincian nota dan pengeluaran.</small>
            </div>
        </div>
    </div>
</div>
@endsection

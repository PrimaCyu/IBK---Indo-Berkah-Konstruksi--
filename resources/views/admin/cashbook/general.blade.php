@extends('admin.layouts.app')

@section('title', 'Overview Keuangan Umum - PT Indo Berkah Konstruksi')
@section('page-title', 'Overview Keuangan Umum')

@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Manajemen Keuangan</li>
    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Keuangan Umum</li>
@endsection

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

    $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $monthShorts = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
@endphp

@section('page-actions')
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="badge-soft-primary px-3 py-1.5 font-monospace">
            <i class="fas fa-calendar-alt me-1" aria-hidden="true"></i> {{ $periodTitle }}
        </span>
        <a href="{{ route('admin.cashbook.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5 py-1 px-2.5 shadow-none" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fas fa-wallet"></i> Keuangan Proyek
        </a>
        <a href="{{ route('admin.cashbook.clients.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 py-1 px-2.5 shadow-none" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fas fa-address-book"></i> Kontak Klien
        </a>
    </div>
@endsection

@section('content')

<style>
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
    .pending-banner {
        background-color: #FFFDF0;
        border: 1px solid #FDE68A;
        border-left: 3.5px solid #F59E0B;
        border-radius: 8px;
        box-shadow: 0 1px 2px 0 rgba(245, 158, 11, 0.05);
    }
    .btn-pending-action {
        background-color: #FEF3C7;
        border: 1px solid #FCD34D;
        color: #92400E;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: background-color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
    }
    .btn-pending-action:hover {
        background-color: #FDE68A;
        border-color: #F59E0B;
        color: #78350F;
        transform: translateY(-0.5px);
    }
</style>

{{-- Compact Pending Notification Banner --}}
@if(($totalPendingCount ?? 0) > 0)
    <div class="pending-banner px-3 py-2 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="d-inline-flex align-items-center justify-content-center text-warning" style="font-size: 0.9rem; color: #D97706 !important;">
                <i class="fas fa-clock-rotate-left"></i>
            </span>
            <span class="fw-semibold text-dark" style="font-size: 0.825rem; letter-spacing: -0.1px;">
                Persetujuan Transaksi Diperlukan
            </span>
            <span class="badge rounded-pill fw-semibold" style="background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; font-size: 0.72rem; padding: 2px 8px;">
                {{ $totalPendingCount }} Piutang
            </span>
            <span class="text-muted d-none d-md-inline" style="opacity: 0.4;">•</span>
            <span class="text-secondary small d-none d-md-inline" style="font-size: 0.8rem;">
                Menunggu verifikasi untuk sinkronisasi saldo kas riil.
            </span>
        </div>
        <div>
            <a href="{{ route('admin.cashbook.index') }}" class="btn-pending-action" title="Buka daftar proyek untuk meninjau transaksi piutang">
                <span>Lihat Transaksi</span>
                <i class="fas fa-arrow-right" style="font-size: 0.65rem;"></i>
            </a>
        </div>
    </div>
@endif

{{-- 1. Filter Periode Konsolidasi --}}
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.cashbook.general') }}" id="cashbook-filter-form" class="row g-2 align-items-center">
            {{-- Pilihan Mode Periode (Dropdown: Bulan, Lengkap, Tahun) --}}
            <div class="col-auto d-flex align-items-center gap-1.5">
                <span class="small fw-bold text-muted text-uppercase d-none d-sm-inline" style="font-size: 0.72rem; letter-spacing: 0.5px;">Periode:</span>
                <select name="period_mode" id="filter-period-mode" class="form-select form-select-sm fw-medium text-dark bg-white" style="min-width: 110px;" onchange="setPeriodMode(this.value)">
                    <option value="bulan" @selected(($activeMode ?? 'bulan') === 'bulan')>Bulan</option>
                    <option value="lengkap" @selected(($activeMode ?? 'bulan') === 'lengkap')>Lengkap</option>
                    <option value="tahun" @selected(($activeMode ?? 'bulan') === 'tahun')>Tahun</option>
                </select>
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
            <div class="col-auto d-flex align-items-center gap-1.5 d-none flex-nowrap" id="filter-col-dates">
                <input type="date" id="filter-start-date" name="start_date" value="{{ $startDate ? (is_string($startDate) ? $startDate : $startDate->format('Y-m-d')) : '' }}" class="form-control form-control-sm" style="width: 140px; flex-shrink: 0;" disabled title="Tanggal Mulai">
                <span class="small text-muted text-nowrap px-1" style="flex-shrink: 0; white-space: nowrap;">s/d</span>
                <input type="date" id="filter-end-date" name="end_date" value="{{ $endDate ? (is_string($endDate) ? $endDate : $endDate->format('Y-m-d')) : '' }}" class="form-control form-control-sm" style="width: 140px; flex-shrink: 0;" disabled title="Tanggal Akhir">
            </div>

            <div class="col-12 col-md-auto ms-auto d-flex align-items-center gap-1.5 justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary px-3 text-nowrap d-flex align-items-center gap-1.5" style="height: 31px;">
                    <i class="fas fa-filter"></i> Terapkan
                </button>
                @if(request()->hasAny(['month', 'year', 'start_date', 'end_date', 'period_mode']))
                    <a href="{{ route('admin.cashbook.general') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center" title="Reset filter" style="height: 31px; width: 31px; padding: 0;">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    function setPeriodMode(mode) {
        const selectMode = document.getElementById('filter-period-mode');
        if (selectMode && selectMode.value !== mode) {
            selectMode.value = mode;
        }

        const colMonth = document.getElementById('filter-col-month');
        const colYear = document.getElementById('filter-col-year');
        const colDates = document.getElementById('filter-col-dates');

        const inputMonth = document.getElementById('filter-month');
        const inputYear = document.getElementById('filter-year');
        const inputStartDate = document.getElementById('filter-start-date');
        const inputEndDate = document.getElementById('filter-end-date');

        if (mode === 'lengkap') {
            if (colMonth) colMonth.classList.add('d-none');
            if (colYear) colYear.classList.add('d-none');
            if (inputMonth) inputMonth.disabled = true;
            if (inputYear) inputYear.disabled = true;

            if (colDates) colDates.classList.remove('d-none');
            if (inputStartDate) inputStartDate.disabled = false;
            if (inputEndDate) inputEndDate.disabled = false;
        } else if (mode === 'tahun') {
            if (colMonth) colMonth.classList.add('d-none');
            if (inputMonth) inputMonth.disabled = true;

            if (colYear) colYear.classList.remove('d-none');
            if (inputYear) inputYear.disabled = false;

            if (colDates) colDates.classList.add('d-none');
            if (inputStartDate) inputStartDate.disabled = true;
            if (inputEndDate) inputEndDate.disabled = true;
        } else {
            // Mode 'bulan' (Bulan + Tahun)
            if (colMonth) colMonth.classList.remove('d-none');
            if (colYear) colYear.classList.remove('d-none');
            if (inputMonth) inputMonth.disabled = false;
            if (inputYear) inputYear.disabled = false;

            if (colDates) colDates.classList.add('d-none');
            if (inputStartDate) inputStartDate.disabled = true;
            if (inputEndDate) inputEndDate.disabled = true;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const initialMode = document.getElementById('filter-period-mode')?.value || 'bulan';
        setPeriodMode(initialMode);
    });
</script>

{{-- 2. Ringkasan Keuangan Perusahaan (3 Top KPI Cards) --}}
<div class="row g-3 mb-3" aria-label="Ringkasan keuangan konsolidasi">
    {{-- Card 1: Penerimaan --}}
    <div class="col-md-4">
        <div class="card h-100 mb-0 shadow-sm">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="form-label mb-0 fw-bold text-muted small">TOTAL PENERIMAAN (LUNAS)</span>
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

    {{-- Card 2: Pengeluaran --}}
    <div class="col-md-4">
        <div class="card h-100 mb-0 shadow-sm">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="form-label mb-0 fw-bold text-muted small">TOTAL PENGELUARAN (LUNAS)</span>
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

    {{-- Card 3: Saldo Bersih & Margin --}}
    <div class="col-md-4">
        <div class="card h-100 mb-0 shadow-sm">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="form-label mb-0 fw-bold text-muted small">SELISIH KAS / SALDO BERSIH</span>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; background-color: #EFF6FF; color: #2563EB;">
                        <i class="fas fa-wallet small"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-dark cashbook-number mb-1">{{ $formatRupiah($grandPeriodBalance) }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Margin: <strong class="{{ ($grandMargin ?? 0) >= 0 ? 'text-success' : 'text-danger' }} font-monospace">{{ ($grandMargin ?? 0) >= 0 ? '+' : '' }}{{ $grandMargin ?? 0 }}% Laba</strong>
                    </small>
                    <span class="badge-soft-primary font-monospace" style="font-size: 0.7rem;">Saldo Kas</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. Analisis Pengeluaran & Pemasukan (Category Progress Bars) --}}
<div class="row g-3 mb-4">
    {{-- Left: Komposisi Pengeluaran Operasional --}}
    <div class="col-lg-6">
        <div class="card h-100 mb-0 shadow-sm">
            <div class="card-header py-2.5 px-3.5 d-flex align-items-center justify-content-between bg-white border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chart-pie text-danger small"></i>
                    <h2 class="h6 fw-bold mb-0 text-dark">Komposisi Pengeluaran Operasional</h2>
                </div>
                <span class="badge-soft-danger font-monospace" style="font-size: 0.7rem;">Beban: {{ $expenseRatio }}%</span>
            </div>
            <div class="card-body p-3.5">
                @forelse($expenseBreakdown as $item)
                    <div class="analytics-progress-row">
                        <div class="analytics-label" title="{{ $item['name'] }}">
                            {{ $item['name'] }}
                        </div>
                        <div class="analytics-bar-wrap">
                            <div class="progress" style="height: 6px; background-color: #f1f5f9; border-radius: 9999px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $item['percent'] }}%; border-radius: 9999px;" aria-valuenow="{{ $item['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="analytics-amount">
                            {{ $formatShortRupiah($item['amount']) }}
                            <span class="text-muted fw-normal font-monospace ms-1" style="font-size: 0.7rem;">({{ $item['percent'] }}%)</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="fas fa-inbox fa-2x mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0">Belum ada data pengeluaran pada periode ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Komposisi Penerimaan Kas --}}
    <div class="col-lg-6">
        <div class="card h-100 mb-0 shadow-sm">
            <div class="card-header py-2.5 px-3.5 d-flex align-items-center justify-content-between bg-white border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chart-line text-success small"></i>
                    <h2 class="h6 fw-bold mb-0 text-dark">Komposisi Penerimaan Kas</h2>
                </div>
                <span class="badge-soft-success font-monospace" style="font-size: 0.7rem;">Total Lunas</span>
            </div>
            <div class="card-body p-3.5">
                @forelse($incomeBreakdown as $item)
                    <div class="analytics-progress-row">
                        <div class="analytics-label" title="{{ $item['name'] }}">
                            {{ $item['name'] }}
                        </div>
                        <div class="analytics-bar-wrap">
                            <div class="progress" style="height: 6px; background-color: #f1f5f9; border-radius: 9999px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $item['percent'] }}%; border-radius: 9999px;" aria-valuenow="{{ $item['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="analytics-amount text-success">
                            {{ $formatShortRupiah($item['amount']) }}
                            <span class="text-muted fw-normal font-monospace ms-1" style="font-size: 0.7rem;">({{ $item['percent'] }}%)</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="fas fa-inbox fa-2x mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0">Belum ada data penerimaan pada periode ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- 4. Matriks Rekapan Pendapatan Bulanan Konsolidasi --}}
<div class="card shadow-sm">
    <div class="card-header py-2.5 px-3.5 d-flex justify-content-between align-items-center flex-wrap gap-2 bg-white border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-table-cells text-primary"></i>
            <h2 class="h6 fw-bold mb-0 text-dark">Rekapan Pendapatan Bulanan (Semua Proyek)</h2>
            <span class="badge-soft-primary">{{ $totalProjectsCount }} Proyek</span>
        </div>
        <form method="GET" action="{{ route('admin.cashbook.general') }}" class="d-flex align-items-center gap-2">
            <input type="hidden" name="period_mode" value="tahun">
            <span class="small text-muted d-none d-sm-inline">Tahun:</span>
            <select name="year" class="form-select form-select-sm" style="width: 105px;" onchange="this.form.submit()">
                @foreach($years as $yr)
                    <option value="{{ $yr }}" @selected((int)$selectedYear === (int)$yr)>{{ $yr }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" tabindex="0" role="region" aria-label="Matriks rekapan bulanan">
            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.8rem;">
                <thead class="table-light text-center">
                    <tr>
                        <th style="min-width: 220px;" class="text-start">NAMA PROYEK / KLIEN</th>
                        @foreach($monthShorts as $mNum => $mLabel)
                            <th style="width: 80px;" class="font-monospace">{{ $mLabel }}</th>
                        @endforeach
                        <th style="width: 130px;" class="text-end bg-primary-subtle text-primary font-monospace fw-bold">TOTAL {{ $selectedYear }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlyMatrix as $row)
                        @php
                            $proj = $row['project'];
                            $hasAnnualIncome = $row['year_income'] !== '0.00' && $row['year_income'] !== '0';
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 220px;">
                                    {{ $proj->nama_proyek }}
                                </div>
                                <small class="text-muted text-truncate d-block" style="max-width: 220px;">
                                    {{ $proj->user->name ?? '-' }}
                                </small>
                            </td>
                            @foreach($row['months'] as $mVal)
                                @php $isZero = $mVal === '0.00' || $mVal === '0'; @endphp
                                <td class="text-end font-monospace {{ $isZero ? 'text-muted opacity-50' : 'fw-semibold text-dark' }}">
                                    {{ $isZero ? '-' : $formatShortRupiah($mVal) }}
                                </td>
                            @endforeach
                            <td class="text-end font-monospace fw-bold {{ $hasAnnualIncome ? 'text-success bg-light' : 'text-muted' }}">
                                {{ $hasAnnualIncome ? $formatRupiah($row['year_income']) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center py-4 text-muted">
                                Tidak ada data proyek tercatat untuk tahun {{ $selectedYear }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="text-start text-dark">TOTAL PENDAPATAN SEMUA PROYEK:</td>
                        @foreach($matrixMonthTotals as $mTotal)
                            @php $isZeroTotal = $mTotal === '0.00' || $mTotal === '0'; @endphp
                            <td class="text-end font-monospace text-primary" style="font-size: 0.775rem;">
                                {{ $isZeroTotal ? '-' : $formatShortRupiah($mTotal) }}
                            </td>
                        @endforeach
                        <td class="text-end font-monospace fs-6 text-success bg-primary-subtle">
                            {{ $formatRupiah($matrixGrandIncome) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection

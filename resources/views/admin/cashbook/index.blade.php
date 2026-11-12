@extends('admin.layouts.app')

@section('title', 'Daftar Keuangan Proyek - PT Indo Berkah Konstruksi')
@section('page-title', 'Daftar Keuangan Proyek')

@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Manajemen Keuangan</li>
    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Keuangan Proyek</li>
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

    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
@endphp

@section('page-actions')
    <span class="badge-soft-primary px-3 py-1.5 font-monospace">
        <i class="fas fa-calendar-alt me-1" aria-hidden="true"></i> {{ $periodTitle }}
    </span>
@endsection

@section('content')

<style>
    .client-avatar-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #EFF6FF;
        color: #2563EB;
        border: 1px solid #DBEAFE;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.72rem;
        flex-shrink: 0;
    }
    #proyek-table-card .table th {
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-weight: 700;
        color: #64748B;
        padding: 9px 8px;
        vertical-align: middle;
        white-space: nowrap;
        background-color: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
    }
    #proyek-table-card .table td {
        vertical-align: middle;
        padding: 10px 8px;
        border-bottom: 1px solid #F1F5F9;
    }
    #proyek-table-card .table tbody tr:hover {
        background-color: #F8FAFC;
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
    .row-has-pending.highlight-pending {
        background-color: #FEF3C7 !important;
        transition: background-color 0.4s ease;
    }
</style>

@php
    $pendingProjects = collect($projectsData)->filter(fn($p) => ($p['pending_count'] ?? 0) > 0);
    $firstPending = $pendingProjects->first();
    $isSinglePending = $pendingProjects->count() === 1;
@endphp

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
                Pilih Buku Kas pada proyek terkait untuk memeriksa rincian transaksi.
            </span>
        </div>
        <div>
            @if($isSinglePending && $firstPending)
                <a href="{{ route('admin.cashbook.show', $firstPending['project']) }}?status=pending" class="btn-pending-action" title="Buka Buku Kas {{ $firstPending['project']->nama_proyek }} (Status: Piutang)">
                    <span>Lihat Transaksi</span>
                    <i class="fas fa-arrow-right" style="font-size: 0.65rem;"></i>
                </a>
            @else
                <a href="#proyek-table-card" onclick="scrollToPendingProjects(event)" class="btn-pending-action" title="Gulir ke daftar proyek yang memiliki transaksi piutang">
                    <span>Lihat Transaksi</span>
                    <i class="fas fa-arrow-down" style="font-size: 0.65rem;"></i>
                </a>
            @endif
        </div>
    </div>
@endif

{{-- 5 Summary KPI Cards: Total Proyek, Pemasukan, Pengeluaran, Piutang, Selisih (Selisih di Paling Kanan) --}}
<div class="row g-2 mb-3">
    <div class="col-6 col-lg">
        <div class="card h-100 mb-0 shadow-sm border" style="border-radius: 10px; border-color: #E2E8F0;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">TOTAL PROYEK</span>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background-color: #F1F5F9; color: #475569;">
                        <i class="fas fa-building small"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-dark cashbook-number mb-0.5 text-nowrap" style="font-size: 1.15rem; line-height: 1.25;">{{ count($projectsData) }}</div>
                <small class="text-muted" style="font-size: 0.72rem;">Proyek Terdata</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg">
        <div class="card h-100 mb-0 shadow-sm border" style="border-radius: 10px; border-color: #E2E8F0;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">PEMASUKAN</span>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background-color: #ECFDF5; color: #10B981;">
                        <i class="fas fa-arrow-down small"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-success cashbook-number mb-0.5 text-nowrap" style="font-size: 1.15rem; line-height: 1.25;">+ {{ $formatRupiah($grandPeriodIncome) }}</div>
                <small class="text-muted" style="font-size: 0.72rem;">Penerimaan Lunas</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg">
        <div class="card h-100 mb-0 shadow-sm border" style="border-radius: 10px; border-color: #E2E8F0;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">PENGELUARAN</span>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background-color: #FEF2F2; color: #EF4444;">
                        <i class="fas fa-arrow-up small"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-danger cashbook-number mb-0.5 text-nowrap" style="font-size: 1.15rem; line-height: 1.25;">− {{ $formatRupiah($grandPeriodExpense) }}</div>
                <small class="text-muted" style="font-size: 0.72rem;">Beban Kas</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg">
        <div class="card h-100 mb-0 shadow-sm border" style="border-radius: 10px; border-color: #E2E8F0;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">PIUTANG</span>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background-color: #FEF3C7; color: #D97706;">
                        <i class="fas fa-clock small"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-warning cashbook-number mb-0.5 text-nowrap" style="font-size: 1.15rem; line-height: 1.25; color: #D97706 !important;">{{ $formatRupiah($grandPeriodReceivable) }}</div>
                <small class="text-muted" style="font-size: 0.72rem;">Menunggu Pembayaran</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg">
        <div class="card h-100 mb-0 shadow-sm border" style="border-radius: 10px; border-color: #E2E8F0;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">SELISIH</span>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background-color: #EFF6FF; color: #2563EB;">
                        <i class="fas fa-wallet small"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-dark cashbook-number mb-0.5 text-nowrap" style="font-size: 1.15rem; line-height: 1.25;">{{ $formatRupiah($grandPeriodBalance) }}</div>
                <small class="text-muted" style="font-size: 0.72rem;">
                    @if($grandMargin !== null)
                        Margin: <strong class="{{ $grandMargin >= 0 ? 'text-success' : 'text-danger' }}">{{ $grandMargin >= 0 ? '+' : '' }}{{ $grandMargin }}%</strong>
                    @else
                        Saldo Bersih
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar: Periode, Status Proyek, dan Pencarian Cepat --}}
<div class="card mb-3 shadow-sm border" style="border-radius: 10px; border-color: #E2E8F0;">
    <div class="card-body py-2.5 px-3">
        <form method="GET" action="{{ route('admin.cashbook.index') }}" id="cashbook-filter-form" class="row g-2 align-items-center">
            {{-- Pilihan Mode Periode (Dropdown: Bulan, Lengkap, Tahun) --}}
            <div class="col-auto d-flex align-items-center gap-1.5">
                <span class="small fw-bold text-muted text-uppercase d-none d-sm-inline" style="font-size: 0.72rem; letter-spacing: 0.5px;">Periode:</span>
                <select name="period_mode" id="filter-period-mode" class="form-select form-select-sm fw-medium text-dark bg-white" style="min-width: 105px; height: 32px; font-size: 0.8rem; border-color: #CBD5E1; border-radius: 6px;" onchange="setPeriodMode(this.value)">
                    <option value="bulan" @selected(($activeMode ?? 'bulan') === 'bulan')>Bulan</option>
                    <option value="lengkap" @selected(($activeMode ?? 'bulan') === 'lengkap')>Lengkap</option>
                    <option value="tahun" @selected(($activeMode ?? 'bulan') === 'tahun')>Tahun</option>
                </select>
            </div>

            {{-- Dynamic Inputs Bulan & Tahun --}}
            <div class="col-6 col-sm-auto" id="filter-col-month">
                <select id="filter-month" name="month" class="form-select form-select-sm" style="min-width: 135px; height: 32px; font-size: 0.8rem; border-color: #CBD5E1; border-radius: 6px;">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" @selected((int)$selectedMonth === (int)$num)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-sm-auto" id="filter-col-year">
                <select id="filter-year" name="year" class="form-select form-select-sm" style="min-width: 95px; height: 32px; font-size: 0.8rem; border-color: #CBD5E1; border-radius: 6px;">
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" @selected((int)$selectedYear === (int)$yr)>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dynamic Inputs Rentang Tanggal (Lengkap) --}}
            <div class="col-auto d-flex align-items-center gap-1.5 d-none flex-nowrap" id="filter-col-dates">
                <input type="date" id="filter-start-date" name="start_date" value="{{ $startDate ? (is_string($startDate) ? $startDate : $startDate->format('Y-m-d')) : '' }}" class="form-control form-control-sm" style="width: 140px; flex-shrink: 0; height: 32px; font-size: 0.8rem; border-color: #CBD5E1; border-radius: 6px;" disabled title="Tanggal Mulai">
                <span class="small text-muted text-nowrap px-1" style="flex-shrink: 0; white-space: nowrap;">s/d</span>
                <input type="date" id="filter-end-date" name="end_date" value="{{ $endDate ? (is_string($endDate) ? $endDate : $endDate->format('Y-m-d')) : '' }}" class="form-control form-control-sm" style="width: 140px; flex-shrink: 0; height: 32px; font-size: 0.8rem; border-color: #CBD5E1; border-radius: 6px;" disabled title="Tanggal Akhir">
            </div>

            {{-- Filter Status Proyek --}}
            <div class="col-auto">
                <select name="status" id="filter-status" class="form-select form-select-sm" style="min-width: 130px; height: 32px; font-size: 0.8rem; border-color: #CBD5E1; border-radius: 6px;">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(($statusFilter ?? '') === 'aktif')>Aktif</option>
                    <option value="selesai" @selected(($statusFilter ?? '') === 'selesai')>Selesai</option>
                    <option value="perencanaan" @selected(($statusFilter ?? '') === 'perencanaan')>Perencanaan</option>
                    <option value="dibatalkan" @selected(($statusFilter ?? '') === 'dibatalkan')>Dibatalkan</option>
                </select>
            </div>

            {{-- Cari Proyek / Klien (Search) --}}
            <div class="col-12 col-md">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1; border-radius: 6px 0 0 6px; height: 32px;"><i class="fas fa-search" style="font-size: 0.75rem;"></i></span>
                    <input type="search" id="cashbook-search" name="search" value="{{ $search }}" class="form-control form-control-sm border-start-0" placeholder="Search nama proyek, lokasi, atau klien..." style="border-color: #CBD5E1; border-radius: 0 6px 6px 0; height: 32px; font-size: 0.8rem;">
                </div>
            </div>

            {{-- Tombol Terapkan & Reset --}}
            <div class="col-auto d-flex align-items-center gap-1.5">
                <button type="submit" class="btn btn-sm btn-primary px-3 text-nowrap d-flex align-items-center gap-1.5" style="height: 32px; font-size: 0.8rem; font-weight: 600; border-radius: 6px;">
                    <i class="fas fa-filter" style="font-size: 0.72rem;"></i> Terapkan
                </button>
                @if(request()->hasAny(['month', 'year', 'search', 'start_date', 'end_date', 'period_mode', 'status']))
                    <a href="{{ route('admin.cashbook.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center" title="Reset filter" style="height: 32px; width: 32px; padding: 0; border-radius: 6px; border-color: #CBD5E1;">
                        <i class="fas fa-rotate-left" style="font-size: 0.75rem;"></i>
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

    function scrollToPendingProjects(e) {
        if (e) e.preventDefault();
        const tableCard = document.getElementById('proyek-table-card');
        if (tableCard) {
            tableCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            const pendingRows = document.querySelectorAll('.row-has-pending');
            pendingRows.forEach(row => {
                row.classList.add('highlight-pending');
                setTimeout(() => row.classList.remove('highlight-pending'), 2500);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const initialMode = document.getElementById('filter-period-mode')?.value || 'bulan';
        setPeriodMode(initialMode);
    });
</script>

{{-- Tabel Utama: Daftar Buku Kas Proyek --}}
<div class="card shadow-sm border" id="proyek-table-card" style="border-radius: 10px; border-color: #E2E8F0;">
    <div class="card-header py-2.5 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2 bg-white border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-book-bookmark text-primary" style="font-size: 0.9rem;"></i>
            <h2 class="h6 fw-bold mb-0 text-dark" style="font-size: 0.95rem; letter-spacing: -0.2px;">Daftar Buku Kas Proyek</h2>
            <span class="badge rounded-pill bg-light text-secondary border font-monospace px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">{{ count($projectsData) }} Proyek Terdaftar</span>
        </div>
        <div class="small text-muted d-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
            <i class="far fa-calendar-alt text-secondary opacity-75"></i>
            <span>Periode Kas:</span>
            <strong class="text-dark">{{ $periodTitle }}</strong>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive p-0" tabindex="0" role="region" aria-label="Daftar proyek dan rincian kas">
            <table class="table table-hover align-middle mb-0" style="table-layout: fixed; width: 100%; min-width: 1120px;">
                <colgroup>
                    <col style="width: 44px;">
                    <col style="width: 21%;">
                    <col style="width: 15.5%;">
                    <col style="width: 12.5%;">
                    <col style="width: 12.5%;">
                    <col style="width: 11.5%;">
                    <col style="width: 13%;">
                    <col style="width: 82px;">
                    <col style="width: 95px;">
                </colgroup>
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-center px-1">NO</th>
                        <th scope="col" class="ps-3">PROYEK & LOKASI</th>
                        <th scope="col">KLIEN</th>
                        <th scope="col" class="text-end text-success pe-3">PEMASUKAN</th>
                        <th scope="col" class="text-end text-danger pe-3">PENGELUARAN</th>
                        <th scope="col" class="text-end text-warning pe-3">PIUTANG</th>
                        <th scope="col" class="text-end text-primary pe-3">SELISIH</th>
                        <th scope="col" class="text-center px-1">STATUS</th>
                        <th scope="col" class="text-center px-1">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projectsData as $index => $item)
                        @php
                            $project = $item['project'];
                            $hasIncome = $item['period_income'] !== '0.00' && $item['period_income'] !== '0';
                            $hasExpense = $item['period_expense'] !== '0.00' && $item['period_expense'] !== '0';
                            $hasReceivable = ($item['period_receivable'] ?? '0.00') !== '0.00' && ($item['period_receivable'] ?? '0.00') !== '0';
                            $clientName = $project->user->name ?? 'Klien';
                            $clientInitials = collect(explode(' ', $clientName))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
                        @endphp
                        <tr class="{{ ($item['pending_count'] ?? 0) > 0 ? 'row-has-pending' : '' }}">
                            <td class="text-center text-muted small px-1">{{ $index + 1 }}</td>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-nowrap">
                                    <span class="fw-bold text-dark text-truncate" style="font-size: 0.84rem; letter-spacing: -0.1px;" title="{{ $project->nama_proyek }}">
                                        {{ $project->nama_proyek }}
                                    </span>
                                    @if($project->status === 'selesai')
                                        <span class="badge-soft-success flex-shrink-0" style="font-size: 0.65rem; padding: 2px 7px; border-radius: 4px;">Selesai</span>
                                    @elseif($project->status === 'aktif')
                                        <span class="badge-soft-primary flex-shrink-0" style="font-size: 0.65rem; padding: 2px 7px; border-radius: 4px;">Aktif</span>
                                    @else
                                        <span class="badge-soft-neutral flex-shrink-0" style="font-size: 0.65rem; padding: 2px 7px; border-radius: 4px;">{{ ucfirst($project->status) }}</span>
                                    @endif
                                </div>
                                <div class="small text-muted text-truncate d-flex align-items-center gap-1" style="font-size: 0.73rem;" title="{{ strip_tags((string) $project->alamat_proyek) ?: 'Lokasi belum diisi' }}">
                                    <i class="fas fa-map-marker-alt text-secondary opacity-75 flex-shrink-0" style="font-size: 0.68rem;"></i>
                                    <span class="text-truncate">{{ strip_tags((string) $project->alamat_proyek) ?: 'Lokasi belum diisi' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="client-avatar-circle" title="{{ $clientName }}">
                                        {{ strtoupper($clientInitials) }}
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="text-dark fw-semibold text-truncate" style="font-size: 0.83rem; line-height: 1.35;" title="{{ $clientName }}">
                                            {{ $clientName }}
                                        </div>
                                        @php
                                            $rawPhone = $project->user?->phone;
                                            $cleanWa = null;
                                            if ($rawPhone) {
                                                $digits = preg_replace('/[^0-9]/', '', (string)$rawPhone);
                                                if (str_starts_with($digits, '0')) {
                                                    $cleanWa = '62' . substr($digits, 1);
                                                } elseif (str_starts_with($digits, '62')) {
                                                    $cleanWa = $digits;
                                                } else {
                                                    $cleanWa = '62' . $digits;
                                                }
                                            }
                                        @endphp
                                        @if($rawPhone && $cleanWa)
                                            <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('Halo ' . $clientName . ', kami dari Bagian Keuangan PT Indo Berkah Konstruksi...') }}" 
                                               target="_blank" 
                                               class="text-success text-decoration-none d-inline-flex align-items-center gap-1 text-truncate" 
                                               style="font-size: 0.73rem; line-height: 1.35;"
                                               title="Chat WhatsApp: {{ $rawPhone }}">
                                                <i class="fab fa-whatsapp" style="font-size: 0.78rem;"></i>
                                                <span class="font-monospace">{{ $rawPhone }}</span>
                                            </a>
                                        @else
                                            <small class="text-muted d-block text-truncate" style="font-size: 0.72rem; line-height: 1.35;" title="{{ $project->user->email ?? '-' }}">{{ $project->user->email ?? '-' }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                @if($hasIncome)
                                    <span class="fw-semibold text-success font-monospace text-nowrap" style="font-size: 0.815rem; letter-spacing: -0.2px;">+ {{ $formatRupiah($item['period_income']) }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                @if($hasExpense)
                                    <span class="fw-semibold text-danger font-monospace text-nowrap" style="font-size: 0.815rem; letter-spacing: -0.2px;">− {{ $formatRupiah($item['period_expense']) }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                @if($hasReceivable)
                                    <span class="fw-semibold text-warning font-monospace text-nowrap" style="font-size: 0.815rem; letter-spacing: -0.2px; color: #D97706 !important;">{{ $formatRupiah($item['period_receivable']) }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <div class="fw-bold text-dark font-monospace text-nowrap" style="font-size: 0.825rem; letter-spacing: -0.2px; line-height: 1.25;">
                                    {{ $formatRupiah($item['lifetime_balance']) }}
                                </div>
                                @if($item['lifetime_margin'] !== null)
                                    <div class="font-monospace text-nowrap {{ $item['lifetime_margin'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 0.68rem; font-weight: 600; line-height: 1.25;" title="Margin Profit: {{ $item['lifetime_margin'] }}%">
                                        <i class="fas {{ $item['lifetime_margin'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} me-0.5" style="font-size: 0.6rem;"></i>{{ $item['lifetime_margin'] >= 0 ? '+' : '' }}{{ $item['lifetime_margin'] }}% Laba
                                    </div>
                                @endif
                            </td>
                            <td class="text-center px-1 text-nowrap">
                                @if(($item['pending_count'] ?? 0) > 0)
                                    <span class="badge rounded-pill fw-semibold" style="background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; font-size: 0.7rem; padding: 3px 8px;" title="{{ $item['pending_count'] }} transaksi kas piutang / pending">
                                        <i class="fas fa-clock me-1 text-warning"></i>Piutang
                                    </span>
                                @else
                                    <span class="badge rounded-pill fw-semibold" style="background-color: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.7rem; padding: 3px 8px;" title="Semua transaksi kas telah lunas">
                                        <i class="fas fa-check-circle me-1 text-success"></i>Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="text-center px-1 text-nowrap">
                                <a href="{{ route('admin.cashbook.show', $project) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center gap-1.5 text-nowrap shadow-none" style="font-size: 0.74rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; height: 28px;" aria-label="Buka buku kas {{ $project->nama_proyek }}">
                                    <i class="fas fa-book-open" style="font-size: 0.7rem;" aria-hidden="true"></i>
                                    <span>Buku Kas</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <p class="fw-semibold mb-1 text-dark">Tidak ada proyek yang sesuai kriteria pencarian.</p>
                                <p class="small text-muted mb-3">Coba ubah filter status, periode, atau kata kunci pencarian Anda.</p>
                                <a href="{{ route('admin.cashbook.index') }}" class="btn btn-sm btn-outline-secondary">Reset filter</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($projects->hasPages())
        <div class="card-footer py-2.5 px-3.5 bg-white border-top">
            {{ $projects->links() }}
        </div>
    @endif
</div>

@endsection

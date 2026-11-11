@extends('admin.layouts.app')

@section('title', 'Daftar Keuangan Proyek - PT Indo Berkah Konstruksi')
@section('page-title', 'Daftar Keuangan Proyek')
@section('page-subtitle', 'Kelola buku kas, verifikasi transaksi, dan arus kas operasional masing-masing proyek')

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
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.cashbook.general') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 shadow-sm">
            <i class="fas fa-chart-pie text-primary"></i>
            <span>Overview Keuangan Umum</span>
        </a>
        <span class="badge-soft-primary px-3 py-1.5 font-monospace">
            <i class="fas fa-calendar-alt me-1" aria-hidden="true"></i> {{ $periodTitle }}
        </span>
    </div>
@endsection

@section('content')

<style>
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
                    <strong>Persetujuan Transaksi Diperlukan:</strong> Terdapat <strong>{{ $totalPendingCount }} transaksi</strong> kas berstatus <strong>Pending</strong> yang menunggu verifikasi.
                </div>
            </div>
            <div class="small text-muted font-monospace">
                Pilih tombol <strong>Buku Kas</strong> pada proyek dengan label pending di bawah.
            </div>
        </div>
    </div>
@endif

{{-- Filter Bar: Periode, Status Proyek, dan Pencarian Cepat --}}
<div class="card mb-3 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.cashbook.index') }}" id="cashbook-filter-form" class="row g-2 align-items-center">
            {{-- Pilihan Mode Periode (Dropdown: Bulan, Lengkap, Tahun) --}}
            <div class="col-auto d-flex align-items-center gap-1.5">
                <span class="small fw-bold text-muted text-uppercase d-none d-sm-inline" style="font-size: 0.72rem; letter-spacing: 0.5px;">Periode:</span>
                <select name="period_mode" id="filter-period-mode" class="form-select form-select-sm fw-medium text-dark bg-white" style="min-width: 105px;" onchange="setPeriodMode(this.value)">
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
            <div class="col-auto d-flex align-items-center gap-1.5 d-none" id="filter-col-dates">
                <input type="date" id="filter-start-date" name="start_date" value="{{ $startDate ? (is_string($startDate) ? $startDate : $startDate->format('Y-m-d')) : '' }}" class="form-control form-control-sm" style="width: 140px;" disabled title="Tanggal Mulai">
                <span class="small text-muted">s/d</span>
                <input type="date" id="filter-end-date" name="end_date" value="{{ $endDate ? (is_string($endDate) ? $endDate : $endDate->format('Y-m-d')) : '' }}" class="form-control form-control-sm" style="width: 140px;" disabled title="Tanggal Akhir">
            </div>

            {{-- Filter Status Proyek --}}
            <div class="col-auto">
                <select name="status" id="filter-status" class="form-select form-select-sm" style="min-width: 130px;">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(($statusFilter ?? '') === 'aktif')>Aktif</option>
                    <option value="selesai" @selected(($statusFilter ?? '') === 'selesai')>Selesai</option>
                    <option value="perencanaan" @selected(($statusFilter ?? '') === 'perencanaan')>Perencanaan</option>
                    <option value="dibatalkan" @selected(($statusFilter ?? '') === 'dibatalkan')>Dibatalkan</option>
                </select>
            </div>

            {{-- Cari Proyek / Klien --}}
            <div class="col-12 col-md">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="search" id="cashbook-search" name="search" value="{{ $search }}" class="form-control form-control-sm border-start-0" placeholder="Cari nama proyek, lokasi, atau klien...">
                </div>
            </div>

            {{-- Tombol Terapkan & Reset --}}
            <div class="col-auto d-flex align-items-center gap-1.5">
                <button type="submit" class="btn btn-sm btn-primary px-3 text-nowrap d-flex align-items-center gap-1.5" style="height: 31px;">
                    <i class="fas fa-filter"></i> Terapkan
                </button>
                @if(request()->hasAny(['month', 'year', 'search', 'start_date', 'end_date', 'period_mode', 'status']))
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

{{-- Tabel Utama: Daftar Buku Kas Proyek --}}
<div class="card shadow-sm">
    <div class="card-header py-2.5 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2 bg-white border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-wallet text-primary"></i>
            <h2 class="h6 fw-bold mb-0 text-dark">Daftar Buku Kas Proyek</h2>
            <span class="badge-soft-neutral">{{ count($projectsData) }} Proyek Terdaftar</span>
        </div>
        <div class="small text-muted">
            Periode Kas: <strong>{{ $periodTitle }}</strong>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" tabindex="0" role="region" aria-label="Daftar proyek dan rincian kas">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
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
                                <div class="fw-bold text-dark font-monospace" style="font-size: 0.875rem;">
                                    {{ $formatRupiah($item['lifetime_balance']) }}
                                </div>
                                @if($item['lifetime_margin'] !== null)
                                    <div class="font-monospace {{ $item['lifetime_margin'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 0.72rem; font-weight: 600;" title="Margin Profit: {{ $item['lifetime_margin'] }}%">
                                        <i class="fas {{ $item['lifetime_margin'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} me-0.5" style="font-size: 0.65rem;"></i>{{ $item['lifetime_margin'] >= 0 ? '+' : '' }}{{ $item['lifetime_margin'] }}% Laba
                                    </div>
                                @endif
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

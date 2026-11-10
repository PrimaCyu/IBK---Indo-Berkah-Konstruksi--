@extends('admin.layouts.app')

@section('title', 'Keuangan proyek & Rekapan Pendapatan')
@section('page-title', 'Keuangan proyek')

@section('page-actions')
    <nav aria-label="Breadcrumb">
        <ol class="breadcrumb mb-0 bg-white px-3 py-1 rounded-pill border small">
            <li class="breadcrumb-item active" aria-current="page">Keuangan proyek</li>
        </ol>
    </nav>
@endsection

@section('content')
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

{{-- Filter Card (Reference-Style Minimalist Controls) --}}
<div class="card mb-3">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold mb-0 text-dark">Filter Periode & Rekapan Pendapatan</h2>
                <p class="small text-muted mb-0">Pilih bulan dan tahun untuk melihat rekapan pendapatan semua proyek secara komprehensif.</p>
            </div>
            <span class="badge-soft-primary px-3 py-1.5 font-monospace">
                <i class="fas fa-calendar-alt me-1" aria-hidden="true"></i> {{ $periodTitle }}
            </span>
        </div>
        <form method="GET" action="{{ route('admin.cashbook.index') }}" class="row g-3 align-items-end pt-3 border-top">
            <div class="col-sm-6 col-md-3">
                <label for="filter-month" class="form-label">BULAN</label>
                <select id="filter-month" name="month" class="form-select">
                    <option value="">Semua Bulan</option>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" @selected((string)$selectedMonth === (string)$num)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-3">
                <label for="filter-year" class="form-label">TAHUN</label>
                <select id="filter-year" name="year" class="form-select">
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" @selected((string)$selectedYear === (string)$yr)>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="cashbook-search" class="form-label">CARI PROYEK / KLIEN</label>
                <input type="search" id="cashbook-search" name="search" value="{{ $search }}" class="form-control" placeholder="Nama proyek, alamat, atau klien...">
            </div>
            <div class="col-md-2 d-flex gap-2 align-items-center">
                <button type="submit" class="btn btn-primary w-100" style="height: 38px;">
                    <i class="fas fa-filter me-1" aria-hidden="true"></i> Terapkan
                </button>
                @if(request()->hasAny(['month', 'year', 'search']))
                    <a href="{{ route('admin.cashbook.index') }}" class="btn-filter-reset" title="Reset filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards (Grand Totals across projects - Minimalist 16px Radius) --}}
<div class="row g-3 mb-4" aria-label="Ringkasan keuangan semua proyek">
    <div class="col-md-4">
        <div class="card h-100 mb-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="form-label mb-0">TOTAL PENERIMAAN</span>
                    <span class="badge-soft-success"><i class="fas fa-arrow-down"></i> Masuk</span>
                </div>
                <div class="fs-4 fw-bold text-success cashbook-number mb-1">+ {{ $formatRupiah($grandPeriodIncome) }}</div>
                <small class="text-muted" style="font-size: 0.75rem;">Periode: {{ $periodTitle }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 mb-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="form-label mb-0">TOTAL PENGELUARAN</span>
                    <span class="badge-soft-danger"><i class="fas fa-arrow-up"></i> Keluar</span>
                </div>
                <div class="fs-4 fw-bold text-danger cashbook-number mb-1">− {{ $formatRupiah($grandPeriodExpense) }}</div>
                <small class="text-muted" style="font-size: 0.75rem;">Periode: {{ $periodTitle }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 mb-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="form-label mb-0">SELISIH KAS / LABA</span>
                    <span class="badge-soft-primary"><i class="fas fa-wallet"></i> Bersih</span>
                </div>
                <div class="fs-4 fw-bold text-dark cashbook-number mb-1">{{ $formatRupiah($grandPeriodBalance) }}</div>
                <small class="text-muted" style="font-size: 0.75rem;">Periode: {{ $periodTitle }}</small>
            </div>
        </div>
    </div>
</div>

{{-- Nav Tabs: Rekapan Bulanan vs Daftar Proyek --}}
<ul class="nav nav-pills mb-3 gap-2" id="cashbook-tab-nav" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="btn btn-sm btn-outline-secondary active fw-bold px-3 py-2" id="monthly-recap-tab" data-bs-toggle="tab" data-bs-target="#monthly-recap" type="button" role="tab" aria-controls="monthly-recap" aria-selected="true">
            <i class="fas fa-chart-line me-1.5 text-primary" aria-hidden="true"></i> Rekapan Pendapatan Bulanan (Semua Proyek)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="btn btn-sm btn-outline-secondary fw-bold px-3 py-2" id="project-list-tab" data-bs-toggle="tab" data-bs-target="#project-list" type="button" role="tab" aria-controls="project-list" aria-selected="false">
            <i class="fas fa-list-alt me-1.5 text-primary" aria-hidden="true"></i> Rincian & Saldo Kas Per Proyek
        </button>
    </li>
</ul>

<div class="tab-content" id="cashbook-tab-content">
    {{-- TAB 1: Matriks Rekapan Pendapatan Bulanan Semua Proyek --}}
    <div class="tab-pane fade show active" id="monthly-recap" role="tabpanel" aria-labelledby="monthly-recap-tab">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h6 fw-bold mb-0 text-dark">Matriks Pendapatan Bulanan Proyek Tahun {{ $selectedYear }}</h2>
                    <span class="badge-soft-neutral">{{ count($monthlyMatrix) }} Proyek</span>
                </div>
                <div class="small text-muted">
                    Nilai dalam Rupiah (Rp). Diambil dari transaksi jenis <strong>Penerimaan</strong>.
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" tabindex="0" role="region" aria-label="Matriks Pendapatan Bulanan Semua Proyek">
                    <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center" style="width: 45px;">NO</th>
                                <th scope="col" class="text-start" style="min-width: 170px;">PROYEK</th>
                                <th scope="col" class="text-start" style="min-width: 130px;">KLIEN</th>
                                @foreach($monthShorts as $mNum => $mLabel)
                                    <th scope="col" class="text-end px-2 {{ $selectedMonth === $mNum ? 'text-primary fw-bold' : '' }}" style="min-width: 105px;">
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
                                        <div class="small text-muted text-truncate" style="max-width: 200px;">{{ $p->alamat_proyek ?: '-' }}</div>
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
                                                <span class="fw-semibold text-success">{{ $formatRupiah($amountStr) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-end px-2 fw-bold text-dark bg-light">
                                        {{ $formatRupiah($yearIncomeStr) }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.cashbook.show', $p) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="Buka Buku Kas {{ $p->nama_proyek }}">
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
                                            <span class="text-success">{{ $formatRupiah($totMonth) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-end px-2 text-primary fw-bold">
                                    {{ $formatRupiah($matrixGrandIncome) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light py-2.5">
                <small class="text-muted"><i class="fas fa-info-circle me-1 text-primary"></i> Tabel rekapan ini merangkum total penerimaan/pendapatan kas untuk setiap proyek per bulan pada tahun {{ $selectedYear }}. Klik <strong>Buku Kas</strong> pada baris proyek untuk melihat rincian nota dan pengeluaran.</small>
            </div>
        </div>
    </div>

    {{-- TAB 2: Daftar & Saldo Kas Lengkap Per Proyek --}}
    <div class="tab-pane fade" id="project-list" role="tabpanel" aria-labelledby="project-list-tab">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h6 fw-bold mb-0 text-dark">Rincian Keuangan & Buku Kas Proyek</h2>
                    <span class="badge-soft-neutral">{{ count($projectsData) }} Proyek</span>
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
                                <th scope="col" style="width: 45px;">NO</th>
                                <th scope="col">PROYEK</th>
                                <th scope="col">KLIEN</th>
                                <th scope="col" class="text-end text-success">PENERIMAAN</th>
                                <th scope="col" class="text-end text-danger">PENGELUARAN</th>
                                <th scope="col" class="text-end">SELISIH</th>
                                <th scope="col" class="text-end text-primary">TOTAL SALDO</th>
                                <th scope="col">STATUS</th>
                                <th scope="col" class="text-end">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projectsData as $index => $item)
                                @php
                                    $project = $item['project'];
                                    $hasIncome = $item['period_income'] !== '0.00' && $item['period_income'] !== '0';
                                    $hasExpense = $item['period_expense'] !== '0.00' && $item['period_expense'] !== '0';
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-break text-dark">{{ $project->nama_proyek }}</div>
                                        <small class="text-muted text-break">{{ $project->alamat_proyek ?: 'Lokasi belum diisi' }}</small>
                                    </td>
                                    <td>
                                        <div class="text-dark small">{{ $project->user->name ?? 'Klien tidak tersedia' }}</div>
                                        <small class="text-muted">{{ $project->user->email ?? '' }}</small>
                                    </td>
                                    <td class="text-end">
                                        @if($hasIncome)
                                            <span class="fw-semibold text-success">+ {{ $formatRupiah($item['period_income']) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($hasExpense)
                                            <span class="fw-semibold text-danger">− {{ $formatRupiah($item['period_expense']) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">
                                        {{ $formatRupiah($item['period_balance']) }}
                                    </td>
                                    <td class="text-end fw-bold text-primary">
                                        {{ $formatRupiah($item['lifetime_balance']) }}
                                    </td>
                                    <td>
                                        @if($project->status === 'selesai')
                                            <span class="badge-soft-success">Selesai</span>
                                        @elseif($project->status === 'aktif')
                                            <span class="badge-soft-primary">Aktif</span>
                                        @else
                                            <span class="badge-soft-neutral">{{ ucfirst($project->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.cashbook.show', $project) }}" class="btn btn-sm btn-outline-primary text-nowrap" aria-label="Buka buku kas {{ $project->nama_proyek }}">
                                            <i class="fas fa-book-open me-1" aria-hidden="true"></i> Buka Buku Kas
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
</div>
@endsection

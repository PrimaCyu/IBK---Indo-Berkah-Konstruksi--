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

{{-- Filter Card --}}
<div class="card mb-3">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div>
                <h2 class="h6 fw-bold mb-0"><i class="fas fa-filter me-2 text-primary" aria-hidden="true"></i>Filter Periode & Rekapan Pendapatan</h2>
                <p class="small text-secondary mb-0">Pilih bulan dan tahun untuk melihat rekapan pendapatan semua proyek secara komprehensif.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                <i class="fas fa-calendar-alt me-1" aria-hidden="true"></i> {{ $periodTitle }}
            </span>
        </div>
        <form method="GET" action="{{ route('admin.cashbook.index') }}" class="row g-2 align-items-end pt-2 border-top">
            <div class="col-sm-6 col-md-3">
                <label for="filter-month" class="form-label small fw-semibold">Bulan</label>
                <select id="filter-month" name="month" class="form-select form-select-sm">
                    <option value="">Semua bulan</option>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" @selected((string)$selectedMonth === (string)$num)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-3">
                <label for="filter-year" class="form-label small fw-semibold">Tahun</label>
                <select id="filter-year" name="year" class="form-select form-select-sm">
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" @selected((string)$selectedYear === (string)$yr)>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="cashbook-search" class="form-label small fw-semibold">Cari proyek / klien</label>
                <input type="search" id="cashbook-search" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Nama proyek, alamat, atau klien">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-search me-1" aria-hidden="true"></i> Terapkan
                </button>
                @if(request()->hasAny(['month', 'year', 'search']))
                    <a href="{{ route('admin.cashbook.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                        <i class="fas fa-undo" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards (Grand Totals across projects) --}}
<dl class="row g-3 mb-3" aria-label="Ringkasan keuangan semua proyek">
    <div class="col-md-4">
        <div class="card h-100 mb-0 border-start border-3 border-success shadow-sm">
            <div class="card-body p-3">
                <dt class="small fw-semibold text-secondary mb-1">
                    <i class="fas fa-arrow-down text-success me-1" aria-hidden="true"></i> Total Pendapatan / Penerimaan ({{ $periodTitle }})
                </dt>
                <dd class="h5 fw-bold text-success text-break mb-0">+ {{ $formatRupiah($grandPeriodIncome) }}</dd>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 mb-0 border-start border-3 border-danger shadow-sm">
            <div class="card-body p-3">
                <dt class="small fw-semibold text-secondary mb-1">
                    <i class="fas fa-arrow-up text-danger me-1" aria-hidden="true"></i> Total Pengeluaran ({{ $periodTitle }})
                </dt>
                <dd class="h5 fw-bold text-danger text-break mb-0">− {{ $formatRupiah($grandPeriodExpense) }}</dd>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 mb-0 border-start border-3 border-primary shadow-sm">
            <div class="card-body p-3">
                <dt class="small fw-semibold text-secondary mb-1">
                    <i class="fas fa-wallet text-primary me-1" aria-hidden="true"></i> Selisih Kas / Laba ({{ $periodTitle }})
                </dt>
                <dd class="h5 fw-bold text-dark text-break mb-0">{{ $formatRupiah($grandPeriodBalance) }}</dd>
            </div>
        </div>
    </div>
</dl>

{{-- Nav Tabs: Rekapan Bulanan vs Daftar Proyek --}}
<ul class="nav nav-tabs nav-fill mb-3" id="cashbook-tab-nav" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold py-2" id="monthly-recap-tab" data-bs-toggle="tab" data-bs-target="#monthly-recap" type="button" role="tab" aria-controls="monthly-recap" aria-selected="true">
            <i class="fas fa-chart-line me-2 text-primary" aria-hidden="true"></i> Rekapan Pendapatan Bulanan (Semua Proyek)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold py-2" id="project-list-tab" data-bs-toggle="tab" data-bs-target="#project-list" type="button" role="tab" aria-controls="project-list" aria-selected="false">
            <i class="fas fa-list-alt me-2 text-primary" aria-hidden="true"></i> Rincian & Saldo Kas Per Proyek
        </button>
    </li>
</ul>

<div class="tab-content" id="cashbook-tab-content">
    {{-- TAB 1: Matriks Rekapan Pendapatan Bulanan Semua Proyek --}}
    <div class="tab-pane fade show active" id="monthly-recap" role="tabpanel" aria-labelledby="monthly-recap-tab">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="fs-6 fw-bold mb-0">Matriks Pendapatan Bulanan Proyek Tahun {{ $selectedYear }}</h2>
                    <span class="badge bg-light text-dark border">{{ count($monthlyMatrix) }} proyek</span>
                </div>
                <div class="small text-secondary">
                    Nilai dalam Rupiah (Rp). Diambil dari transaksi jenis <strong>Penerimaan</strong>.
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" tabindex="0" role="region" aria-label="Matriks Pendapatan Bulanan Semua Proyek">
                    <table class="table table-hover table-bordered table-sm align-middle mb-0 text-nowrap" style="font-size: 0.86rem;">
                        <thead class="table-light text-center">
                            <tr>
                                <th scope="col" class="text-center" style="width: 40px;">No</th>
                                <th scope="col" class="text-start" style="min-width: 170px;">Proyek</th>
                                <th scope="col" class="text-start" style="min-width: 130px;">Klien</th>
                                @foreach($monthShorts as $mNum => $mLabel)
                                    <th scope="col" class="text-end px-2 {{ $selectedMonth === $mNum ? 'table-warning text-dark fw-bold' : '' }}" style="min-width: 105px;">
                                        {{ $mLabel }}
                                    </th>
                                @endforeach
                                <th scope="col" class="text-end px-2 table-primary fw-bold" style="min-width: 130px;">Total {{ $selectedYear }}</th>
                                <th scope="col" class="text-center" style="width: 90px;">Aksi</th>
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
                                    <td class="text-center">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark text-break">{{ $p->nama_proyek }}</div>
                                        <div class="small text-secondary text-truncate" style="max-width: 200px;">{{ $p->alamat_proyek ?: '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="text-break">{{ $p->user->name ?? 'Klien tidak tersedia' }}</div>
                                    </td>
                                    @foreach($row['months'] as $mNum => $amountStr)
                                        @php
                                            $hasIncome = $amountStr !== '0.00' && $amountStr !== '0';
                                        @endphp
                                        <td class="text-end px-2 {{ $selectedMonth === $mNum ? 'table-warning' : '' }}">
                                            @if($hasIncome)
                                                <span class="fw-semibold text-success">{{ $formatRupiah($amountStr) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-end px-2 table-primary fw-bold text-dark">
                                        {{ $formatRupiah($yearIncomeStr) }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.cashbook.show', $p) }}" class="btn btn-xs btn-outline-primary" title="Buka Buku Kas {{ $p->nama_proyek }}">
                                            <i class="fas fa-book-open me-1"></i>Buku kas
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="text-center py-4 text-secondary">
                                        Tidak ada proyek yang sesuai dengan pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr class="table-secondary border-top border-2">
                                <td colspan="3" class="text-end text-dark pe-3">TOTAL PENDAPATAN SEMUA PROYEK:</td>
                                @foreach($matrixMonthTotals as $mNum => $totMonth)
                                    @php
                                        $hasMonthTotal = $totMonth !== '0.00' && $totMonth !== '0';
                                    @endphp
                                    <td class="text-end px-2 {{ $selectedMonth === $mNum ? 'table-warning' : '' }}">
                                        @if($hasMonthTotal)
                                            <span class="text-success">{{ $formatRupiah($totMonth) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-end px-2 table-primary text-primary fw-bold">
                                    {{ $formatRupiah($matrixGrandIncome) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <small class="text-secondary"><i class="fas fa-info-circle me-1 text-primary"></i> Tabel rekapan ini merangkum total penerimaan/pendapatan kas untuk setiap proyek per bulan pada tahun {{ $selectedYear }}. Klik <strong>Buku kas</strong> pada tiap baris proyek untuk melihat rincian nota dan pengeluaran proyek.</small>
            </div>
        </div>
    </div>

    {{-- TAB 2: Daftar & Saldo Kas Lengkap Per Proyek --}}
    <div class="tab-pane fade" id="project-list" role="tabpanel" aria-labelledby="project-list-tab">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="fs-6 fw-bold mb-0"><i class="fas fa-book me-2 text-primary" aria-hidden="true"></i>Rincian Keuangan & Buku Kas Proyek</h2>
                    <span class="badge bg-light text-dark border">{{ count($projectsData) }} proyek</span>
                </div>
                <div class="small text-secondary">
                    Periode: <strong>{{ $periodTitle }}</strong>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" tabindex="0" role="region" aria-label="Daftar proyek dan rincian kas">
                    <table class="table table-hover align-middle mb-0">
                        <caption class="visually-hidden">Daftar buku kas dan saldo tiap proyek.</caption>
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 40px;">No</th>
                                <th scope="col">Proyek</th>
                                <th scope="col">Klien</th>
                                <th scope="col" class="text-end text-success">Penerimaan (Periode)</th>
                                <th scope="col" class="text-end text-danger">Pengeluaran (Periode)</th>
                                <th scope="col" class="text-end">Selisih (Periode)</th>
                                <th scope="col" class="text-end text-primary">Total Saldo Kumulatif</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Aksi</th>
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
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-break text-dark">{{ $project->nama_proyek }}</div>
                                        <small class="text-body-secondary text-break">{{ $project->alamat_proyek ?: 'Lokasi belum diisi' }}</small>
                                    </td>
                                    <td>
                                        <div class="text-break">{{ $project->user->name ?? 'Klien tidak tersedia' }}</div>
                                        <small class="text-body-secondary text-break">{{ $project->user->email ?? '' }}</small>
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
                                        <span class="badge bg-light text-dark border">{{ ucfirst($project->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.cashbook.show', $project) }}" class="btn btn-sm btn-outline-primary text-nowrap" aria-label="Buka buku kas {{ $project->nama_proyek }}">
                                            <i class="fas fa-book-open me-1" aria-hidden="true"></i> Buka buku kas
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <p class="fw-semibold mb-1">Tidak ada proyek yang sesuai kriteria.</p>
                                        <p class="small text-body-secondary mb-3">Coba kata kunci lain atau reset pencarian.</p>
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

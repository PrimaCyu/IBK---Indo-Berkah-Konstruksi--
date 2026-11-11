@extends('admin.layouts.app')

@section('title', 'Buku kas: ' . $project->nama_proyek)
@section('page-title', 'Buku kas proyek')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.cashbook.index') }}">Keuangan proyek</a></li>
    <li class="breadcrumb-item active" aria-current="page">Buku kas</li>
@endsection

@push('styles')
    <style>
        .cashbook-table { min-width: 78rem; }
        .cashbook-table .cashbook-copy { min-width: 13rem; max-width: 24rem; overflow-wrap: anywhere; white-space: pre-line; }
        .cashbook-number { font-variant-numeric: tabular-nums; white-space: nowrap; }
    </style>
@endpush

@section('content')
    @php
        $codeOptions = $codeOptions ?? \App\Models\CashflowOption::optionsFor('code');
        $unitOptions = $unitOptions ?? \App\Models\CashflowOption::optionsFor('unit');

        // Keep decimal strings exact and format as Rupiah (e.g. 10000.00 -> Rp 10.000, 10000.50 -> Rp 10.000,50).
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

        // Strip trailing zeros for quantity input (e.g. 1.000 -> 1, 1.250 -> 1.25).
        $formatQuantityInput = static function (string $value): string {
            if (! str_contains($value, '.')) {
                return $value;
            }

            return rtrim(rtrim($value, '0'), '.');
        };

        // Format quantity for table/detail display (e.g. 1.000 -> 1, 1.250 -> 1,25).
        $formatQuantityDisplay = static function (string $value): string {
            [$whole, $fraction] = array_pad(explode('.', ltrim($value, '-'), 2), 2, '');
            $whole = ltrim($whole, '0') ?: '0';
            $formattedWhole = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $whole);
            $trimmedFraction = rtrim($fraction, '0');

            return $trimmedFraction === '' ? $formattedWhole : $formattedWhole . ',' . $trimmedFraction;
        };

        $oldCashflowId = old('_cashflow_id');
        $editingTransaction = $oldCashflowId ? $transactions->firstWhere('id', $oldCashflowId) : null;
        $missingTransaction = $oldCashflowId && ! $editingTransaction;

        $oldCode = is_string(old('code')) ? trim(old('code')) : '';
        if ($oldCode !== '' && ! in_array($oldCode, $codeOptions, true)) {
            $codeOptions[] = $oldCode;
        }
        $oldUnit = is_string(old('unit')) ? trim(old('unit')) : '';
        if ($oldUnit !== '' && ! in_array($oldUnit, $unitOptions, true)) {
            $unitOptions[] = $oldUnit;
        }

        $oldUnitPriceRaw = is_string(old('unit_price')) ? trim(old('unit_price')) : '';
        $oldUnitPriceDisplay = $oldUnitPriceRaw !== '' && preg_match('/\A[0-9]+(?:\.[0-9]{1,2})?\z/', $oldUnitPriceRaw)
            ? $formatRupiah($oldUnitPriceRaw)
            : $oldUnitPriceRaw;
    @endphp

    @php
        // Calculate progress analytics for reference-style summary cards
        $incomeByCode = [];
        $expenseByCode = [];
        foreach ($transactions as $t) {
            $codeName = $t->code ?: 'Operasional / Umum';
            $tVal = (float) $t->total;
            if ($t->type === 'penerimaan') {
                $incomeByCode[$codeName] = ($incomeByCode[$codeName] ?? 0) + $tVal;
            } else {
                $expenseByCode[$codeName] = ($expenseByCode[$codeName] ?? 0) + $tVal;
            }
        }
        arsort($incomeByCode);
        arsort($expenseByCode);
        $maxIncomeVal = !empty($incomeByCode) ? max($incomeByCode) : 1;
        $maxExpenseVal = !empty($expenseByCode) ? max($expenseByCode) : 1;

        $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $selectedMonth = (string) ($filters['month'] ?? '');
        $selectedYear = (string) ($filters['year'] ?? '');
        $yearOptions = collect($years ?? [])->push(now()->year)->when($selectedYear !== '', fn ($c) => $c->push((int) $selectedYear))->unique()->sortDesc()->values();
    @endphp

    @php
        // Persentase Keuntungan Proyek
        $incomeFloat = (float) $income;
        $expenseFloat = (float) $expense;
        $balanceFloat = (float) $balance;
        $profitPercentage = $incomeFloat > 0 ? round(($balanceFloat / $incomeFloat) * 100, 1) : 0;
    @endphp

    <!-- Top Project Header Card -->
    <div class="card mb-3">
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <a href="{{ route('admin.cashbook.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Kembali ke Keuangan Proyek
                </a>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h2 class="h5 fw-bold text-dark text-break mb-0">{{ $project->nama_proyek }}</h2>
                    @if($incomeFloat > 0)
                        <span class="badge {{ $profitPercentage >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-2.5 py-1 fw-bold" style="font-size: 0.76rem; border-radius: 9999px;">
                            <i class="fas {{ $profitPercentage >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} me-1"></i> Keuntungan: {{ $profitPercentage }}%
                        </span>
                    @endif
                </div>
                <div class="small text-muted text-break">
                    Klien: <span class="fw-semibold text-dark">{{ $project->user->name ?? 'Klien tidak tersedia' }}</span>
                    @if($project->user?->email) ({{ $project->user->email }}) @endif
                    @if($project->alamat_proyek) &bull; {{ strip_tags((string) $project->alamat_proyek) }} @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" id="cashbook-add" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cashbook-modal">
                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Tambah Transaksi Kas
                </button>
                <a href="{{ route('admin.cashbook.export', $project) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-excel me-1" aria-hidden="true"></i> Unduh (.xlsx)
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Card: 2 Baris Teratur (Baris 1: Periode, Baris 2: Pencarian & Kategori) -->
    <div class="card mb-3">
        <div class="card-body p-3">
            <form id="cashbook-filter-form" method="GET" action="{{ route('admin.cashbook.show', $project) }}">
                <input type="hidden" name="period_mode" id="input-period-mode-show" value="{{ $activeMode ?? 'bulan' }}">

                <!-- Baris 1: Filter Waktu (Fokus & Lega) -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-2.5 mb-2.5 border-bottom">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Periode:</span>
                        <div class="btn-group btn-group-sm period-segmented-control" role="group" aria-label="Pilihan Periode Waktu">
                            <button type="button" class="btn btn-sm {{ ($activeMode ?? 'bulan') === 'lengkap' ? 'btn-primary active text-white' : 'btn-outline-secondary bg-white text-dark' }}" id="btn-period-lengkap-show" onclick="setShowPeriodMode('lengkap')">
                                <i class="fas fa-calendar-week me-1"></i> Lengkap
                            </button>
                            <button type="button" class="btn btn-sm {{ ($activeMode ?? 'bulan') === 'bulan' ? 'btn-primary active text-white' : 'btn-outline-secondary bg-white text-dark' }}" id="btn-period-bulan-show" onclick="setShowPeriodMode('bulan')">
                                <i class="fas fa-calendar-day me-1"></i> Bulan
                            </button>
                            <button type="button" class="btn btn-sm {{ ($activeMode ?? 'bulan') === 'tahun' ? 'btn-primary active text-white' : 'btn-outline-secondary bg-white text-dark' }}" id="btn-period-tahun-show" onclick="setShowPeriodMode('tahun')">
                                <i class="fas fa-calendar-alt me-1"></i> Tahun
                            </button>
                        </div>

                        {{-- Mode Bulan & Tahun --}}
                        <div class="d-inline-flex align-items-center gap-1.5 ms-1" id="filter-col-month">
                            <select id="filter-month" name="month" class="form-select form-select-sm" style="min-width: 135px;">
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" @selected((int)($selectedMonth ?? 0) === (int)$num)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-inline-flex align-items-center gap-1.5" id="filter-col-year">
                            <select id="filter-year" name="year" class="form-select form-select-sm" style="min-width: 95px;">
                                @foreach($yearOptions as $yr)
                                    <option value="{{ $yr }}" @selected((int)($selectedYear ?? 0) === (int)$yr)>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Mode Rentang Tanggal (Lengkap) --}}
                        <div class="d-inline-flex align-items-center gap-1.5 ms-1 d-none" id="filter-col-start-date">
                            <span class="small text-muted">Dari:</span>
                            <input type="date" id="filter-show-start-date" name="start_date" value="{{ $startDate ? (is_string($startDate) ? $startDate : $startDate->format('Y-m-d')) : '' }}" class="form-control form-control-sm" style="width: 140px;" disabled>
                        </div>
                        <div class="d-inline-flex align-items-center gap-1.5 d-none" id="filter-col-end-date">
                            <span class="small text-muted">s/d</span>
                            <input type="date" id="filter-show-end-date" name="end_date" value="{{ $endDate ? (is_string($endDate) ? $endDate : $endDate->format('Y-m-d')) : '' }}" class="form-control form-control-sm" style="width: 140px;" disabled>
                        </div>
                    </div>

                    {{-- Tombol Aksi Terapkan & Reset Periode --}}
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-sm btn-primary px-3 d-flex align-items-center gap-1.5" style="height: 31px;">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>
                        @if(request()->hasAny(['month', 'year', 'status', 'start_date', 'end_date', 'period_mode']))
                            <a href="{{ route('admin.cashbook.show', $project) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center" title="Reset filter" style="height: 31px; width: 31px; padding: 0;">
                                <i class="fas fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Baris 2: Pencarian Cepat & Kategori Transaksi (Proporsional & Seimbang) -->
                <div class="row g-2 align-items-center">
                    {{-- Cari Transaksi --}}
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" id="filter-search-input" class="form-control form-control-sm border-start-0" placeholder="Cari uraian, kode, catatan...">
                        </div>
                    </div>

                    {{-- Tipe --}}
                    <div class="col-6 col-md-2">
                        <select id="filter-type" class="form-select form-select-sm">
>>>>>>> origin/main
                            <option value="">Semua Tipe</option>
                            <option value="penerimaan">Penerimaan (Masuk)</option>
                            <option value="pengeluaran">Pengeluaran (Keluar)</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-6 col-md-2">
                        <select id="filter-status" name="status" class="form-select form-select-sm" onchange="document.getElementById('cashbook-filter-form').submit()">
                            <option value="">Semua Status</option>
                            <option value="lunas" @selected(($statusFilter ?? '') === 'lunas')>Lunas</option>
                            <option value="pending" @selected(($statusFilter ?? '') === 'pending')>Pending</option>
                            <option value="ditolak" @selected(($statusFilter ?? '') === 'ditolak')>Ditolak</option>
                        </select>
                    </div>

                    {{-- Program / Kode --}}
                    <div class="col-12 col-md-4">
                        <select id="filter-code-select" class="form-select form-select-sm">
                            <option value="">Semua Program / Kategori</option>
                            @foreach($codeOptions as $cOpt)
                                <option value="{{ $cOpt }}">{{ $cOpt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Analytics Progress Cards (Reference-Style Side-by-Side) -->
    <div class="row g-3 mb-3">
        <!-- Pemasukan per Program Layanan (Lunas) -->
        <div class="col-md-6">
            <div class="card h-100 mb-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-chart-column text-success" style="font-size: 0.95rem;"></i>
                            <span class="fw-bold text-dark small">Pemasukan</span>
                        </div>
                        <span class="badge-soft-success cashbook-number">+ {{ $formatRupiah($income) }}</span>
                    </div>
                    @php $topIncomes = array_slice($incomeByCode, 0, 4, true); @endphp
                    @forelse($topIncomes as $cName => $amt)
                        @php $pct = min(100, max(12, round(($amt / $maxIncomeVal) * 100))); @endphp
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="text-truncate small text-dark" style="min-width: 140px; max-width: 180px;" title="{{ $cName }}">
                                {{ $cName }}
                            </div>
                            <div class="progress-clean flex-grow-1">
                                <div class="progress-clean-bar emerald" style="width: {{ $pct }}%;"></div>
                            </div>
                            <div class="fw-bold text-dark small text-end cashbook-number" style="min-width: 95px;">
                                {{ number_format($amt, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small py-2">Belum ada rincian pemasukan untuk periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pengeluaran per Kategori (Lunas) -->
        <div class="col-md-6">
            <div class="card h-100 mb-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-pie-chart text-warning" style="font-size: 0.95rem;"></i>
                            <span class="fw-bold text-dark small">Pengeluaran</span>
                        </div>
                        <span class="badge-soft-danger cashbook-number">− {{ $formatRupiah($expense) }}</span>
                    </div>
                    @php $topExpenses = array_slice($expenseByCode, 0, 4, true); @endphp
                    @forelse($topExpenses as $cName => $amt)
                        @php $pct = min(100, max(12, round(($amt / $maxExpenseVal) * 100))); @endphp
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="text-truncate small text-dark" style="min-width: 140px; max-width: 180px;" title="{{ $cName }}">
                                {{ $cName }}
                            </div>
                            <div class="progress-clean flex-grow-1">
                                <div class="progress-clean-bar amber" style="width: {{ $pct }}%;"></div>
                            </div>
                            <div class="fw-bold text-dark small text-end cashbook-number" style="min-width: 95px;">
                                {{ number_format($amt, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small py-2">Belum ada rincian pengeluaran untuk periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @php
        $pendingTxCount = $transactions->where('status', 'pending')->count();
    @endphp
    @if($pendingTxCount > 0)
        <div class="alert alert-warning border-0 shadow-xs py-2.5 px-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-radius: 12px; background-color: #fefce8; border-left: 4px solid #eab308 !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-hourglass-half text-warning fs-5"></i>
                <div>
                    <span class="fw-bold text-dark small">Perhatian: Terdapat {{ $pendingTxCount }} transaksi kas berstatus Menunggu Persetujuan (Pending).</span>
                    <div class="text-muted" style="font-size: 0.75rem;">Transaksi pending belum memotong/menambah saldo kas riil sampai disetujui oleh Manager/Super Admin.</div>
                </div>
            </div>
            <button type="button" class="btn btn-xs btn-outline-warning text-dark fw-bold" onclick="document.getElementById('filter-status').value='pending'; if(typeof applyTableFilters === 'function') applyTableFilters();" style="border-radius: 6px;">
                <i class="fas fa-filter me-1"></i> Tampilkan Transaksi Pending
            </button>
        </div>
    @endif

    <!-- Data Table Card (Matching Reference Screenshot) -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h2 class="h6 fw-bold mb-0 text-dark">Daftar Transaksi</h2>
                <span class="text-muted small" id="trans-count-label">{{ $transactions->count() }} transaksi ditemukan</span>
            </div>
            <div class="small text-muted font-monospace d-flex align-items-center gap-2">
                <span>Saldo Kas: <strong class="text-primary">{{ $formatRupiah($balance) }}</strong></span>
                @if($incomeFloat > 0)
                    <span class="badge {{ $profitPercentage >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-2 py-0.5" style="font-size: 0.72rem; border-radius: 6px;">
                        Laba: {{ $profitPercentage }}%
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" tabindex="0" role="region" aria-label="Transaksi buku kas proyek">
                <table class="table table-hover align-middle mb-0" id="cashbook-trans-table">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 130px;">KODE</th>
                            <th scope="col" style="width: 110px;">TANGGAL</th>
                            <th scope="col" style="width: 100px;">TIPE</th>
                            <th scope="col" style="min-width: 230px;">JUDUL / PENERIMA</th>
                            <th scope="col" style="min-width: 140px;">PROGRAM</th>
                            <th scope="col" class="text-end" style="width: 140px;">NOMINAL</th>
                            <th scope="col" style="width: 120px;">METODE</th>
                            <th scope="col" style="width: 100px;">STATUS</th>
                            <th scope="col" class="text-end" style="min-width: 145px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            @php
                                $isIncome = $transaction->type === 'penerimaan';
                                $cleanQty = $formatQuantityInput($transaction->quantity);
                                $formattedUnitPrice = $formatRupiah($transaction->unit_price);
                                $formattedTotal = ($isIncome ? '+ ' : '− ') . $formatRupiah($transaction->total);
                                $formattedRunningBalance = $formatRupiah($transaction->running_balance);
                                $attachment = $transaction->attachment;
                                $lastEditedText = $transaction->last_edited_at
                                    ? $transaction->last_edited_at->format('d/m/Y H:i') . ($transaction->editor ? ' oleh ' . $transaction->editor->name : '')
                                    : null;
                                $transactionData = [
                                    'id' => (string) $transaction->id,
                                    'action' => route('admin.cashbook.update', [$project, $transaction]),
                                    'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                                    'transaction_date_display' => $transaction->transaction_date->format('d/m/Y'),
                                    'updated_at_display' => $transaction->updated_at?->format('d/m/Y H:i:s') ?? '-',
                                    'last_edited_at_display' => $lastEditedText,
                                    'type' => $transaction->type,
                                    'type_label' => $isIncome ? 'Penerimaan' : 'Pengeluaran',
                                    'code' => $transaction->code,
                                    'description' => $transaction->description,
                                    'unit' => $transaction->unit,
                                    'quantity' => $cleanQty,
                                    'quantity_display' => $formatQuantityDisplay($transaction->quantity),
                                    'unit_price' => $formatQuantityInput($transaction->unit_price),
                                    'unit_price_display' => $formattedUnitPrice,
                                    'total_display' => $formattedTotal,
                                    'running_balance_display' => $formattedRunningBalance,
                                    'notes' => $transaction->notes,
                                    'status' => $transaction->status ?? 'lunas',
                                    'status_label' => match($transaction->status ?? 'lunas') {
                                        'pending' => 'Menunggu Persetujuan',
                                        'ditolak' => 'Ditolak',
                                        default => 'Lunas (Diverifikasi)',
                                    },
                                    'attachment' => $attachment ? [
                                        'id' => $attachment->id,
                                        'url' => $attachment->url,
                                        'original_name' => $attachment->original_name,
                                        'formatted_size' => $attachment->formattedSize(),
                                    ] : null,
                                ];
                                
                                // Standardized code display matching TRX-YYYYMM-XXXX style or custom code
                                $displayCode = $transaction->code ?: ('TRX-' . $transaction->transaction_date->format('Ym') . '-' . str_pad($transaction->id, 4, '0', STR_PAD_LEFT));
                            @endphp
                            <tr class="cashbook-row" data-type="{{ $transaction->type }}" data-status="{{ $transaction->status ?? 'lunas' }}" data-code="{{ strtolower($transaction->code ?? '') }}" data-text="{{ strtolower($transaction->description . ' ' . $transaction->code . ' ' . $transaction->notes . ' ' . $displayCode) }}">
                                <!-- KODE (Crimson Bold Monospace matching reference) -->
                                <td class="text-nowrap">
                                    <span style="color: #b91c1c; font-weight: 700; font-family: ui-monospace, monospace; font-size: 0.8rem; letter-spacing: -0.2px;">
                                        {{ $displayCode }}
                                    </span>
                                </td>

                                <!-- TANGGAL -->
                                <td class="text-nowrap">
                                    <span class="text-secondary small">{{ $transaction->transaction_date->format('d/m/Y') }}</span>
                                    @if($transaction->last_edited_at)
                                        <div class="mt-1">
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle text-wrap text-start" title="Terakhir diedit pada {{ $transaction->last_edited_at->format('d/m/Y H:i:s') }}@if($transaction->editor) oleh {{ $transaction->editor->name }}@endif" style="font-size: 0.72rem; font-weight: 500;">
                                                <i class="fas fa-edit me-1" aria-hidden="true"></i>Last edited: {{ $transaction->last_edited_at->format('d/m/Y H:i') }}
                                                @if($transaction->editor)
                                                    <span class="d-block">{{ $transaction->editor->name }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <!-- TIPE (Soft Badges) -->
                                <td class="text-nowrap">
                                    @if($isIncome)
                                        <span class="badge-soft-success">
                                            <i class="fas fa-arrow-down" style="font-size: 0.65rem;"></i> Masuk
                                        </span>
                                    @else
                                        <span class="badge-soft-danger">
                                            <i class="fas fa-arrow-up" style="font-size: 0.65rem;"></i> Keluar
                                        </span>
                                    @endif
                                </td>

                                <!-- JUDUL / PENERIMA -->
                                <td>
                                    <div class="fw-bold text-dark">{{ $transaction->description }}</div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mt-0.5">
                                        @if($transaction->notes)
                                            <span class="text-muted small" style="font-size: 0.76rem;">{{ $transaction->notes }}</span>
                                        @endif
                                        @if($attachment)
                                            <button type="button" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1 py-0 px-1.5" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#cashbook-photo-modal" data-photo-url="{{ $attachment->url }}" data-photo-title="Foto bukti: {{ $transaction->description }}" data-photo-caption="{{ $attachment->original_name }} ({{ $attachment->formattedSize() }})">
                                                <i class="fas fa-image" aria-hidden="true"></i> Foto bukti
                                            </button>
                                        @endif
                                        @if($transaction->unit || $cleanQty != '1')
                                            <span class="badge bg-light text-muted border py-0 px-1" style="font-size: 0.7rem;">
                                                {{ $formatQuantityDisplay($transaction->quantity) }} {{ $transaction->unit }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- PROGRAM / KODE -->
                                <td>
                                    <span class="text-secondary small">{{ $transaction->code ?: '—' }}</span>
                                </td>

                                <!-- NOMINAL -->
                                <td class="text-end text-nowrap">
                                    @if($isIncome)
                                        <span class="fw-bold text-success cashbook-number">+{{ $formatRupiah($transaction->total) }}</span>
                                    @else
                                        <span class="fw-bold text-danger cashbook-number">−{{ $formatRupiah($transaction->total) }}</span>
                                    @endif
                                </td>

                                <!-- METODE -->
                                <td class="text-nowrap">
                                    <span class="text-secondary small">Transfer Bank</span>
                                </td>

                                <!-- STATUS -->
                                <td class="text-nowrap">
                                    @if(($transaction->status ?? 'lunas') === 'pending')
                                        <span class="badge-soft-warning" title="Menunggu verifikasi Manager / Super Admin">
                                            <i class="fas fa-clock" style="font-size: 0.65rem;"></i> Menunggu
                                        </span>
                                    @elseif(($transaction->status ?? 'lunas') === 'ditolak')
                                        <span class="badge-soft-danger" title="Transaksi ditolak / dibatalkan">
                                            <i class="fas fa-times-circle" style="font-size: 0.65rem;"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="badge-soft-success" title="Transaksi telah diverifikasi (Lunas)">
                                            <i class="fas fa-check" style="font-size: 0.65rem;"></i> Lunas
                                        </span>
                                    @endif
                                </td>

                                <!-- AKSI (Square Icon Buttons matching reference) -->
                                <td class="text-end text-nowrap">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <!-- Print / Invoice Icon -->
                                        <a href="{{ route('admin.cashbook.invoice', [$project, $transaction]) }}" target="_blank" class="btn-action-icon text-decoration-none" title="Cetak / Download Invoice">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <!-- Detail Icon -->
                                        <button type="button" class="btn-action-icon" data-bs-toggle="modal" data-bs-target="#cashbook-detail-modal" data-cashflow="{{ json_encode($transactionData, JSON_THROW_ON_ERROR) }}" title="Lihat detail transaksi">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- Edit Icon -->
                                        <button type="button" class="btn-action-icon" data-bs-toggle="modal" data-bs-target="#cashbook-modal" data-cashflow="{{ json_encode($transactionData, JSON_THROW_ON_ERROR) }}" title="Ubah transaksi">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <!-- Delete Icon -->
                                        <form action="{{ route('admin.cashbook.destroy', [$project, $transaction]) }}" method="POST" class="d-inline m-0" data-cashbook-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon danger" disabled data-cashbook-delete-button title="Hapus transaksi">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 px-3">
                                    <p class="fw-semibold mb-1 text-dark">Belum ada transaksi kas untuk proyek ini.</p>
                                    <p class="small text-secondary mb-3">Tambahkan penerimaan atau pengeluaran pertama untuk mulai mencatat saldo.</p>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#cashbook-modal">
                                        <i class="fas fa-plus me-1"></i> Tambah Transaksi Kas
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <noscript><p class="small text-danger-emphasis">Aktifkan JavaScript untuk menambah, mengubah, atau menghapus transaksi kas.</p></noscript>

    {{-- Modal Detail Transaksi --}}
    <div class="modal fade" id="cashbook-detail-modal" tabindex="-1" aria-labelledby="cashbook-detail-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5 fw-bold" id="cashbook-detail-modal-title">Detail transaksi kas</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup detail transaksi"></button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0 g-2 small">
                        <dt class="col-5 text-secondary">Tanggal transaksi</dt>
                        <dd class="col-7 fw-semibold text-dark mb-1" id="detail-date">-</dd>

                        <dt class="col-5 text-secondary">Jenis transaksi</dt>
                        <dd class="col-7 fw-semibold text-dark mb-1" id="detail-type">-</dd>

                        <dt class="col-5 text-secondary">Kode transaksi</dt>
                        <dd class="col-7 text-dark mb-1" id="detail-code">-</dd>

                        <dt class="col-5 text-secondary">Uraian transaksi</dt>
                        <dd class="col-7 text-dark mb-1 text-break" id="detail-description">-</dd>

                        <dt class="col-5 text-secondary">Satuan</dt>
                        <dd class="col-7 text-dark mb-1" id="detail-unit">-</dd>

                        <dt class="col-5 text-secondary">Kuantitas</dt>
                        <dd class="col-7 text-dark cashbook-number mb-1" id="detail-quantity">-</dd>

                        <dt class="col-5 text-secondary">Harga satuan</dt>
                        <dd class="col-7 fw-semibold text-dark cashbook-number mb-1" id="detail-unit-price">-</dd>

                        <dt class="col-5 text-secondary">Total transaksi</dt>
                        <dd class="col-7 fw-bold text-dark cashbook-number mb-1" id="detail-total">-</dd>

                        <dt class="col-5 text-secondary">Saldo berjalan</dt>
                        <dd class="col-7 fw-bold text-primary cashbook-number mb-1" id="detail-balance">-</dd>

                        <dt class="col-5 text-secondary">Status verifikasi</dt>
                        <dd class="col-7 mb-1" id="detail-status">-</dd>

                        <dt class="col-5 text-secondary">Catatan</dt>
                        <dd class="col-7 text-dark mb-1 text-break" id="detail-notes">-</dd>

                        <dt class="col-5 text-secondary" id="detail-attachment-dt" hidden>Foto bukti / catatan</dt>
                        <dd class="col-7 mb-1" id="detail-attachment-dd" hidden>
                            <div class="card p-1 border shadow-none bg-light" style="max-width: 200px;">
                                <a id="detail-attachment-link" href="#" target="_blank" class="d-block text-center position-relative">
                                    <img id="detail-attachment-img" src="" alt="Foto bukti" class="img-fluid rounded border" style="max-height: 120px; object-fit: contain;">
                                </a>
                                <div class="card-footer p-1 bg-transparent border-0 text-center">
                                    <a id="detail-attachment-btn" href="#" target="_blank" class="btn btn-xs btn-outline-secondary w-100">
                                        <i class="fas fa-external-link-alt me-1"></i>Buka foto penuh
                                    </a>
                                </div>
                            </div>
                        </dd>

                        <dt class="col-5 text-secondary">Terakhir diedit</dt>
                        <dd class="col-7 text-dark mb-0" id="detail-last-edited">-</dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="detail-edit-trigger">
                        <i class="fas fa-pen me-1" aria-hidden="true"></i> Ubah transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah / Ubah Transaksi --}}
    <div class="modal fade" id="cashbook-modal" tabindex="-1" aria-labelledby="cashbook-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
            <form id="cashbook-form" class="modal-content" method="POST" enctype="multipart/form-data" action="{{ $editingTransaction ? route('admin.cashbook.update', [$project, $editingTransaction]) : route('admin.cashbook.store', $project) }}" data-store-url="{{ route('admin.cashbook.store', $project) }}" data-options-url="{{ route('admin.cashbook.options.store', $project) }}" data-today="{{ now()->format('Y-m-d') }}" data-reopen="{{ $errors->any() ? '1' : '0' }}">
                @csrf
                <input type="hidden" id="cashbook-method" name="_method" value="PUT" @disabled(! $editingTransaction)>
                <input type="hidden" id="cashbook-id" name="_cashflow_id" value="{{ old('_cashflow_id') }}">
                <div class="modal-header">
                    <h2 class="modal-title fs-5 fw-bold" id="cashbook-modal-title">{{ $oldCashflowId ? 'Ubah transaksi kas' : 'Tambah transaksi kas' }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup form transaksi"></button>
                </div>
                <div class="modal-body">
                    <div id="cashbook-modal-last-edited" class="alert alert-warning-subtle border border-warning-subtle small py-2 px-3 mb-3 d-flex align-items-center" hidden>
                        <i class="fas fa-history text-warning-emphasis me-2" aria-hidden="true"></i>
                        <span>Terakhir diedit: <strong id="cashbook-modal-last-edited-text"></strong></span>
                    </div>
                    @if($errors->any())
                        <div class="border border-danger rounded bg-danger-subtle text-danger-emphasis p-3 mb-3" role="alert" data-cashbook-validation>
                            <p class="small fw-semibold mb-1">Transaksi belum tersimpan. Periksa isian berikut.</p>
                            <ul class="small mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <p class="small text-danger-emphasis" id="cashbook-missing" @if(! $missingTransaction) hidden @endif>Transaksi yang akan diubah tidak tersedia. Tutup form, lalu pilih transaksi lain atau tambahkan transaksi baru.</p>
                    <p class="small text-secondary mb-3">Kolom bertanda * wajib diisi. Buku kas ini hanya untuk proyek <strong class="text-dark">{{ $project->nama_proyek }}</strong>.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cashbook-date" class="form-label">Tanggal transaksi <span aria-hidden="true">*</span></label>
                            <input type="date" id="cashbook-date" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required aria-describedby="cashbook-date-error" @error('transaction_date') aria-invalid="true" @enderror>
                            <div class="invalid-feedback" id="cashbook-date-error">@error('transaction_date'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label for="cashbook-type" class="form-label">Jenis transaksi <span aria-hidden="true">*</span></label>
                            <select id="cashbook-type" name="type" class="form-select @error('type') is-invalid @enderror" required aria-describedby="cashbook-type-error" @error('type') aria-invalid="true" @enderror>
                                <option value="penerimaan" @selected(old('type', 'penerimaan') === 'penerimaan')>Penerimaan</option>
                                <option value="pengeluaran" @selected(old('type') === 'pengeluaran')>Pengeluaran</option>
                            </select>
                            <div class="invalid-feedback" id="cashbook-type-error">@error('type'){{ $message }}@enderror</div>
                        </div>

                        {{-- Dropdown Dinamis Kode Transaksi --}}
                        <div class="col-md-6">
                            <label for="cashbook-code" class="form-label">Kode transaksi</label>
                            <div class="input-group">
                                <select id="cashbook-code" class="form-select @error('code') is-invalid @enderror" aria-describedby="cashbook-code-help cashbook-code-error" @error('code') aria-invalid="true" @enderror>
                                    <option value="">— Tanpa kode —</option>
                                    @foreach($codeOptions as $codeOption)
                                        <option value="{{ $codeOption }}" @selected($oldCode === $codeOption)>{{ $codeOption }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary" data-option-trigger="code" aria-label="Tambah pilihan kode transaksi baru">
                                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Baru
                                </button>
                            </div>
                            <div class="mt-2" id="cashbook-code-custom-wrap" hidden>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="cashbook-code-custom" class="form-control" maxlength="50" placeholder="Ketik kode baru, mis. OUT-SEWA">
                                    <button type="button" class="btn btn-primary" data-option-save="code">Simpan</button>
                                    <button type="button" class="btn btn-outline-secondary" data-option-cancel="code">Batal</button>
                                </div>
                                <div class="small text-danger mt-1" id="cashbook-code-custom-feedback" hidden></div>
                            </div>
                            <input type="hidden" name="code" id="cashbook-code-value" value="{{ $oldCode }}">
                            <label for="cashbook-code-suffix" class="form-label mt-2">Nomor atau akhiran kode</label>
                            <input type="text" id="cashbook-code-suffix" class="form-control" maxlength="50" placeholder="Contoh: 001">
                            <div class="form-text" id="cashbook-code-help">Pilih prefix dan isi akhiran, misalnya INV dan 001 menjadi INV-001. Kode lama dapat dipilih tanpa akhiran.</div>
                            <div class="invalid-feedback d-block" id="cashbook-code-error">@error('code'){{ $message }}@enderror</div>
                        </div>

                        {{-- Dropdown Dinamis Satuan --}}
                        <div class="col-md-6">
                            <label for="cashbook-unit" class="form-label">Satuan</label>
                            <div class="input-group">
                                <select id="cashbook-unit" name="unit" class="form-select @error('unit') is-invalid @enderror" aria-describedby="cashbook-unit-help cashbook-unit-error" @error('unit') aria-invalid="true" @enderror>
                                    <option value="">— Tanpa satuan —</option>
                                    @foreach($unitOptions as $unitOption)
                                        <option value="{{ $unitOption }}" @selected($oldUnit === $unitOption)>{{ $unitOption }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary" data-option-trigger="unit" aria-label="Tambah pilihan satuan baru">
                                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Baru
                                </button>
                            </div>
                            <div class="mt-2" id="cashbook-unit-custom-wrap" hidden>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="cashbook-unit-custom" class="form-control" maxlength="30" placeholder="Ketik satuan baru, mis. sak, m², paket">
                                    <button type="button" class="btn btn-primary" data-option-save="unit">Simpan</button>
                                    <button type="button" class="btn btn-outline-secondary" data-option-cancel="unit">Batal</button>
                                </div>
                                <div class="small text-danger mt-1" id="cashbook-unit-custom-feedback" hidden></div>
                            </div>
                            <div class="form-text" id="cashbook-unit-help">Pilih satuan dari daftar atau klik <strong>+ Baru</strong> untuk menambahkan pilihan.</div>
                            <div class="invalid-feedback d-block" id="cashbook-unit-error">@error('unit'){{ $message }}@enderror</div>
                        </div>

                        <div class="col-12">
                            <label for="cashbook-description" class="form-label">Uraian transaksi <span aria-hidden="true">*</span></label>
                            <textarea id="cashbook-description" name="description" class="form-control @error('description') is-invalid @enderror" rows="2" required aria-describedby="cashbook-description-error" @error('description') aria-invalid="true" @enderror>{{ old('description') }}</textarea>
                            <div class="invalid-feedback" id="cashbook-description-error">@error('description'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label for="cashbook-quantity" class="form-label">Kuantitas <span aria-hidden="true">*</span></label>
                            <input type="text" inputmode="decimal" id="cashbook-quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ (string) old('quantity', '1') }}" pattern="[0-9]+([.][0-9]{1,3})?" required aria-describedby="cashbook-quantity-help cashbook-quantity-error" @error('quantity') aria-invalid="true" @enderror>
                            <div class="form-text" id="cashbook-quantity-help">Contoh: 1 atau 1.25 (maksimal 3 angka desimal, gunakan titik).</div>
                            <div class="invalid-feedback" id="cashbook-quantity-error">@error('quantity'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label for="cashbook-unit-price-display" class="form-label">Harga satuan (Rp) <span aria-hidden="true">*</span></label>
                            <input type="hidden" id="cashbook-unit-price" name="unit_price" value="{{ $oldUnitPriceRaw }}">
                            <input type="text" inputmode="decimal" id="cashbook-unit-price-display" class="form-control @error('unit_price') is-invalid @enderror" value="{{ $oldUnitPriceDisplay }}" placeholder="Rp 10.000" required aria-describedby="cashbook-unit-price-help cashbook-unit-price-error" @error('unit_price') aria-invalid="true" @enderror>
                            <div class="form-text" id="cashbook-unit-price-help">Otomatis diformat menjadi Rupiah (contoh: ketik 10000 menjadi Rp 10.000). Gunakan koma untuk sen bila perlu.</div>
                            <div class="invalid-feedback" id="cashbook-unit-price-error">@error('unit_price'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-12">
                            <label for="cashbook-total" class="form-label">Pratinjau total (Rp)</label>
                            <input type="text" id="cashbook-total" class="form-control bg-light fw-semibold" value="—" readonly aria-describedby="cashbook-total-help">
                            <div class="form-text" id="cashbook-total-help">Kuantitas × harga satuan, dibulatkan ke 2 angka desimal. Total akhir dan saldo dihitung ulang oleh server saat disimpan.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="cashbook-status" class="form-label">Status Verifikasi <span aria-hidden="true">*</span></label>
                            <select id="cashbook-status" name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="lunas">Lunas (Diverifikasi)</option>
                                <option value="pending" {{ auth()->user()?->role === 'accounting' ? 'selected' : '' }}>Pending (Menunggu Persetujuan Manager)</option>
                                @if(auth()->user()?->role === 'admin')
                                    <option value="ditolak">Ditolak (Dibatalkan)</option>
                                @endif
                            </select>
                            <div class="form-text">Status 'Pending' tidak mengurangi/menambah saldo kas sebelum disetujui Manager.</div>
                            <div class="invalid-feedback">@error('status'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label for="cashbook-notes" class="form-label">Catatan</label>
                            <textarea id="cashbook-notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Tulis catatan transaksi bila ada..." aria-describedby="cashbook-notes-error" @error('notes') aria-invalid="true" @enderror>{{ old('notes') }}</textarea>
                            <div class="invalid-feedback" id="cashbook-notes-error">@error('notes'){{ $message }}@enderror</div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <label class="form-label mb-0 fw-bold text-dark small">
                                    <i class="fas fa-camera text-primary me-1" aria-hidden="true"></i> FOTO BUKTI / NOTA (OPSIONAL)
                                </label>
                                <span class="text-muted" style="font-size: 0.72rem;">Maks. 10 MB (JPG, PNG, WEBP)</span>
                            </div>

                            {{-- 1. FOTO SAAT INI (Tampil saat edit transaksi yang memiliki foto) --}}
                            <div id="cashbook-current-attachment" class="card border p-3 mb-2.5 bg-light d-none" style="border-radius: 12px;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2.5 overflow-hidden">
                                        <img id="cashbook-current-attachment-thumb" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 24 24'%3E%3Cpath fill='%2394a3b8' d='M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z'/%3E%3C/svg%3E" alt="Foto Tersimpan" class="rounded-2 border object-fit-cover shadow-xs" style="width: 48px; height: 48px; background: #fff;">
                                        <div class="text-truncate">
                                            <div class="d-flex align-items-center gap-1.5 mb-0.5">
                                                <span class="badge bg-secondary-subtle text-secondary border py-0.5 px-1.5" style="font-size: 0.65rem;">Tersimpan</span>
                                                <span class="small fw-bold text-dark text-truncate" id="cashbook-current-attachment-name">-</span>
                                            </div>
                                            <a href="#" id="cashbook-current-attachment-link" target="_blank" class="small text-primary text-decoration-none fw-semibold" style="font-size: 0.75rem;">
                                                <i class="fas fa-external-link-alt me-1"></i>Lihat foto saat ini
                                            </a>
                                        </div>
                                    </div>
                                    <div class="form-check p-2 bg-white rounded-2 border border-danger-subtle text-nowrap">
                                        <input class="form-check-input ms-0.5" type="checkbox" name="remove_attachment" id="cashbook-remove-attachment" value="1">
                                        <label class="form-check-label small text-danger fw-semibold ms-1.5" for="cashbook-remove-attachment" style="cursor: pointer; font-size: 0.76rem;">
                                            <i class="fas fa-trash-alt me-1"></i>Hapus foto ini
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. UPLOAD DROPZONE / FILE SELECTOR --}}
                            <div id="cashbook-upload-zone" class="border border-2 border-dashed rounded-3 p-3 text-center bg-white position-relative" style="cursor: pointer; border-color: #cbd5e1 !important; transition: all 0.2s ease;">
                                <input type="file" id="cashbook-attachment" name="attachment" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 @error('attachment') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" style="cursor: pointer; z-index: 2;" aria-describedby="cashbook-attachment-help cashbook-attachment-error">

                                {{-- Idle State --}}
                                <div id="cashbook-upload-idle" class="py-1">
                                    <div class="mx-auto mb-1.5 d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary" style="width: 40px; height: 40px;">
                                        <i class="fas fa-cloud-arrow-up fs-5"></i>
                                    </div>
                                    <div class="fw-semibold text-dark small mb-0.5">
                                        <span class="text-primary text-decoration-underline">Pilih file foto</span> atau seret foto bukti ke sini
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;" id="cashbook-attachment-help">
                                        Pilih foto bukti/nota (JPG, PNG, WEBP, maks 10 MB). Foto akan ditampilkan pada catatan transaksi.
                                    </div>
                                </div>

                                {{-- Preview State (File Baru Dipilih) --}}
                                <div id="cashbook-new-attachment-preview" class="d-none align-items-center justify-content-between p-2 bg-light rounded-3 text-start position-relative" style="z-index: 3;">
                                    <div class="d-flex align-items-center gap-2.5 overflow-hidden">
                                        <img id="cashbook-new-attachment-img" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 24 24'%3E%3Cpath fill='%2394a3b8' d='M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z'/%3E%3C/svg%3E" alt="Foto Baru" class="rounded-2 border object-fit-cover shadow-xs" style="width: 48px; height: 48px; background: #fff;">
                                        <div class="text-truncate">
                                            <div class="d-flex align-items-center gap-1.5 mb-0.5">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle py-0.5 px-1.5" style="font-size: 0.65rem;">Foto Baru</span>
                                                <span class="small fw-bold text-dark text-truncate" id="cashbook-new-attachment-name"></span>
                                            </div>
                                            <span class="badge bg-white text-muted border py-0.5 px-1.5" id="cashbook-new-attachment-size" style="font-size: 0.7rem;"></span>
                                        </div>
                                    </div>
                                    <button type="button" id="cashbook-new-attachment-clear" class="btn btn-sm btn-outline-danger px-2.5 py-1" style="font-size: 0.75rem;">
                                        <i class="fas fa-times me-1"></i>Batal
                                    </button>
                                </div>
                            </div>
                            <div class="invalid-feedback d-block" id="cashbook-attachment-error">@error('attachment'){{ $message }}@enderror</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="cashbook-save" class="btn btn-primary" @disabled($missingTransaction)>{{ $oldCashflowId ? 'Simpan perubahan' : 'Simpan transaksi' }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Pratinjau Foto --}}
    <div class="modal fade" id="cashbook-photo-modal" tabindex="-1" aria-labelledby="cashbook-photo-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5 fw-bold" id="cashbook-photo-modal-title">Foto Bukti Transaksi</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center p-2 bg-light">
                    <img id="cashbook-photo-modal-img" src="" alt="Foto Bukti" class="img-fluid rounded border shadow-sm" style="max-height: 75vh; object-fit: contain;">
                </div>
                <div class="modal-footer justify-content-between">
                    <span class="small text-muted text-truncate me-2" id="cashbook-photo-modal-caption"></span>
                    <div class="d-flex gap-2">
                        <a href="#" id="cashbook-photo-modal-open-btn" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i>Buka ukuran penuh
                        </a>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const modal = document.getElementById('cashbook-modal');
            const detailModal = document.getElementById('cashbook-detail-modal');
            const form = document.getElementById('cashbook-form');
            const method = document.getElementById('cashbook-method');
            const cashflowId = document.getElementById('cashbook-id');
            const title = document.getElementById('cashbook-modal-title');
            const save = document.getElementById('cashbook-save');
            const total = document.getElementById('cashbook-total');
            const priceDisplay = document.getElementById('cashbook-unit-price-display');
            const csrfToken = form.querySelector('input[name="_token"]')?.value || '';
            const photoModal = document.getElementById('cashbook-photo-modal');
            const attachmentInput = document.getElementById('cashbook-attachment');
            const currentAttachmentWrap = document.getElementById('cashbook-current-attachment');
            const currentAttachmentThumb = document.getElementById('cashbook-current-attachment-thumb');
            const currentAttachmentName = document.getElementById('cashbook-current-attachment-name');
            const currentAttachmentLink = document.getElementById('cashbook-current-attachment-link');
            const removeAttachmentCheckbox = document.getElementById('cashbook-remove-attachment');
            const uploadIdle = document.getElementById('cashbook-upload-idle');
            const newAttachmentPreview = document.getElementById('cashbook-new-attachment-preview');
            const newAttachmentImg = document.getElementById('cashbook-new-attachment-img');
            const newAttachmentName = document.getElementById('cashbook-new-attachment-name');
            const newAttachmentSize = document.getElementById('cashbook-new-attachment-size');
            const newAttachmentClear = document.getElementById('cashbook-new-attachment-clear');
            const modalLastEdited = document.getElementById('cashbook-modal-last-edited');
            const modalLastEditedText = document.getElementById('cashbook-modal-last-edited-text');
            const placeholderSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 24 24'%3E%3Cpath fill='%2394a3b8' d='M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z'/%3E%3C/svg%3E";

            function clearNewAttachment() {
                if (attachmentInput) attachmentInput.value = '';
                if (newAttachmentPreview) {
                    newAttachmentPreview.classList.add('d-none');
                    newAttachmentPreview.classList.remove('d-flex');
                }
                if (uploadIdle) uploadIdle.classList.remove('d-none');
                if (newAttachmentImg) newAttachmentImg.src = placeholderSvg;
            }

            newAttachmentClear?.addEventListener('click', event => {
                event.preventDefault();
                event.stopPropagation();
                clearNewAttachment();
            });

            removeAttachmentCheckbox?.addEventListener('change', () => {
                if (currentAttachmentThumb) {
                    currentAttachmentThumb.style.opacity = removeAttachmentCheckbox.checked ? '0.3' : '1';
                    currentAttachmentThumb.style.filter = removeAttachmentCheckbox.checked ? 'grayscale(100%)' : 'none';
                }
            });

            attachmentInput?.addEventListener('change', () => {
                const file = attachmentInput.files?.[0];
                if (!file) {
                    clearNewAttachment();
                    return;
                }
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        if (newAttachmentImg) newAttachmentImg.src = e.target.result;
                        if (newAttachmentName) newAttachmentName.textContent = file.name;
                        if (newAttachmentSize) {
                            const kb = file.size / 1024;
                            newAttachmentSize.textContent = kb >= 1024 ? (kb / 1024).toFixed(2) + ' MB' : kb.toFixed(1) + ' KB';
                        }
                        if (uploadIdle) uploadIdle.classList.add('d-none');
                        if (newAttachmentPreview) {
                            newAttachmentPreview.classList.remove('d-none');
                            newAttachmentPreview.classList.add('d-flex');
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    clearNewAttachment();
                }
            });

            const defaults = {
                transaction_date: form.dataset.today,
                type: 'penerimaan',
                code: '',
                description: '',
                unit: '',
                quantity: '1',
                unit_price: '',
                status: '{{ auth()->user()?->role === 'accounting' ? 'pending' : 'lunas' }}',
                notes: '',
            };
            const fields = Object.fromEntries(Object.keys(defaults).map(name => [name, form.elements.namedItem(name)]));
            fields.code = document.getElementById('cashbook-code');
            const codeValue = document.getElementById('cashbook-code-value');
            const codeSuffix = document.getElementById('cashbook-code-suffix');

            function updateCode() {
                const prefix = fields.code.value;
                const suffix = codeSuffix.value.trim();
                codeValue.value = prefix && suffix ? `${prefix.replace(/-+$/, '')}-${suffix.replace(/^-+/, '')}` : (prefix || suffix);
                codeSuffix.setCustomValidity(codeValue.value.length > 50 ? 'Kode transaksi maksimal 50 karakter.' : '');
            }
            fields.code.addEventListener('change', updateCode);
            codeSuffix.addEventListener('input', updateCode);
            let returnFocusTo = document.getElementById('cashbook-add');
            let currentDetailTransaction = null;

            function ensureSelectOption(selectEl, value) {
                if (!value) return;
                const exists = Array.from(selectEl.options).some(opt => opt.value === value);
                if (!exists) {
                    const opt = document.createElement('option');
                    opt.value = value;
                    opt.textContent = value;
                    selectEl.appendChild(opt);
                }
            }

            function toggleCustomOptionBox(type, show) {
                const wrap = document.getElementById(`cashbook-${type}-custom-wrap`);
                const input = document.getElementById(`cashbook-${type}-custom`);
                const feedback = document.getElementById(`cashbook-${type}-custom-feedback`);
                if (!wrap || !input) return;

                wrap.hidden = !show;
                if (feedback) {
                    feedback.hidden = true;
                    feedback.textContent = '';
                }
                if (show) {
                    input.focus();
                } else {
                    input.value = '';
                }
            }

            async function saveCustomOption(type) {
                const input = document.getElementById(`cashbook-${type}-custom`);
                const feedback = document.getElementById(`cashbook-${type}-custom-feedback`);
                const selectEl = fields[type];
                const rawVal = (input?.value || '').trim();

                if (!rawVal) {
                    if (feedback) {
                        feedback.textContent = 'Masukkan nama pilihan terlebih dahulu.';
                        feedback.hidden = false;
                    }
                    input?.focus();
                    return;
                }

                try {
                    const response = await fetch(form.dataset.optionsUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            option_type: type,
                            option_value: rawVal,
                        }),
                    });

                    if (!response.ok) {
                        const errData = await response.json().catch(() => null);
                        const msg = errData?.errors?.option_value?.[0] || 'Pilihan tidak dapat disimpan.';
                        if (feedback) {
                            feedback.textContent = msg;
                            feedback.hidden = false;
                        }
                        return;
                    }

                    const data = await response.json();
                    const savedVal = data.value || rawVal;
                    ensureSelectOption(selectEl, savedVal);
                    selectEl.value = savedVal;
                    if (type === 'code') updateCode();
                    toggleCustomOptionBox(type, false);
                } catch (_) {
                    if (feedback) {
                        feedback.textContent = 'Gagal menghubungi server. Periksa koneksi Anda.';
                        feedback.hidden = false;
                    }
                }
            }

            ['code', 'unit'].forEach(type => {
                const customInput = document.getElementById(`cashbook-${type}-custom`);

                document.querySelector(`[data-option-trigger="${type}"]`)?.addEventListener('click', () => {
                    const wrap = document.getElementById(`cashbook-${type}-custom-wrap`);
                    toggleCustomOptionBox(type, Boolean(wrap?.hidden));
                });

                document.querySelector(`[data-option-save="${type}"]`)?.addEventListener('click', () => {
                    saveCustomOption(type);
                });

                document.querySelector(`[data-option-cancel="${type}"]`)?.addEventListener('click', () => {
                    toggleCustomOptionBox(type, false);
                });

                customInput?.addEventListener('keydown', event => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        saveCustomOption(type);
                    }
                });
            });

            function parseDisplayToCanonicalAndRupiah(raw) {
                const trimmed = (raw || '').trim();
                if (!trimmed || /^Rp\s*$/i.test(trimmed)) {
                    return { valid: true, canonical: '', display: '' };
                }

                const hadPrefix = /^Rp\s/i.test(trimmed);
                const body = trimmed.replace(/^Rp\s*/i, '');

                if (!hadPrefix && /^\d+\.\d{1,2}$/.test(body)) {
                    const [wholeRaw, fraction] = body.split('.');
                    const whole = wholeRaw.replace(/^0+(?=\d)/, '') || '0';
                    const formattedWhole = whole.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    const cleanFraction = fraction.replace(/0+$/, '');
                    return {
                        valid: true,
                        canonical: cleanFraction ? `${whole}.${cleanFraction}` : whole,
                        display: 'Rp ' + formattedWhole + (cleanFraction ? ',' + fraction : ''),
                    };
                }

                const validPattern = hadPrefix
                    ? /^[0-9.]+(?:,[0-9]{0,2})?$/
                    : /^(?:\d+|\d{1,3}(?:\.\d{3})+)(?:,[0-9]{0,2})?$/;

                if (!validPattern.test(body)) {
                    return { valid: false, canonical: trimmed, display: raw };
                }

                const [wholePartRaw, fraction = null] = body.split(',');
                const wholeDigits = wholePartRaw.replace(/\./g, '');
                if (!wholeDigits) {
                    return { valid: false, canonical: trimmed, display: raw };
                }

                const whole = wholeDigits.replace(/^0+(?=\d)/, '') || '0';
                const formattedWhole = whole.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                const display = 'Rp ' + formattedWhole + (fraction !== null ? ',' + fraction : '');
                const cleanFraction = fraction !== null ? fraction.replace(/0+$/, '') : '';
                const canonical = cleanFraction !== '' ? `${whole}.${cleanFraction}` : whole;

                return { valid: true, canonical, display };
            }

            function canonicalToDisplayRupiah(canonical) {
                const trimmed = (canonical || '').trim();
                if (!trimmed) return '';
                const [wholeRaw, fractionRaw = ''] = trimmed.split('.');
                const whole = (wholeRaw || '').replace(/^0+(?=\d)/, '') || '0';
                const formattedWhole = whole.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                const cleanFraction = fractionRaw.replace(/0+$/, '');

                return 'Rp ' + formattedWhole + (cleanFraction ? ',' + fractionRaw.slice(0, 2) : '');
            }

            function decimalUnits(value, scale) {
                const pattern = scale === 3 ? /^[0-9]+(?:\.[0-9]{1,3})?$/ : /^[0-9]+(?:\.[0-9]{1,2})?$/;
                if (!pattern.test(value)) return null;
                const [whole, fraction = ''] = value.split('.');
                return BigInt(whole) * (10n ** BigInt(scale)) + BigInt(fraction.padEnd(scale, '0'));
            }

            function formatCents(cents) {
                const whole = (cents / 100n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                const rem = cents % 100n;
                return rem === 0n ? whole : whole + ',' + rem.toString().padStart(2, '0');
            }

            function updatePreview() {
                const quantity = decimalUnits(fields.quantity.value, 3);
                const price = decimalUnits(fields.unit_price.value, 2);
                fields.quantity.setCustomValidity(fields.quantity.value !== '' && (quantity === null || quantity <= 0n)
                    ? 'Masukkan kuantitas lebih dari 0 dengan maksimal 3 angka desimal. Gunakan titik sebagai pemisah desimal.' : '');
                priceDisplay.setCustomValidity(priceDisplay.value !== '' && (price === null || price <= 0n)
                    ? 'Masukkan harga satuan yang valid lebih dari Rp 0 (maksimal 2 angka sen setelah koma).' : '');
                if (quantity === null || price === null || quantity <= 0n || price <= 0n) {
                    total.value = '—';
                    return;
                }
                const cents = (quantity * price + 500n) / 1000n;
                total.value = (fields.type.value === 'pengeluaran' ? '− ' : '+ ') + 'Rp ' + formatCents(cents);
            }

            priceDisplay.addEventListener('input', () => {
                const parsed = parseDisplayToCanonicalAndRupiah(priceDisplay.value);
                if (parsed.valid) {
                    priceDisplay.value = parsed.display;
                }
                fields.unit_price.value = parsed.canonical;
                updatePreview();
            });

            form.addEventListener('submit', event => {
                for (const type of ['code', 'unit']) {
                    const wrap = document.getElementById(`cashbook-${type}-custom-wrap`);
                    const input = document.getElementById(`cashbook-${type}-custom`);
                    const feedback = document.getElementById(`cashbook-${type}-custom-feedback`);
                    if (wrap && !wrap.hidden && (input?.value || '').trim() !== '') {
                        event.preventDefault();
                        if (feedback) {
                            feedback.textContent = 'Klik Simpan atau Batal pada pilihan baru sebelum menyimpan transaksi.';
                            feedback.hidden = false;
                        }
                        input?.focus();
                        return;
                    }
                }
            });

            [fields.quantity, fields.type].forEach(field => field.addEventListener('input', updatePreview));

            function populateFormWithTransaction(transaction) {
                form.action = transaction ? transaction.action : form.dataset.storeUrl;
                method.disabled = !transaction;
                cashflowId.value = transaction ? transaction.id : '';
                title.textContent = transaction ? 'Ubah transaksi kas' : 'Tambah transaksi kas';
                save.textContent = transaction ? 'Simpan perubahan' : 'Simpan transaksi';
                save.disabled = false;
                document.getElementById('cashbook-missing').hidden = true;
                form.querySelectorAll('[data-cashbook-validation]').forEach(message => message.hidden = true);

                toggleCustomOptionBox('code', false);
                toggleCustomOptionBox('unit', false);

                if (transaction?.code) {
                    ensureSelectOption(fields.code, transaction.code);
                }
                if (transaction?.unit) {
                    ensureSelectOption(fields.unit, transaction.unit);
                }

                Object.entries(defaults).forEach(([name, defaultValue]) => {
                    const field = fields[name];
                    if (!field) return;
                    field.value = transaction ? (transaction[name] ?? '') : defaultValue;
                    field.classList.remove('is-invalid');
                    field.removeAttribute('aria-invalid');
                });

                codeSuffix.value = '';
                updateCode();
                priceDisplay.value = transaction ? canonicalToDisplayRupiah(transaction.unit_price) : '';
                priceDisplay.classList.remove('is-invalid');
                priceDisplay.removeAttribute('aria-invalid');
                updatePreview();

                clearNewAttachment();
                if (removeAttachmentCheckbox) removeAttachmentCheckbox.checked = false;
                if (currentAttachmentThumb) {
                    currentAttachmentThumb.style.opacity = '1';
                    currentAttachmentThumb.style.filter = 'none';
                }

                if (transaction?.attachment) {
                    if (currentAttachmentWrap) {
                        currentAttachmentWrap.classList.remove('d-none');
                        currentAttachmentWrap.hidden = false;
                    }
                    if (currentAttachmentThumb) currentAttachmentThumb.src = transaction.attachment.url;
                    if (currentAttachmentName) currentAttachmentName.textContent = transaction.attachment.original_name + (transaction.attachment.formatted_size ? ' (' + transaction.attachment.formatted_size + ')' : '');
                    if (currentAttachmentLink) currentAttachmentLink.href = transaction.attachment.url;
                } else {
                    if (currentAttachmentWrap) {
                        currentAttachmentWrap.classList.add('d-none');
                        currentAttachmentWrap.hidden = true;
                    }
                    if (currentAttachmentThumb) currentAttachmentThumb.src = placeholderSvg;
                }

                if (transaction?.last_edited_at_display) {
                    if (modalLastEdited) modalLastEdited.hidden = false;
                    if (modalLastEditedText) modalLastEditedText.textContent = transaction.last_edited_at_display;
                } else {
                    if (modalLastEdited) modalLastEdited.hidden = true;
                }
            }

            modal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                if (!button) return;
                returnFocusTo = button;
                const transaction = button.dataset.cashflow ? JSON.parse(button.dataset.cashflow) : null;
                populateFormWithTransaction(transaction);
            });

            modal.addEventListener('shown.bs.modal', () => {
                (form.querySelector('.is-invalid') || fields.transaction_date).focus();
            });

            modal.addEventListener('hidden.bs.modal', () => {
                returnFocusTo?.focus();
            });

            if (photoModal) {
                photoModal.addEventListener('show.bs.modal', event => {
                    const btn = event.relatedTarget;
                    if (!btn) return;
                    const url = btn.dataset.photoUrl || '';
                    const titleText = btn.dataset.photoTitle || 'Foto Bukti Transaksi';
                    const caption = btn.dataset.photoCaption || '';

                    const img = document.getElementById('cashbook-photo-modal-img');
                    const titleEl = document.getElementById('cashbook-photo-modal-title');
                    const capEl = document.getElementById('cashbook-photo-modal-caption');
                    const openBtn = document.getElementById('cashbook-photo-modal-open-btn');

                    if (img) img.src = url;
                    if (titleEl) titleEl.textContent = titleText;
                    if (capEl) capEl.textContent = caption;
                    if (openBtn) openBtn.href = url;
                });
            }

            if (detailModal) {
                detailModal.addEventListener('show.bs.modal', event => {
                    const button = event.relatedTarget;
                    if (!button || !button.dataset.cashflow) return;
                    returnFocusTo = button;
                    const tx = JSON.parse(button.dataset.cashflow);
                    currentDetailTransaction = tx;

                    document.getElementById('detail-date').textContent = tx.transaction_date_display || tx.transaction_date || '-';
                    document.getElementById('detail-type').textContent = tx.type_label || tx.type || '-';
                    document.getElementById('detail-code').textContent = tx.code || '-';
                    document.getElementById('detail-description').textContent = tx.description || '-';
                    document.getElementById('detail-unit').textContent = tx.unit || '-';
                    document.getElementById('detail-quantity').textContent = tx.quantity_display || tx.quantity || '-';
                    document.getElementById('detail-unit-price').textContent = tx.unit_price_display || '-';
                    document.getElementById('detail-total').textContent = tx.total_display || '-';
                    document.getElementById('detail-balance').textContent = tx.running_balance_display || '-';
                    const statusEl = document.getElementById('detail-status');
                    if (statusEl) {
                        statusEl.innerHTML = tx.status === 'pending'
                            ? '<span class="badge-soft-warning"><i class="fas fa-clock me-1"></i>Menunggu Persetujuan Manager</span>'
                            : (tx.status === 'ditolak'
                                ? '<span class="badge-soft-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>'
                                : '<span class="badge-soft-success"><i class="fas fa-check me-1"></i>Lunas (Diverifikasi)</span>');
                    }
                    document.getElementById('detail-notes').textContent = tx.notes || '-';
                    document.getElementById('detail-last-edited').textContent = tx.last_edited_at_display || 'Belum pernah diedit';

                    const attDt = document.getElementById('detail-attachment-dt');
                    const attDd = document.getElementById('detail-attachment-dd');
                    const attImg = document.getElementById('detail-attachment-img');
                    const attLink = document.getElementById('detail-attachment-link');
                    const attBtn = document.getElementById('detail-attachment-btn');

                    if (tx.attachment) {
                        if (attDt) attDt.hidden = false;
                        if (attDd) attDd.hidden = false;
                        if (attImg) attImg.src = tx.attachment.url;
                        if (attLink) attLink.href = tx.attachment.url;
                        if (attBtn) attBtn.href = tx.attachment.url;
                    } else {
                        if (attDt) attDt.hidden = true;
                        if (attDd) attDd.hidden = true;
                    }
                });

                document.getElementById('detail-edit-trigger')?.addEventListener('click', () => {
                    if (!currentDetailTransaction) return;
                    const txToEdit = currentDetailTransaction;
                    detailModal.addEventListener('hidden.bs.modal', () => {
                        populateFormWithTransaction(txToEdit);
                        bootstrap.Modal.getOrCreateInstance(modal).show();
                    }, { once: true });
                    bootstrap.Modal.getOrCreateInstance(detailModal).hide();
                });
            }

            document.querySelectorAll('[data-cashbook-delete]').forEach(deleteForm => {
                deleteForm.addEventListener('submit', event => {
                    if (!window.confirm('Hapus transaksi kas ini? Saldo proyek akan dihitung ulang. Tindakan ini tidak dapat dibatalkan.')) event.preventDefault();
                });
                deleteForm.querySelector('[data-cashbook-delete-button]').disabled = false;
            });

            // Instant client-side filtering matching reference filter controls
            const filterType = document.getElementById('filter-type');
            const filterStatus = document.getElementById('filter-status');
            const filterCode = document.getElementById('filter-code-select');
            const filterSearch = document.getElementById('filter-search-input');
            const transRows = document.querySelectorAll('.cashbook-row');
            const transCounter = document.getElementById('trans-count-label');

            function applyTableFilters() {
                const selectedType = filterType ? filterType.value.toLowerCase() : '';
                const selectedStatus = filterStatus ? filterStatus.value.toLowerCase() : '';
                const selectedCode = filterCode ? filterCode.value.toLowerCase() : '';
                const query = filterSearch ? filterSearch.value.trim().toLowerCase() : '';

                let visibleCount = 0;
                transRows.forEach(row => {
                    const rowType = (row.dataset.type || '').toLowerCase();
                    const rowStatus = (row.dataset.status || 'lunas').toLowerCase();
                    const rowCode = (row.dataset.code || '').toLowerCase();
                    const rowText = (row.dataset.text || '').toLowerCase();

                    const matchType = !selectedType || rowType === selectedType;
                    const matchStatus = !selectedStatus || rowStatus === selectedStatus;
                    const matchCode = !selectedCode || rowCode === selectedCode || rowCode.includes(selectedCode);
                    const matchQuery = !query || rowText.includes(query);

                    if (matchType && matchStatus && matchCode && matchQuery) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (transCounter) {
                    transCounter.textContent = `${visibleCount} transaksi ditemukan`;
                }
            }

            if (filterType) filterType.addEventListener('change', applyTableFilters);
            if (filterStatus) filterStatus.addEventListener('change', applyTableFilters);
            if (filterCode) filterCode.addEventListener('change', applyTableFilters);
            if (filterSearch) filterSearch.addEventListener('input', applyTableFilters);

            updatePreview();
            if (form.dataset.reopen === '1') bootstrap.Modal.getOrCreateInstance(modal).show();
        })();
    </script>
    <script>
        function setShowPeriodMode(mode) {
            const inputMode = document.getElementById('input-period-mode-show');
            if (inputMode) inputMode.value = mode;

            const btnLengkap = document.getElementById('btn-period-lengkap-show');
            const btnBulan = document.getElementById('btn-period-bulan-show');
            const btnTahun = document.getElementById('btn-period-tahun-show');

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
            const inputStartDate = document.getElementById('filter-show-start-date');
            const inputEndDate = document.getElementById('filter-show-end-date');

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
            const initialMode = document.getElementById('input-period-mode-show')?.value || 'bulan';
            setShowPeriodMode(initialMode);
        });
    </script>
@endpush

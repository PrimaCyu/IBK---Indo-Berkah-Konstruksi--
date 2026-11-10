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

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
        <div>
            <a href="{{ route('admin.cashbook.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Kembali ke keuangan proyek
            </a>
            <h2 class="h5 fw-bold text-break mb-1">{{ $project->nama_proyek }}</h2>
            <p class="small text-secondary mb-1 text-break">Klien: <span class="fw-semibold text-dark">{{ $project->user->name ?? 'Klien tidak tersedia' }}</span>@if($project->user?->email) ({{ $project->user->email }})@endif</p>
            @if($project->alamat_proyek)
                <p class="small text-secondary mb-1 text-break">{{ $project->alamat_proyek }}</p>
            @endif
            <p class="small text-secondary mb-0">Pencatatan internal proyek, tidak ditampilkan di portal klien.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" id="cashbook-add" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cashbook-modal">
                <i class="fas fa-plus me-1" aria-hidden="true"></i> Tambah transaksi kas
            </button>
            <a href="{{ route('admin.cashbook.export', $project) }}" class="btn btn-outline-secondary">
                <i class="fas fa-file-excel me-1" aria-hidden="true"></i> Unduh semua transaksi (.xlsx)
            </a>
        </div>
    </div>

    <dl class="row g-3 mb-3" aria-label="Ringkasan buku kas proyek">
        <div class="col-md-4">
            <div class="card h-100 mb-0 border-start border-3 border-success">
                <div class="card-body p-3">
                    <dt class="small fw-semibold text-dark mb-2"><i class="fas fa-arrow-down me-2" aria-hidden="true"></i>Total penerimaan periode</dt>
                    <dd class="h5 fw-bold text-dark text-break mb-0">+ {{ $formatRupiah($income) }}</dd>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 mb-0 border-start border-3 border-danger">
                <div class="card-body p-3">
                    <dt class="small fw-semibold text-dark mb-2"><i class="fas fa-arrow-up me-2" aria-hidden="true"></i>Total pengeluaran periode</dt>
                    <dd class="h5 fw-bold text-dark text-break mb-0">− {{ $formatRupiah($expense) }}</dd>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 mb-0 border-start border-3 border-primary">
                <div class="card-body p-3">
                    <dt class="small fw-semibold text-dark mb-2"><i class="fas fa-wallet me-2" aria-hidden="true"></i>Selisih periode</dt>
                    <dd class="h5 fw-bold text-dark text-break mb-0">{{ $formatRupiah($balance) }}</dd>
                </div>
            </div>
        </div>
    </dl>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <h2 class="h6 fw-bold mb-0">Transaksi kas</h2>
                <span class="badge bg-light text-dark border">{{ $transactions->count() }} transaksi</span>
            </div>
            <form method="GET" action="{{ route('admin.cashbook.show', $project) }}" class="d-flex align-items-center flex-wrap gap-2" aria-label="Filter periode transaksi">
                @php
                    $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                    $selectedMonth = (string) ($filters['month'] ?? '');
                    $selectedYear = (string) ($filters['year'] ?? '');
                    $yearOptions = collect($years ?? [])->push(now()->year)->when($selectedYear !== '', fn ($c) => $c->push((int) $selectedYear))->unique()->sortDesc()->values();
                @endphp
                <label for="filter-month" class="visually-hidden">Bulan transaksi</label>
                <select id="filter-month" name="month" class="form-select form-select-sm w-auto">
                    <option value="">Semua bulan</option>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" @selected($selectedMonth === (string) $num)>{{ $name }}</option>
                    @endforeach
                </select>
                <label for="filter-year" class="visually-hidden">Tahun transaksi</label>
                <select id="filter-year" name="year" class="form-select form-select-sm w-auto">
                    <option value="">Semua tahun</option>
                    @foreach($yearOptions as $yr)
                        <option value="{{ $yr }}" @selected($selectedYear === (string) $yr)>{{ $yr }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                @if($selectedMonth !== '' || $selectedYear !== '')
                    <a href="{{ route('admin.cashbook.show', $project) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <p class="small text-secondary px-3 pt-3 mb-2" id="cashbook-table-help">Diurutkan dari tanggal terlama. Tanda + berarti penerimaan, tanda − berarti pengeluaran. Saldo berjalan merupakan saldo kumulatif proyek; kartu di atas merangkum transaksi pada periode yang dipilih. Geser tabel untuk melihat semua kolom.</p>
            <div class="table-responsive" tabindex="0" role="region" aria-label="Transaksi buku kas proyek" aria-describedby="cashbook-table-help">
                <table class="table table-hover cashbook-table mb-0">
                    <caption class="visually-hidden">Buku kas {{ $project->nama_proyek }}. Harga, total, dan saldo dalam rupiah. Saldo berjalan dihitung oleh server setelah setiap transaksi.</caption>
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Jenis</th>
                            <th scope="col">Kode</th>
                            <th scope="col">Uraian</th>
                            <th scope="col">Satuan</th>
                            <th scope="col" class="text-end">Kuantitas</th>
                            <th scope="col" class="text-end">Harga satuan (Rp)</th>
                            <th scope="col" class="text-end">Total (Rp)</th>
                            <th scope="col" class="text-end">Saldo berjalan (Rp)</th>
                            <th scope="col">Catatan</th>
                            <th scope="col" class="text-end">Aksi</th>
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
                                    'attachment' => $attachment ? [
                                        'id' => $attachment->id,
                                        'url' => $attachment->url,
                                        'original_name' => $attachment->original_name,
                                        'formatted_size' => $attachment->formattedSize(),
                                    ] : null,
                                ];
                            @endphp
                            <tr>
                                <td class="text-nowrap">
                                    <time datetime="{{ $transaction->transaction_date->format('Y-m-d') }}">{{ $transaction->transaction_date->format('d/m/Y') }}</time>
                                    @if($transaction->last_edited_at)
                                        <div class="mt-1">
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle text-wrap text-start" title="Terakhir diedit pada {{ $transaction->last_edited_at->format('d/m/Y H:i:s') }}@if($transaction->editor) oleh {{ $transaction->editor->name }}@endif" style="font-size: 0.72rem; font-weight: 500;">
                                                <i class="fas fa-edit me-1" aria-hidden="true"></i>Last edited: {{ $transaction->last_edited_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas {{ $isIncome ? 'fa-arrow-down' : 'fa-arrow-up' }} me-1" aria-hidden="true"></i>{{ $isIncome ? 'Penerimaan' : 'Pengeluaran' }}
                                    </span>
                                </td>
                                <td class="text-break">{{ $transaction->code ?: '-' }}</td>
                                <td class="cashbook-copy">{{ $transaction->description }}</td>
                                <td class="text-break">{{ $transaction->unit ?: '-' }}</td>
                                <td class="text-end cashbook-number">{{ $formatQuantityDisplay($transaction->quantity) }}</td>
                                <td class="text-end cashbook-number">{{ $formattedUnitPrice }}</td>
                                <td class="text-end cashbook-number fw-semibold">{{ $formattedTotal }}</td>
                                <td class="text-end cashbook-number fw-semibold">{{ $formattedRunningBalance }}</td>
                                <td class="cashbook-copy">
                                    @if($transaction->notes)
                                        <div>{{ $transaction->notes }}</div>
                                    @endif
                                    @if($attachment)
                                        <div class="{{ $transaction->notes ? 'mt-1' : '' }}">
                                            <button type="button" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1 text-nowrap" data-bs-toggle="modal" data-bs-target="#cashbook-photo-modal" data-photo-url="{{ $attachment->url }}" data-photo-title="Foto bukti: {{ $transaction->description }}" data-photo-caption="{{ $attachment->original_name }} ({{ $attachment->formattedSize() }})">
                                                <i class="fas fa-image" aria-hidden="true"></i>
                                                <span>Foto bukti</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if(! $transaction->notes && ! $attachment)
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cashbook-detail-modal" data-cashflow="{{ json_encode($transactionData, JSON_THROW_ON_ERROR) }}" aria-label="Detail transaksi {{ $transaction->description }}">
                                        <i class="fas fa-eye me-1" aria-hidden="true"></i> Detail
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cashbook-modal" data-cashflow="{{ json_encode($transactionData, JSON_THROW_ON_ERROR) }}" aria-label="Ubah transaksi {{ $transaction->description }}">
                                        <i class="fas fa-pen me-1" aria-hidden="true"></i> Ubah
                                    </button>
                                    <form action="{{ route('admin.cashbook.destroy', [$project, $transaction]) }}" method="POST" class="d-inline" data-cashbook-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Hapus transaksi {{ $transaction->description }}" disabled data-cashbook-delete-button>
                                            <i class="fas fa-trash me-1" aria-hidden="true"></i> Hapus
                                        </button>
                                    </form>
                                    @if($transaction->last_edited_at)
                                        <div class="text-end text-muted mt-1 small" style="font-size: 0.72rem;" title="Terakhir diedit pada {{ $transaction->last_edited_at->format('d/m/Y H:i:s') }}@if($transaction->editor) oleh {{ $transaction->editor->name }}@endif">
                                            <i class="fas fa-history me-1" aria-hidden="true"></i>Last edited: <span class="fw-semibold text-dark">{{ $transaction->last_edited_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 px-3">
                                    <p class="fw-semibold mb-1">Belum ada transaksi kas untuk proyek ini.</p>
                                    <p class="small text-secondary mb-3">Tambahkan penerimaan atau pengeluaran pertama untuk mulai mencatat saldo.</p>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#cashbook-modal">Tambah transaksi kas</button>
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
                        <div class="col-12">
                            <label for="cashbook-notes" class="form-label">Catatan</label>
                            <textarea id="cashbook-notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Tulis catatan transaksi bila ada..." aria-describedby="cashbook-notes-error" @error('notes') aria-invalid="true" @enderror>{{ old('notes') }}</textarea>
                            <div class="invalid-feedback" id="cashbook-notes-error">@error('notes'){{ $message }}@enderror</div>
                        </div>

                        <div class="col-12">
                            <label for="cashbook-attachment" class="form-label d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold"><i class="fas fa-camera text-primary me-1" aria-hidden="true"></i> Foto bukti / catatan (opsional)</span>
                                <span class="small text-muted">Bisa gunakan foto atau catatan teks di atas</span>
                            </label>

                            {{-- Existing attachment in Edit mode --}}
                            <div id="cashbook-current-attachment" class="p-2 mb-2 border rounded bg-light d-flex align-items-center justify-content-between" hidden>
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <img id="cashbook-current-attachment-thumb" src="" alt="Thumbnail" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div class="text-truncate">
                                        <div class="small fw-semibold text-truncate" id="cashbook-current-attachment-name">-</div>
                                        <a href="#" id="cashbook-current-attachment-link" target="_blank" class="small text-primary text-decoration-none">
                                            <i class="fas fa-external-link-alt me-1"></i>Lihat foto saat ini
                                        </a>
                                    </div>
                                </div>
                                <div class="form-check ms-3 text-nowrap">
                                    <input class="form-check-input" type="checkbox" name="remove_attachment" id="cashbook-remove-attachment" value="1">
                                    <label class="form-check-label small text-danger fw-semibold" for="cashbook-remove-attachment">
                                        Hapus foto
                                    </label>
                                </div>
                            </div>

                            {{-- File input --}}
                            <input type="file" id="cashbook-attachment" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" aria-describedby="cashbook-attachment-help cashbook-attachment-error" @error('attachment') aria-invalid="true" @enderror>
                            <div class="form-text" id="cashbook-attachment-help">Pilih foto bukti/nota (JPG, PNG, WEBP, maks 10 MB). Foto akan ditampilkan pada catatan transaksi.</div>
                            <div class="invalid-feedback" id="cashbook-attachment-error">@error('attachment'){{ $message }}@enderror</div>

                            {{-- Preview for newly selected file --}}
                            <div id="cashbook-new-attachment-preview" class="mt-2 p-2 border rounded bg-white d-flex align-items-center justify-content-between" hidden>
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <img id="cashbook-new-attachment-img" src="" alt="Preview baru" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div class="text-truncate">
                                        <div class="small fw-semibold text-truncate" id="cashbook-new-attachment-name"></div>
                                        <div class="small text-muted" id="cashbook-new-attachment-size" style="font-size: 0.75rem;"></div>
                                    </div>
                                </div>
                                <button type="button" id="cashbook-new-attachment-clear" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-times me-1"></i>Batal pilih
                                </button>
                            </div>
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
            const newAttachmentPreview = document.getElementById('cashbook-new-attachment-preview');
            const newAttachmentImg = document.getElementById('cashbook-new-attachment-img');
            const newAttachmentName = document.getElementById('cashbook-new-attachment-name');
            const newAttachmentSize = document.getElementById('cashbook-new-attachment-size');
            const newAttachmentClear = document.getElementById('cashbook-new-attachment-clear');
            const modalLastEdited = document.getElementById('cashbook-modal-last-edited');
            const modalLastEditedText = document.getElementById('cashbook-modal-last-edited-text');

            function clearNewAttachment() {
                if (attachmentInput) attachmentInput.value = '';
                if (newAttachmentPreview) newAttachmentPreview.hidden = true;
                if (newAttachmentImg) newAttachmentImg.src = '';
            }

            newAttachmentClear?.addEventListener('click', clearNewAttachment);

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
                        if (newAttachmentPreview) newAttachmentPreview.hidden = false;
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

                if (transaction?.attachment) {
                    if (currentAttachmentWrap) currentAttachmentWrap.hidden = false;
                    if (currentAttachmentThumb) currentAttachmentThumb.src = transaction.attachment.url;
                    if (currentAttachmentName) currentAttachmentName.textContent = transaction.attachment.original_name + (transaction.attachment.formatted_size ? ' (' + transaction.attachment.formatted_size + ')' : '');
                    if (currentAttachmentLink) currentAttachmentLink.href = transaction.attachment.url;
                } else {
                    if (currentAttachmentWrap) currentAttachmentWrap.hidden = true;
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

            updatePreview();
            if (form.dataset.reopen === '1') bootstrap.Modal.getOrCreateInstance(modal).show();
        })();
    </script>
@endpush

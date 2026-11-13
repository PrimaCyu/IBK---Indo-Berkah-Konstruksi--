<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Kas Proyek - {{ $project->nama_proyek }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #111827;
            background: #f3f4f6;
        }
        .toolbar {
            max-width: 1120px;
            margin: 0 auto 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #111827;
        }
        .btn-primary {
            background: #111827;
            border-color: #111827;
            color: #ffffff;
        }
        .sheet {
            max-width: 1120px;
            margin: 0 auto;
            background: #ffffff;
            padding: 24px 28px;
            border: 1px solid #e5e7eb;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #111827;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .company-name {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
            margin-top: 2px;
        }
        .meta {
            text-align: right;
            font-size: 11px;
            color: #374151;
        }
        .project-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 8px 16px;
            margin-bottom: 14px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .info-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-value {
            font-size: 11.5px;
            font-weight: 600;
            color: #111827;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(140px, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }
        .summary-box {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
        }
        .summary-box .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .summary-box .val {
            font-size: 13px;
            font-weight: 700;
            margin-top: 2px;
            font-variant-numeric: tabular-nums;
        }
        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-variant-numeric: tabular-nums;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: left;
            color: #111827;
            white-space: nowrap;
        }
        td {
            font-size: 10.5px;
        }
        .num {
            text-align: right;
            white-space: nowrap;
        }
        .center {
            text-align: center;
            white-space: nowrap;
        }
        .nowrap {
            white-space: nowrap;
        }
        tfoot td {
            font-weight: 700;
            background: #f9fafb;
        }
        .signatures {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 28px;
            page-break-inside: avoid;
        }
        .sig-box {
            text-align: center;
            font-size: 11px;
        }
        .sig-line {
            margin: 54px auto 4px;
            width: 200px;
            border-top: 1px solid #111827;
        }
        @media (max-width: 767px) {
            body {
                padding: 10px;
            }
            .sheet {
                padding: 14px;
            }
            .meta {
                text-align: left;
            }
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .signatures {
                grid-template-columns: 1fr;
                gap: 24px;
            }
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .toolbar {
                display: none !important;
            }
            .sheet {
                border: none;
                padding: 0;
                max-width: none;
            }
            .table-wrap {
                overflow: visible;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ route('admin.cashbook.show', $project) }}" class="btn">Kembali ke Buku Kas</a>
        <button type="button" onclick="window.print()" class="btn btn-primary">Cetak / Simpan PDF</button>
    </div>

    <div class="sheet">
        <div class="header">
            <div>
                <div class="company-name">Indo Berkah Konstruksi</div>
                <div class="doc-title">Laporan Buku Kas Proyek</div>
            </div>
            <div class="meta">
                <div><strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}</div>
                <div><strong>Dicetak oleh:</strong> {{ auth()->user()?->name ?? '-' }}</div>
            </div>
        </div>

        <div class="project-info">
            <div>
                <div class="info-label">Nama Proyek</div>
                <div class="info-value">{{ $project->nama_proyek }}</div>
            </div>
            <div>
                <div class="info-label">Klien</div>
                <div class="info-value">{{ $project->client_name ?: ($project->user?->name ?? '-') }}</div>
            </div>
            <div>
                <div class="info-label">Lokasi</div>
                <div class="info-value">{{ $project->alamat_proyek ? strip_tags((string) $project->alamat_proyek) : '-' }}</div>
            </div>
            <div>
                <div class="info-label">Jumlah Transaksi</div>
                <div class="info-value">{{ $ledger['transactions']->count() }} transaksi</div>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-box">
                <div class="label">Total Pemasukan</div>
                <div class="val">Rp {{ number_format((float) $ledger['income'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Total Pengeluaran</div>
                <div class="val">Rp {{ number_format((float) $ledger['expense'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Piutang / Pending</div>
                <div class="val">Rp {{ number_format((float) ($ledger['receivable'] ?? 0), 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Saldo Akhir</div>
                <div class="val">Rp {{ number_format((float) $ledger['balance'], 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="center" style="width: 34px;">No</th>
                        <th class="nowrap" style="width: 78px;">Tanggal</th>
                        <th class="nowrap" style="width: 74px;">Kode</th>
                        <th>Uraian</th>
                        <th class="nowrap" style="width: 74px;">Vol / Sat</th>
                        <th class="num" style="width: 105px;">Masuk</th>
                        <th class="num" style="width: 105px;">Keluar</th>
                        <th class="center" style="width: 70px;">Status</th>
                        <th class="num" style="width: 110px;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ledger['transactions'] as $index => $transaction)
                        @php
                            $isIncome = $transaction->type === 'penerimaan';
                            $statusLabel = match($transaction->status ?? 'lunas') {
                                'pending', 'piutang' => 'Piutang',
                                'ditolak' => 'Ditolak',
                                default => 'Lunas',
                            };
                        @endphp
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td class="nowrap">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                            <td class="nowrap">{{ $transaction->code ?: '-' }}</td>
                            <td>
                                {{ $transaction->description }}
                                @if($transaction->notes)
                                    <div style="color: #6b7280; font-size: 9.5px;">{{ $transaction->notes }}</div>
                                @endif
                            </td>
                            <td class="nowrap">{{ rtrim(rtrim(number_format((float) $transaction->quantity, 3, ',', '.'), '0'), ',') }} {{ $transaction->unit ?: '' }}</td>
                            <td class="num">{{ $isIncome ? 'Rp ' . number_format((float) $transaction->total, 0, ',', '.') : '-' }}</td>
                            <td class="num">{{ ! $isIncome ? 'Rp ' . number_format((float) $transaction->total, 0, ',', '.') : '-' }}</td>
                            <td class="center">{{ $statusLabel }}</td>
                            <td class="num">Rp {{ number_format((float) $transaction->running_balance, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="center" style="padding: 18px; color: #6b7280;">Belum ada transaksi kas pada proyek ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: right;">TOTAL</td>
                        <td class="num">Rp {{ number_format((float) $ledger['income'], 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format((float) $ledger['expense'], 0, ',', '.') }}</td>
                        <td class="center">-</td>
                        <td class="num">Rp {{ number_format((float) $ledger['balance'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="signatures">
            <div class="sig-box">
                <div>Dibuat oleh,</div>
                <div class="sig-line"></div>
                <div>Bagian Keuangan / Accounting</div>
            </div>
            <div class="sig-box">
                <div>Disetujui oleh,</div>
                <div class="sig-line"></div>
                <div>Pimpinan Proyek / Super Admin</div>
            </div>
        </div>
    </div>
</body>
</html>

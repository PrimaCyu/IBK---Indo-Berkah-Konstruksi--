<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $cashflow->transaction_number ?? ('TRX-' . str_pad($cashflow->id, 5, '0', STR_PAD_LEFT)) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0f172a;
            --primary-accent: #2563eb;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
            --success: #16a34a;
            --success-bg: #dcfce7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: var(--text-main);
            padding: 40px 20px;
            -webkit-font-smoothing: antialiased;
        }

        .action-bar {
            max-width: 800px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: var(--primary-accent);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: var(--text-main);
            border-color: var(--border-color);
        }

        .btn-secondary:hover {
            background-color: var(--bg-light);
        }

        .invoice-card {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            padding: 48px;
            position: relative;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 28px;
            margin-bottom: 32px;
        }

        .company-brand h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.02em;
            text-transform: uppercase;
        }

        .company-brand p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 4px;
            line-height: 1.5;
        }

        .invoice-title-block {
            text-align: right;
        }

        .invoice-badge-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .invoice-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--primary-accent);
            margin-top: 4px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background-color: var(--success-bg);
            color: var(--success);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .invoice-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 36px;
        }

        .meta-col h3 {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }

        .meta-col p {
            font-size: 0.925rem;
            color: var(--text-main);
            font-weight: 600;
            line-height: 1.5;
        }

        .meta-col .sub-info {
            font-size: 0.825rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .table-container {
            margin-bottom: 32px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: var(--bg-light);
            padding: 14px 18px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 16px 18px;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .td-nominal {
            text-align: right;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            font-size: 0.95rem;
            white-space: nowrap;
        }

        td, th {
            white-space: nowrap;
        }

        td:nth-child(2), th:nth-child(2) {
            white-space: normal;
        }

        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }

        .total-box {
            width: 320px;
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px 24px;
        }

        .total-box .total-row {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            font-size: 0.875rem;
            margin-bottom: 10px;
            color: var(--text-muted);
        }

        .total-box .total-row.grand-total {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px dashed var(--border-color);
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0;
            white-space: nowrap;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }

        .total-row.grand-total span:last-child {
            font-family: 'JetBrains Mono', monospace;
            color: var(--primary-accent);
        }

        .invoice-footer {
            border-top: 1px solid var(--border-color);
            padding-top: 28px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-bottom: 1px solid var(--text-main);
            height: 60px;
            margin-bottom: 8px;
        }

        .notes-box {
            max-width: 480px;
        }

        .notes-box h4 {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .notes-box p {
            font-size: 0.825rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .action-bar {
                display: none !important;
            }
            .invoice-card {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <a href="{{ route('admin.cashbook.show', $project) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Buku Kas
        </a>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="invoice-card">
        <!-- HEADER -->
        <div class="invoice-header">
            <div class="company-brand">
                <h1>PT Indo Berkah Konstruksi</h1>
                <p>
                    Jasa Konstruksi, Renovasi, & Interior Desain<br>
                    Website: indoberkah.com | Email: admin@indoberkah.com
                </p>
            </div>
            <div class="invoice-title-block">
                <div class="invoice-badge-title">KUITANSI RESMI</div>
                <div class="invoice-number">{{ $cashflow->transaction_number ?? ('TRX-' . str_pad($cashflow->id, 5, '0', STR_PAD_LEFT)) }}</div>
                <div>
                    <span class="status-pill">
                        <i class="fas fa-check-circle"></i> Lunas
                    </span>
                </div>
            </div>
        </div>

        <!-- INFO GRID -->
        <div class="invoice-meta-grid">
            <div class="meta-col">
                <h3>{{ $cashflow->type === 'penerimaan' ? 'DITERIMA DARI / KLIEN' : 'DIBAYARKAN KEPADA' }}</h3>
                <p>{{ $project->user->name ?? ($project->nama_klien ?? 'Klien Proyek') }}</p>
                <div class="sub-info">
                    Proyek: <strong>{{ $project->nama_proyek ?? $project->name }}</strong><br>
                    Lokasi: {{ $project->alamat_proyek ?? ($project->location ?? '-') }}
                </div>
            </div>
            <div class="meta-col" style="text-align: right;">
                <h3>RINCIAN TRANSAKSI</h3>
                <p>Tanggal Transaksi: {{ $cashflow->transaction_date ? $cashflow->transaction_date->translatedFormat('d F Y') : '-' }}</p>
                <div class="sub-info">
                    Metode: <strong>{{ $cashflow->payment_method ?? 'Transfer Bank' }}</strong><br>
                    Jenis: <strong style="color: {{ $cashflow->type === 'penerimaan' ? 'var(--success)' : '#e11d48' }};">{{ $cashflow->type === 'penerimaan' ? 'Pemasukan / Termin' : 'Pengeluaran Biaya' }}</strong>
                </div>
            </div>
        </div>

        <!-- TABLE OF ITEMS -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 45px;">NO</th>
                        <th>URAIAN TRANSAKSI</th>
                        <th style="width: 100px; text-align: center;">VOLUME</th>
                        <th style="width: 140px; text-align: right;">HARGA SATUAN</th>
                        <th style="width: 160px; text-align: right;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; color: var(--text-muted);">1</td>
                        <td>
                            <strong style="color: var(--primary);">{{ $cashflow->description }}</strong>
                            @if($cashflow->notes)
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">
                                    Catatan: {{ $cashflow->notes }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            {{ $cashflow->quantity_millis ? rtrim(rtrim(number_format($cashflow->quantity_millis / 1000, 3, ',', '.'), '0'), ',') : '1' }}
                            <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $cashflow->unit ?: 'ls' }}</span>
                        </td>
                        <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">
                            @php
                                $unitPrice = ($cashflow->unit_price_cents ?? 0) / 100;
                            @endphp
                            Rp {{ number_format($unitPrice, 0, ',', '.') }}
                        </td>
                        <td class="td-nominal">
                            @php
                                $total = (($cashflow->quantity_millis ?? 1000) / 1000) * $unitPrice;
                            @endphp
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- TOTAL SECTION -->
        <div class="total-section">
            <div class="total-box">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Pajak (PPN):</span>
                    <span>Rp 0</span>
                </div>
                <div class="total-row grand-total" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <span>TOTAL DIBAYAR:</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- FOOTER & SIGNATURE -->
        <div class="invoice-footer">
            <div class="notes-box">
                <h4>KETERANGAN</h4>
                <p>
                    Bukti pembayaran ini sah dan diterbitkan secara digital oleh sistem PT Indo Berkah Konstruksi.
                    Simpan kuitansi ini sebagai bukti transaksi resmi proyek.
                </p>
            </div>
            <div class="signature-box">
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Denpasar, {{ $cashflow->transaction_date ? $cashflow->transaction_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</div>
                <div style="font-size: 0.85rem; font-weight: 700; color: var(--primary);">Bagian Keuangan</div>
                <div class="signature-line"></div>
                <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-main);">PT Indo Berkah Konstruksi</div>
            </div>
        </div>
    </div>

</body>
</html>

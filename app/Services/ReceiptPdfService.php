<?php

namespace App\Services;

use App\Models\ProjectCashflow;
use App\Models\ReceiptSequence;
use App\Support\Terbilang;

class ReceiptPdfService
{
    /**
     * Build a standards-compliant A4 PDF binary for the given transaction receipt.
     */
    public function generatePdf(ProjectCashflow $cashflow): string
    {
        $data = $this->receiptPayload($cashflow);

        return $this->compilePdfDocument($data);
    }

    /**
     * Prepare normalized receipt metadata for both PDF and print views.
     *
     * @return array<string, string>
     */
    public function receiptPayload(ProjectCashflow $cashflow): array
    {
        $cashflow->loadMissing(['project.user', 'category', 'creator', 'editor']);

        $dirty = false;
        if (empty($cashflow->transaction_number)) {
            $cashflow->transaction_number = ReceiptSequence::nextNumber('TRX', $cashflow->transaction_date?->format('Y-m-d'));
            $dirty = true;
        }
        if (empty($cashflow->receipt_number)) {
            $cashflow->receipt_number = ReceiptSequence::nextNumber('KWT', $cashflow->transaction_date?->format('Y-m-d'));
            $dirty = true;
        }
        if ($dirty) {
            $cashflow->saveQuietly();
        }

        $isIncome = $cashflow->type === 'penerimaan';
        $project = $cashflow->project;
        $projectName = $project?->nama_proyek ?? '-';
        $projectCode = $project?->kode_proyek ?: ($project ? sprintf('PRJ-%04d', $project->id) : '-');
        $categoryName = $cashflow->category?->name ?? ($isIncome ? 'Pembayaran Klien' : 'Operasional Proyek');
        $paymentMethod = $cashflow->payment_method ?: 'Transfer Bank';
        $creatorName = $cashflow->creator?->name ?? auth()->user()?->name ?? 'Administrator Keuangan';
        $nominalFormatted = Terbilang::formatRupiah($cashflow->total, true);
        $terbilangText = Terbilang::rupiah($cashflow->total);

        return [
            'company_name' => 'PT INDO BERKAH KONSTRUKSI',
            'company_tagline' => 'Jasa Konstruksi, Arsitektur & Sipil Terpadu',
            'company_logo_mark' => 'IBK OFFICIAL RECEIPT LOGO',
            'receipt_number' => (string) $cashflow->receipt_number,
            'transaction_number' => (string) $cashflow->transaction_number,
            'transaction_date' => $cashflow->transaction_date ? $cashflow->transaction_date->format('d/m/Y') : '-',
            'transaction_date_iso' => $cashflow->transaction_date ? $cashflow->transaction_date->format('Y-m-d') : '-',
            'type_label' => $isIncome ? 'Pemasukan (Penerimaan Kas)' : 'Pengeluaran (Kas Keluar)',
            'project_name' => $projectName,
            'project_code' => $projectCode,
            'client_name' => $project?->user?->name ?? '-',
            'transaction_name' => (string) $cashflow->description,
            'category_name' => $categoryName,
            'nominal_rupiah' => $nominalFormatted,
            'terbilang' => $terbilangText,
            'payment_method' => $paymentMethod,
            'notes' => $cashflow->notes ? (string) $cashflow->notes : '-',
            'creator_name' => $creatorName,
            'printed_at' => now()->format('d/m/Y H:i'),
        ];
    }

    /**
     * Compile a clean A4 Portrait PDF 1.4 document with vector boxes, typography, and corporate layout.
     *
     * @param  array<string, string>  $d
     */
    private function compilePdfDocument(array $d): string
    {
        $lines = [];

        // Dark Navy Header Banner
        $lines[] = '0.07 0.13 0.24 rg';
        $lines[] = '40 735 515 68 re f';

        // Brand Accent Gold/Amber Strip
        $lines[] = '0.85 0.65 0.20 rg';
        $lines[] = '40 731 515 4 re f';

        // Logo Emblem Box inside Header
        $lines[] = '0.15 0.39 0.92 rg';
        $lines[] = '54 749 42 42 re f';
        $lines[] = $this->pdfText(62, 765, 'IBK', 'F2', 14, [1, 1, 1]);

        // Company Name & Subtitle
        $lines[] = $this->pdfText(108, 777, $d['company_name'], 'F2', 15, [1, 1, 1]);
        $lines[] = $this->pdfText(108, 761, $d['company_tagline'], 'F1', 9, [0.82, 0.87, 0.95]);
        $lines[] = $this->pdfText(108, 748, 'Logo: '.$d['company_logo_mark'], 'F1', 7.5, [0.68, 0.76, 0.88]);

        // Receipt Title Right-Aligned in Header
        $lines[] = $this->pdfText(400, 777, 'KUITANSI TRANSAKSI', 'F2', 12, [1, 1, 1]);
        $lines[] = $this->pdfText(400, 761, 'No: '.$d['receipt_number'], 'F3', 9.5, [0.98, 0.82, 0.35]);
        $lines[] = $this->pdfText(400, 748, 'Ref: '.$d['transaction_number'], 'F3', 8, [0.82, 0.87, 0.95]);

        // Outer Receipt Frame
        $lines[] = '0.82 0.85 0.90 RG 1 w';
        $lines[] = '40 245 515 486 re S';

        // Metadata Band
        $lines[] = '0.97 0.98 0.99 rg';
        $lines[] = '41 675 513 55 re f';
        $lines[] = '0.88 0.90 0.94 RG 0.75 w';
        $lines[] = '40 675 m 555 675 l S';

        $lines[] = $this->pdfText(56, 710, 'Nomor Kuitansi', 'F2', 8.5, [0.35, 0.40, 0.48]);
        $lines[] = $this->pdfText(56, 695, $d['receipt_number'], 'F3', 10.5, [0.06, 0.09, 0.16]);

        $lines[] = $this->pdfText(225, 710, 'Tanggal Transaksi', 'F2', 8.5, [0.35, 0.40, 0.48]);
        $lines[] = $this->pdfText(225, 695, $d['transaction_date'].' ('.$d['transaction_date_iso'].')', 'F1', 10, [0.06, 0.09, 0.16]);

        $lines[] = $this->pdfText(400, 710, 'Jenis Transaksi', 'F2', 8.5, [0.35, 0.40, 0.48]);
        $lines[] = $this->pdfText(400, 695, $d['type_label'], 'F2', 9.5, [0.06, 0.09, 0.16]);

        // Detail Rows
        $rows = [
            ['Nama Proyek', $d['project_name'].' ('.$d['project_code'].')'],
            ['Nomor Transaksi', $d['transaction_number']],
            ['Nama Transaksi', $this->truncate($d['transaction_name'], 78)],
            ['Kategori Transaksi', $d['category_name']],
            ['Metode Pembayaran', $d['payment_method']],
            ['Keterangan / Catatan', $this->truncate($d['notes'], 78)],
            ['Dibuat Oleh', $d['creator_name']],
        ];

        $y = 648;
        foreach ($rows as [$label, $value]) {
            $lines[] = $this->pdfText(56, $y, $label, 'F2', 9.5, [0.28, 0.33, 0.41]);
            $lines[] = $this->pdfText(185, $y, ':  '.$value, 'F1', 9.5, [0.06, 0.09, 0.16]);
            $lines[] = '0.92 0.94 0.96 RG 0.5 w';
            $lines[] = sprintf('56 %d m 539 %d l S', $y - 8, $y - 8);
            $y -= 26;
        }

        // Nominal Highlight Box
        $lines[] = '0.94 0.97 1.00 rg';
        $lines[] = '56 402 483 52 re f';
        $lines[] = '0.16 0.47 0.84 RG 1.2 w';
        $lines[] = '56 402 483 52 re S';

        $lines[] = $this->pdfText(70, 436, 'NOMINAL TRANSAKSI (RUPIAH)', 'F2', 8.5, [0.16, 0.47, 0.84]);
        $lines[] = $this->pdfText(70, 414, $d['nominal_rupiah'], 'F2', 16, [0.05, 0.12, 0.25]);

        // Terbilang Box
        $lines[] = '0.98 0.98 0.97 rg';
        $lines[] = '56 346 483 44 re f';
        $lines[] = '0.84 0.86 0.89 RG 0.8 w';
        $lines[] = '56 346 483 44 re S';

        $lines[] = $this->pdfText(70, 374, 'TERBILANG:', 'F2', 8, [0.40, 0.44, 0.50]);
        $lines[] = $this->pdfText(70, 358, '# '.$this->truncate($d['terbilang'], 86).' #', 'F2', 10, [0.10, 0.14, 0.20]);

        // Signature and Verification Block
        $lines[] = $this->pdfText(56, 315, 'Dokumen kuitansi ini diterbitkan secara otomatis oleh Sistem Keuangan Proyek', 'F1', 8.5, [0.40, 0.45, 0.52]);
        $lines[] = $this->pdfText(56, 302, $d['company_name'].' pada '.$d['printed_at'].'.', 'F1', 8.5, [0.40, 0.45, 0.52]);

        $lines[] = $this->pdfText(380, 315, 'Pembuat Kuitansi / Otorisasi,', 'F2', 9, [0.20, 0.25, 0.33]);
        $lines[] = '0.65 0.70 0.78 RG 0.8 w';
        $lines[] = '380 268 m 525 268 l S';
        $lines[] = $this->pdfText(380, 255, $d['creator_name'], 'F2', 9.5, [0.06, 0.09, 0.16]);

        $stream = implode("\n", $lines);

        return $this->assemblePdfObjects($stream);
    }

    /**
     * @param  array{0: float, 1: float, 2: float}  $rgb
     */
    private function pdfText(float $x, float $y, string $text, string $font, float $size, array $rgb): string
    {
        $escaped = $this->escapePdfText($text);

        return sprintf(
            'BT /%s %.2f Tf %.2f %.2f %.2f rg %.2f %.2f Td (%s) Tj ET',
            $font,
            $size,
            $rgb[0],
            $rgb[1],
            $rgb[2],
            $x,
            $y,
            $escaped
        );
    }

    private function escapePdfText(string $text): string
    {
        $clean = preg_replace('/[\r\n\t]+/', ' ', $text) ?? '';
        $ascii = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $clean);
        if ($ascii !== false && $ascii !== '') {
            $clean = $ascii;
        }

        return strtr($clean, [
            '\\' => '\\\\',
            '(' => '\(',
            ')' => '\)',
        ]);
    }

    private function truncate(string $value, int $limit): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $value) ?? '');
        if (mb_strlen($clean) <= $limit) {
            return $clean;
        }

        return mb_substr($clean, 0, $limit - 3).'...';
    }

    private function assemblePdfObjects(string $contentStream): string
    {
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R /F3 6 0 R >> >> /Contents 7 0 R >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            5 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
            6 => '<< /Type /Font /Subtype /Type1 /BaseFont /Courier-Bold /Encoding /WinAnsiEncoding >>',
            7 => sprintf("<< /Length %d >>\nstream\n%s\nendstream", strlen($contentStream), $contentStream),
        ];

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0 => 0];

        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$body}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 {$count}\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size {$count} /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }
}

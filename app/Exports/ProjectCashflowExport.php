<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\ProjectCashflow;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Cell\NumericCell;
use OpenSpout\Common\Entity\Cell\StringCell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ProjectCashflowExport
{
    public function download(Project $project): BinaryFileResponse
    {
        $ledger = ProjectCashflow::ledger($project);
        $options = new Options;
        $options->SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY = false;
        $options->DEFAULT_ROW_STYLE = (new Style)->setFontName('Calibri')->setFontSize(11)->setShouldWrapText();
        $options->setColumnWidth(14, 1, 2, 3);
        $options->setColumnWidth(48, 4);
        $options->setColumnWidth(12, 5, 6);
        $options->setColumnWidth(22, 7, 8, 9);
        $options->setColumnWidth(40, 10);
        foreach (range(1, 4) as $row) {
            $options->mergeCells(0, $row, 9, $row);
        }

        $heading = (new Style)->setFontBold()->setFontSize(14)->setCellAlignment(CellAlignment::CENTER);
        $header = (new Style)->setFontBold()->setBackgroundColor('E7E6E6');
        $money = (new Style)->setFormat('#,##0.00');
        $quantity = (new Style)->setFormat('#,##0.000');
        $text = (new Style)->setFormat('@');
        $writer = new Writer($options);
        $path = tempnam(sys_get_temp_dir(), 'ibk-cashbook-');
        if ($path === false) {
            throw new RuntimeException('Tidak dapat membuat berkas sementara laporan.');
        }

        // ponytail: Excel supports 15 significant digits; preserve cents as text above that ceiling. Use a decimal-aware format if it grows.
        $originalPrecision = ini_set('precision', '15');
        try {
            if ($originalPrecision === false) {
                throw new RuntimeException('Presisi angka ekspor tidak dapat diatur.');
            }
            $writer->openToFile($path);
            $writer->getCurrentSheet()->setName('Buku Kas Proyek');
            foreach ([
                'INDO BERKAH KONSTRUKSI',
                'BUKU KAS PROYEK',
                'Proyek: '.$project->nama_proyek,
                'Tanggal laporan: '.now()->format('d-m-Y'),
            ] as $title) {
                $writer->addRow(new Row([new StringCell($title, $text)], $heading));
            }
            $writer->addRow(new Row([]));
            $writer->addRow(new Row(array_map(
                fn (string $value): StringCell => new StringCell($value, $text),
                ['Tanggal', 'Jenis', 'Kode', 'Uraian', 'Satuan', 'Jumlah', 'Harga Satuan', 'Total', 'Saldo', 'Keterangan'],
            ), $header));

            foreach ($ledger['transactions'] as $transaction) {
                $writer->addRow(new Row([
                    new StringCell($transaction->transaction_date->format('Y-m-d'), $text),
                    new StringCell($transaction->type === 'penerimaan' ? 'Penerimaan' : 'Pengeluaran', $text),
                    new StringCell($transaction->code ?? '-', $text),
                    new StringCell($transaction->description, $text),
                    new StringCell($transaction->unit ?? '-', $text),
                    $this->numberCell($transaction->quantity, $quantity, $text),
                    $this->numberCell($transaction->unit_price, $money, $text),
                    $this->numberCell($transaction->total, $money, $text),
                    $this->numberCell($transaction->running_balance, $money, $text),
                    new StringCell($transaction->notes ?? '', $text),
                ]));
            }

            $writer->addRow(new Row([]));
            foreach (['income' => 'Total penerimaan', 'expense' => 'Total pengeluaran', 'balance' => 'Saldo akhir'] as $key => $label) {
                $writer->addRow(new Row([
                    new StringCell($label, $text),
                    $this->numberCell($ledger[$key], $money, $text),
                ], (new Style)->setFontBold()));
            }
            $writer->close();
            clearstatcache(true, $path);

            return response()->download($path, 'buku-kas-proyek-'.$project->getKey().'.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'private, no-store',
            ])->deleteFileAfterSend(true);
        } catch (Throwable $exception) {
            try {
                $writer->close();
            } catch (Throwable) {
                // Preserve the original failure while removing the partial report.
            }
            if (is_file($path)) {
                unlink($path);
            }
            throw $exception;
        } finally {
            if ($originalPrecision !== false) {
                ini_set('precision', $originalPrecision);
            }
        }
    }

    private function numberCell(string $value, Style $numericStyle, Style $textStyle): Cell
    {
        if (strlen(ltrim(str_replace(['-', '.'], '', $value), '0')) > 15) {
            return new StringCell($value, $textStyle);
        }

        return new NumericCell((float) $value, $numericStyle);
    }
}

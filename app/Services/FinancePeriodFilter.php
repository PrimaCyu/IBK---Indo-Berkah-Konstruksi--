<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FinancePeriodFilter
{
    public const PRESETS = [
        'all' => 'Semua Periode',
        'today' => 'Hari Ini',
        'yesterday' => 'Kemarin',
        'last_7_days' => '7 Hari Terakhir',
        'this_month' => 'Bulan Ini',
        'last_month' => 'Bulan Lalu',
        'this_year' => 'Tahun Ini',
        'last_year' => 'Tahun Lalu',
        'last_2_years' => '2 Tahun Terakhir',
        'last_3_years' => '3 Tahun Terakhir',
        'custom' => 'Rentang Tanggal Kustom',
    ];

    public const PAYMENT_METHODS = [
        'Transfer Bank' => 'Transfer Bank',
        'Tunai' => 'Tunai (Cash)',
        'QRIS' => 'QRIS / E-Wallet',
        'Cek / Giro' => 'Cek / Bilyet Giro',
        'Virtual Account' => 'Virtual Account',
        'Lainnya' => 'Lainnya',
    ];

    /**
     * Resolve start and end dates (Y-m-d) for a given period preset.
     *
     * @return array{period: string, start_date: ?string, end_date: ?string, label: string}
     */
    public static function resolveRange(?string $period, ?string $customStart = null, ?string $customEnd = null, ?Carbon $reference = null): array
    {
        $now = ($reference ? $reference->copy() : now())->startOfDay();
        $period = is_string($period) && array_key_exists($period, self::PRESETS) ? $period : 'all';

        $validStart = self::sanitizeDate($customStart);
        $validEnd = self::sanitizeDate($customEnd);

        if ($period === 'all' && ($validStart !== null || $validEnd !== null)) {
            $period = 'custom';
        }

        [$start, $end] = match ($period) {
            'today' => [$now->format('Y-m-d'), $now->format('Y-m-d')],
            'yesterday' => [$now->copy()->subDay()->format('Y-m-d'), $now->copy()->subDay()->format('Y-m-d')],
            'last_7_days' => [$now->copy()->subDays(6)->format('Y-m-d'), $now->format('Y-m-d')],
            'this_month' => [$now->copy()->startOfMonth()->format('Y-m-d'), $now->copy()->endOfMonth()->format('Y-m-d')],
            'last_month' => [
                $now->copy()->subMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
                $now->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d'),
            ],
            'this_year' => [$now->copy()->startOfYear()->format('Y-m-d'), $now->copy()->endOfYear()->format('Y-m-d')],
            'last_year' => [
                $now->copy()->subYear()->startOfYear()->format('Y-m-d'),
                $now->copy()->subYear()->endOfYear()->format('Y-m-d'),
            ],
            'last_2_years' => [
                $now->copy()->subYears(2)->format('Y-m-d'),
                $now->format('Y-m-d'),
            ],
            'last_3_years' => [
                $now->copy()->subYears(3)->format('Y-m-d'),
                $now->format('Y-m-d'),
            ],
            'custom' => [$validStart, $validEnd],
            default => [null, null],
        };

        if ($start !== null && $end !== null && $start > $end) {
            [$start, $end] = [$end, $start];
        }

        return [
            'period' => $period,
            'start_date' => $start,
            'end_date' => $end,
            'label' => self::PRESETS[$period] ?? 'Semua Periode',
        ];
    }

    /**
     * Apply period and combined dimension filters onto a ProjectCashflow query builder.
     */
    public static function apply(Builder $query, Request $request, string $table = 'project_cashflows'): array
    {
        $range = self::resolveRange(
            is_string($request->query('period')) ? $request->query('period') : null,
            is_string($request->query('start_date')) ? $request->query('start_date') : null,
            is_string($request->query('end_date')) ? $request->query('end_date') : null,
        );

        if ($range['start_date'] !== null) {
            $query->whereDate("{$table}.transaction_date", '>=', $range['start_date']);
        }

        if ($range['end_date'] !== null) {
            $query->whereDate("{$table}.transaction_date", '<=', $range['end_date']);
        }

        $projectId = $request->query('project_id');
        if (is_scalar($projectId) && trim((string) $projectId) !== '') {
            $query->where("{$table}.project_id", (int) $projectId);
        }

        $type = $request->query('type');
        if (is_string($type) && in_array($type, ['penerimaan', 'pengeluaran', 'pemasukan'], true)) {
            $normalizedType = $type === 'pemasukan' ? 'penerimaan' : $type;
            $query->where("{$table}.type", $normalizedType);
        }

        $categoryId = $request->query('category_id');
        if (is_scalar($categoryId) && trim((string) $categoryId) !== '') {
            $query->where("{$table}.category_id", (int) $categoryId);
        }

        $paymentMethod = is_string($request->query('payment_method')) ? trim($request->query('payment_method')) : '';
        if ($paymentMethod !== '') {
            $query->where("{$table}.payment_method", $paymentMethod);
        }

        $search = is_string($request->query('search')) ? trim($request->query('search')) : '';
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search, $table): void {
                $q->where("{$table}.transaction_number", 'like', "%{$search}%")
                    ->orWhere("{$table}.receipt_number", 'like', "%{$search}%")
                    ->orWhere("{$table}.code", 'like', "%{$search}%")
                    ->orWhere("{$table}.description", 'like', "%{$search}%")
                    ->orWhere("{$table}.notes", 'like', "%{$search}%")
                    ->orWhereHas('project', function (Builder $pq) use ($search): void {
                        $pq->where('nama_proyek', 'like', "%{$search}%")
                            ->orWhere('kode_proyek', 'like', "%{$search}%");
                    });
            });
        }

        return $range + [
            'project_id' => is_scalar($projectId) && trim((string) $projectId) !== '' ? (string) $projectId : '',
            'type' => is_string($type) ? ($type === 'pemasukan' ? 'penerimaan' : $type) : '',
            'category_id' => is_scalar($categoryId) && trim((string) $categoryId) !== '' ? (string) $categoryId : '',
            'payment_method' => $paymentMethod,
            'search' => $search,
        ];
    }

    private static function sanitizeDate(?string $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $value = trim($value);
        if (! preg_match('/\A\d{4}-\d{2}-\d{2}\z/', $value)) {
            return null;
        }
        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $value);

            return $parsed && $parsed->format('Y-m-d') === $value ? $value : null;
        } catch (\Throwable) {
            return null;
        }
    }
}

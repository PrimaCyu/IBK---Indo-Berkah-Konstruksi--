<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReceiptSequence extends Model
{
    protected $fillable = [
        'sequence_key',
        'last_number',
    ];

    protected function casts(): array
    {
        return [
            'last_number' => 'integer',
        ];
    }

    public static function nextNumber(string $prefix, ?string $date = null): string
    {
        $dateCompact = $date ? preg_replace('/[^0-9]/', '', substr($date, 0, 10)) : now()->format('Ymd');
        if (strlen((string) $dateCompact) !== 8) {
            $dateCompact = now()->format('Ymd');
        }

        $sequenceKey = strtoupper($prefix).'-'.$dateCompact;
        $column = strtoupper($prefix) === 'KWT' ? 'receipt_number' : 'transaction_number';

        if (! Schema::hasTable('receipt_sequences')) {
            return $sequenceKey.'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        return DB::transaction(function () use ($sequenceKey, $column): string {
            $sequence = static::query()->where('sequence_key', $sequenceKey)->lockForUpdate()->first();
            if (! $sequence) {
                $sequence = static::query()->create([
                    'sequence_key' => $sequenceKey,
                    'last_number' => 0,
                ]);
            }

            do {
                $sequence->last_number++;
                $candidate = sprintf('%s-%04d', $sequenceKey, $sequence->last_number);
                $exists = Schema::hasColumn('project_cashflows', $column)
                    && ProjectCashflow::withTrashed()->where($column, $candidate)->exists();
            } while ($exists);

            $sequence->save();

            return $candidate;
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class CashflowOption extends Model
{
    protected $fillable = ['type', 'value', 'normalized_hash'];

    /** @return list<string> */
    public static function optionsFor(string $type): array
    {
        if (! in_array($type, ['code', 'unit'], true)) {
            throw new InvalidArgumentException('Jenis pilihan tidak valid.');
        }

        $stored = Schema::hasTable('cashflow_options')
            ? static::query()->where('type', $type)->orderBy('value')->pluck('value')->all()
            : [];
        $historical = ProjectCashflow::withTrashed()->whereNotNull($type)->where($type, '!=', '')
            ->distinct()->orderBy($type)->pluck($type)->all();

        $deduped = [];
        foreach (array_merge($stored, $historical) as $item) {
            $trimmed = trim((string) $item);
            if ($trimmed === '') {
                continue;
            }
            $key = mb_strtolower($trimmed, 'UTF-8');
            if (! array_key_exists($key, $deduped)) {
                $deduped[$key] = $trimmed;
            }
        }

        $values = array_values($deduped);
        natcasesort($values);

        return array_values($values);
    }

    public static function remember(string $type, string $value): string
    {
        if (! in_array($type, ['code', 'unit'], true)) {
            throw new InvalidArgumentException('Jenis pilihan tidak valid.');
        }

        $trimmed = trim($value);

        return static::query()->createOrFirst(
            ['type' => $type, 'normalized_hash' => hash('sha256', mb_strtolower($trimmed, 'UTF-8'))],
            ['value' => $trimmed],
        )->value;
    }
}

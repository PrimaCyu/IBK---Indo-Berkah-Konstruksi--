<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectCashflow extends Model
{
    use SoftDeletes;

    public const MAX_QUANTITY = '999999.999';

    public const MAX_UNIT_PRICE = '9999999999.99';

    public const MAX_TOTAL = '999999999999.99';

    public string $running_balance = '0.00';

    protected $fillable = [
        'transaction_date', 'type', 'code', 'description', 'unit',
        'quantity_millis', 'unit_price_cents', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'quantity_millis' => 'integer',
            'unit_price_cents' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected function quantity(): Attribute
    {
        return Attribute::get(fn (): string => (string) BigDecimal::ofUnscaledValue($this->quantity_millis, 3));
    }

    protected function unitPrice(): Attribute
    {
        return Attribute::get(fn (): string => (string) BigDecimal::ofUnscaledValue($this->unit_price_cents, 2));
    }

    protected function total(): Attribute
    {
        return Attribute::get(fn (): string => (string) self::calculateTotal($this->quantity, $this->unit_price));
    }

    public static function calculateTotal(string $quantity, string $unitPrice): BigDecimal
    {
        return BigDecimal::of($quantity)->multipliedBy($unitPrice)->toScale(2, RoundingMode::HalfUp);
    }

    public static function ledger(Project $project): array
    {
        $transactions = $project->cashflows()->orderBy('transaction_date')->orderBy('id')->get();
        $income = $expense = $balance = BigDecimal::of('0.00');

        foreach ($transactions as $transaction) {
            $total = BigDecimal::of($transaction->total);
            if ($transaction->type === 'penerimaan') {
                $income = $income->plus($total);
                $balance = $balance->plus($total);
            } else {
                $expense = $expense->plus($total);
                $balance = $balance->minus($total);
            }
            $transaction->running_balance = (string) $balance;
        }

        return [
            'transactions' => $transactions,
            'income' => (string) $income,
            'expense' => (string) $expense,
            'balance' => (string) $balance,
        ];
    }
}

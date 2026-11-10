<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'created_by', 'updated_by', 'last_edited_at',
        'payment_method', 'category_id', 'transaction_number', 'receipt_number',
        'status', 'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'quantity_millis' => 'integer',
            'unit_price_cents' => 'integer',
            'last_edited_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TransactionAttachment::class, 'project_cashflow_id');
    }

    public function attachment(): HasOne
    {
        return $this->hasOne(TransactionAttachment::class, 'project_cashflow_id')->latestOfMany();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
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
        $transactions = $project->cashflows()->with(['creator', 'editor', 'approver', 'attachments'])->orderBy('transaction_date')->orderBy('id')->get();
        $income = $expense = $balance = BigDecimal::of('0.00');
        $pendingIncome = $pendingExpense = BigDecimal::of('0.00');
        $pendingCount = 0;

        foreach ($transactions as $transaction) {
            $total = BigDecimal::of($transaction->total);
            $status = $transaction->status ?? 'lunas';

            if ($status === 'lunas') {
                if ($transaction->type === 'penerimaan') {
                    $income = $income->plus($total);
                    $balance = $balance->plus($total);
                } else {
                    $expense = $expense->plus($total);
                    $balance = $balance->minus($total);
                }
            } elseif ($status === 'pending') {
                $pendingCount++;
                if ($transaction->type === 'penerimaan') {
                    $pendingIncome = $pendingIncome->plus($total);
                } else {
                    $pendingExpense = $pendingExpense->plus($total);
                }
            }
            $transaction->running_balance = (string) $balance;
        }

        return [
            'transactions' => $transactions,
            'income' => (string) $income,
            'expense' => (string) $expense,
            'balance' => (string) $balance,
            'pending_income' => (string) $pendingIncome,
            'pending_expense' => (string) $pendingExpense,
            'pending_count' => $pendingCount,
        ];
    }
}

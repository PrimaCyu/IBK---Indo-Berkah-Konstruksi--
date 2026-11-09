<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class TransactionAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'project_cashflow_id',
        'project_id',
        'transaction_number',
        'action',
        'user_id',
        'user_name',
        'user_role',
        'before_data',
        'after_data',
        'changed_fields',
        'ip_address',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'before_data' => 'array',
            'after_data' => 'array',
            'changed_fields' => 'array',
            'recorded_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new LogicException('Riwayat audit transaksi bersifat tetap (immutable) dan tidak dapat diubah.');
        });

        static::deleting(function (): void {
            throw new LogicException('Riwayat audit transaksi bersifat tetap (immutable) dan tidak dapat dihapus.');
        });
    }

    public function cashflow(): BelongsTo
    {
        return $this->belongsTo(ProjectCashflow::class, 'project_cashflow_id')->withTrashed();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'created' => 'Transaksi Dibuat',
            'updated' => 'Transaksi Diedit',
            'deleted' => 'Transaksi Dihapus',
            default => ucfirst((string) $this->action),
        };
    }
}

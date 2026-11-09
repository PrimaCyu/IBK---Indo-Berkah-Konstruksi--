<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionAttachment extends Model
{
    protected $fillable = [
        'project_cashflow_id',
        'disk',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function cashflow(): BelongsTo
    {
        return $this->belongsTo(ProjectCashflow::class, 'project_cashflow_id')->withTrashed();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isPdf(): bool
    {
        return strtolower($this->mime_type) === 'application/pdf'
            || str_ends_with(strtolower($this->original_name), '.pdf');
    }

    public function isImage(): bool
    {
        return str_starts_with(strtolower($this->mime_type), 'image/');
    }

    public function formattedSize(): string
    {
        $bytes = max(0, (int) $this->file_size);
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', '.').' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1, ',', '.').' KB';
        }

        return $bytes.' B';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TransactionCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $category): void {
            if (empty($category->slug)) {
                $base = Str::slug($category->name) ?: 'kategori';
                $slug = $base;
                $counter = 1;
                while (static::where('slug', $slug)->whereKeyNot($category->getKey())->exists()) {
                    $slug = $base.'-'.$counter++;
                }
                $category->slug = $slug;
            }
        });
    }

    public function cashflows(): HasMany
    {
        return $this->hasMany(ProjectCashflow::class, 'category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForType(Builder $query, ?string $type): Builder
    {
        if ($type === 'penerimaan' || $type === 'pengeluaran') {
            return $query->whereIn('type', [$type, 'both']);
        }

        return $query;
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'penerimaan' => 'Pemasukan',
            'pengeluaran' => 'Pengeluaran',
            default => 'Pemasukan & Pengeluaran',
        };
    }
}

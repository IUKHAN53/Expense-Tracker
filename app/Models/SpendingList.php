<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpendingList extends Model
{
    public const TYPE_PERSON = 'person';
    public const TYPE_HOUSEHOLD = 'household';
    public const TYPE_VEHICLE = 'vehicle';

    protected $fillable = [
        'name',
        'type',
        'color',
        'icon',
        'monthly_budget',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'monthly_budget' => 'decimal:2',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    /** Total spent on this list within a date range (defaults to all time). */
    public function totalSpent(?string $from = null, ?string $to = null): float
    {
        return (float) $this->entries()
            ->when($from, fn ($q) => $q->where('purchased_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('purchased_at', '<=', $to))
            ->sum('amount');
    }
}

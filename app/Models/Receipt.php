<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receipt extends Model
{
    use BelongsToAccount;

    public const TYPE_GROCERY = 'grocery';
    public const TYPE_FUEL = 'fuel';
    public const TYPE_PHARMACY = 'pharmacy';
    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'account_id',
        'created_by_user_id',
        'image_path',
        'merchant',
        'receipt_type',
        'total',
        'purchased_at',
        'status',
        'raw_json',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'purchased_at' => 'datetime',
            'raw_json' => 'array',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function isFuel(): bool
    {
        return $this->receipt_type === self::TYPE_FUEL;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entry extends Model
{
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_SCAN = 'scan';
    public const SOURCE_SMS = 'sms';

    protected $fillable = [
        'spending_list_id',
        'category_id',
        'receipt_id',
        'bank_message_id',
        'item_name',
        'amount',
        'quantity',
        'unit',
        'purchased_at',
        'source',
        'notes',
        'fuel_liters',
        'fuel_rate',
        'odometer',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'quantity' => 'decimal:2',
            'purchased_at' => 'datetime',
            'fuel_liters' => 'decimal:2',
            'fuel_rate' => 'decimal:2',
            'odometer' => 'integer',
        ];
    }

    public function spendingList(): BelongsTo
    {
        return $this->belongsTo(SpendingList::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    public function bankMessage(): BelongsTo
    {
        return $this->belongsTo(BankMessage::class);
    }
}

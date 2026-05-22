<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankMessage extends Model
{
    protected $fillable = [
        'sender',
        'body',
        'sms_hash',
        'received_at',
        'amount',
        'merchant',
        'direction',
        'is_transaction',
        'matched_list_id',
        'entry_id',
        'status',
        'raw_json',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
            'amount' => 'decimal:2',
            'is_transaction' => 'boolean',
            'raw_json' => 'array',
        ];
    }

    public function matchedList(): BelongsTo
    {
        return $this->belongsTo(SpendingList::class, 'matched_list_id');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class, 'entry_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A single USD-pivoted FX rate (1 USD = `rate` units of `quote`). Global
 * reference data refreshed by the fx:refresh command — no AccountScope.
 */
class CurrencyRate extends Model
{
    protected $fillable = ['base', 'quote', 'rate', 'fetched_at'];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:8',
            'fetched_at' => 'datetime',
        ];
    }
}

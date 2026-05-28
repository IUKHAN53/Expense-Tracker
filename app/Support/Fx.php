<?php

namespace App\Support;

use App\Models\CurrencyRate;
use Illuminate\Support\Carbon;

/**
 * Cross-rate resolver over the USD-pivoted `currency_rates` table.
 *
 * rate('EUR', 'PKR') = (USD->PKR) / (USD->EUR), i.e. how many PKR one EUR buys.
 * Returns null when either leg is missing (table not yet populated / unknown
 * code) so callers can fall back to a manually entered rate.
 */
class Fx
{
    public static function rate(string $from, string $to): ?float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return 1.0;
        }

        $usdFrom = self::usdRate($from);
        $usdTo = self::usdRate($to);

        if (! $usdFrom || ! $usdTo) {
            return null;
        }

        return round($usdTo / $usdFrom, 8);
    }

    /** How many units of $code equal 1 USD. */
    private static function usdRate(string $code): ?float
    {
        if ($code === 'USD') {
            return 1.0;
        }

        $row = CurrencyRate::query()
            ->where('base', 'USD')
            ->where('quote', $code)
            ->first();

        return $row ? (float) $row->rate : null;
    }

    /** Timestamp of the most recent refresh, for "rate as of …" labels. */
    public static function asOf(): ?string
    {
        $value = CurrencyRate::query()->where('base', 'USD')->max('fetched_at');

        return $value ? Carbon::parse($value)->toIso8601String() : null;
    }
}

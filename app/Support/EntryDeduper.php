<?php

namespace App\Support;

use App\Models\Entry;
use App\Models\Receipt;
use Carbon\CarbonImmutable;
use DateTimeInterface;

/**
 * Detects probable duplicates so an SMS import doesn't double-count a
 * purchase the user already recorded manually or via a receipt scan.
 *
 *  • findDuplicate()        — single entry on the same list & amount
 *                             (catches manual entries / fuel SMS / single-person scans).
 *  • findDuplicateReceipt() — a scanned receipt whose total equals the SMS
 *                             amount, even when its items were split across
 *                             several people's lists.
 */
class EntryDeduper
{
    public const WINDOW_HOURS = 12;

    public static function findDuplicate(
        int $spendingListId,
        float $amount,
        DateTimeInterface|string $when,
        int $hoursWindow = self::WINDOW_HOURS,
    ): ?Entry {
        $center = self::carbon($when);

        return Entry::query()
            ->where('spending_list_id', $spendingListId)
            ->whereBetween('purchased_at', [
                $center->subHours($hoursWindow),
                $center->addHours($hoursWindow),
            ])
            // PKR with no decimals, so tolerate <1 Rs rounding noise.
            ->whereRaw('ABS(CAST(amount AS REAL) - ?) < 1', [$amount])
            ->orderBy('id')
            ->first();
    }

    /**
     * Match against a receipt's total — receipts are usually split across
     * multiple people, so checking individual entries on the SMS's "Home"
     * fallback list misses them. We compare against `purchased_at` if set,
     * otherwise the receipt's `created_at` (when it was scanned).
     */
    public static function findDuplicateReceipt(
        float $amount,
        DateTimeInterface|string $when,
        int $hoursWindow = self::WINDOW_HOURS,
    ): ?Receipt {
        $center = self::carbon($when);
        $from = $center->subHours($hoursWindow);
        $to = $center->addHours($hoursWindow);

        return Receipt::query()
            ->whereNotNull('total')
            ->whereRaw('ABS(CAST(total AS REAL) - ?) < 1', [$amount])
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('purchased_at', [$from, $to])
                    ->orWhere(function ($qq) use ($from, $to) {
                        $qq->whereNull('purchased_at')
                            ->whereBetween('created_at', [$from, $to]);
                    });
            })
            ->orderBy('id')
            ->first();
    }

    private static function carbon(DateTimeInterface|string $when): CarbonImmutable
    {
        return $when instanceof DateTimeInterface
            ? CarbonImmutable::instance($when)
            : CarbonImmutable::parse($when);
    }
}

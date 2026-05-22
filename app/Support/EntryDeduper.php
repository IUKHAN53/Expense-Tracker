<?php

namespace App\Support;

use App\Models\Entry;
use Carbon\CarbonImmutable;
use DateTimeInterface;

/**
 * Looks for an existing Entry that probably represents the same purchase
 * as the one we're about to create — same list, same rupee amount, within
 * a small time window around the same moment. Used to stop SMS imports
 * from duplicating a manual or receipt-scan entry the user already added.
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
        $center = $when instanceof DateTimeInterface
            ? CarbonImmutable::instance($when)
            : CarbonImmutable::parse($when);

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
}

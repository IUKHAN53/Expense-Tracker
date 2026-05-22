<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Throwable;

/**
 * Resolves a "YYYY-MM" string (or null = current month) into a
 * [start, end] pair of CarbonImmutable instances.
 */
class MonthRange
{
    /**
     * @return array{0:CarbonImmutable,1:CarbonImmutable}
     */
    public static function resolve(?string $month): array
    {
        $base = CarbonImmutable::now();

        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            try {
                $base = CarbonImmutable::createFromFormat('Y-m-d', $month.'-01');
            } catch (Throwable) {
                $base = CarbonImmutable::now();
            }
        }

        return [$base->startOfMonth(), $base->endOfMonth()];
    }

    /**
     * Build a "YYYY-MM" => "Month YYYY" map for the last $count months.
     * Used by the admin's month-filter selects.
     *
     * @return array<string, string>
     */
    public static function options(int $count = 24): array
    {
        $out = [];
        for ($i = 0; $i < $count; $i++) {
            $d = CarbonImmutable::now()->subMonths($i);
            $out[$d->format('Y-m')] = $d->format('F Y');
        }

        return $out;
    }
}

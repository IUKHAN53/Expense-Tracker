<?php

namespace App\Support;

use Carbon\CarbonImmutable;

/**
 * Resolves the Reports page "period" filter into a concrete [start, end]
 * window, plus a label and the number of months it spans (used to pro-rate
 * monthly budgets against multi-month periods).
 */
class ReportPeriod
{
    public const DEFAULT = 'last_6';

    /** @return array<string,string> */
    public static function options(): array
    {
        return [
            'this_month' => 'This month',
            'last_3' => 'Last 3 months',
            'last_6' => 'Last 6 months',
            'last_12' => 'Last 12 months',
            'this_year' => 'This year',
            'all' => 'All time',
        ];
    }

    /**
     * @return array{start:CarbonImmutable, end:CarbonImmutable, label:string, months:int}
     */
    public static function resolve(?string $key): array
    {
        $now = CarbonImmutable::now();
        $end = $now->endOfMonth();

        [$start, $months] = match ($key) {
            'this_month' => [$now->startOfMonth(), 1],
            'last_3' => [$now->subMonths(2)->startOfMonth(), 3],
            'last_12' => [$now->subMonths(11)->startOfMonth(), 12],
            'this_year' => [$now->startOfYear(), $now->month],
            'all' => [CarbonImmutable::create(2000, 1, 1), max(1, (int) round($now->diffInMonths(CarbonImmutable::create(2000, 1, 1)))) + 1],
            default => [$now->subMonths(5)->startOfMonth(), 6], // last_6
        };

        if ($key === 'this_year') {
            $end = $now->endOfYear();
        }

        return [
            'start' => $start,
            'end' => $end,
            'label' => self::options()[$key] ?? self::options()[self::DEFAULT],
            'months' => max(1, $months),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Entry;
use App\Support\Money;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * This month vs last month. Always compares the two most recent months
 * (independent of the page period filter) so it reads as a fixed "trend now".
 */
class ReportMonthOverMonth extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $ccy = Money::current();
        $now = CarbonImmutable::now();
        $thisStart = $now->startOfMonth();
        $thisEnd = $now->endOfMonth();
        $lastStart = $now->subMonth()->startOfMonth();
        $lastEnd = $now->subMonth()->endOfMonth();

        $thisTotal = (float) Entry::query()->whereBetween('purchased_at', [$thisStart, $thisEnd])->sum('amount');
        $lastTotal = (float) Entry::query()->whereBetween('purchased_at', [$lastStart, $lastEnd])->sum('amount');

        $delta = $thisTotal - $lastTotal;
        $pct = $lastTotal > 0 ? round($delta / $lastTotal * 100) : null;
        $up = $delta > 0;

        $mover = $this->biggestMover($thisStart, $thisEnd, $lastStart, $lastEnd);

        return [
            Stat::make('This month', Money::format($thisTotal, $ccy))
                ->description($now->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Last month', Money::format($lastTotal, $ccy))
                ->description($lastStart->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('gray'),

            Stat::make('Change', ($up ? '+' : '').Money::format($delta, $ccy).($pct !== null ? " ({$pct}%)" : ''))
                ->description($up ? 'More than last month' : ($delta < 0 ? 'Less than last month' : 'No change'))
                ->descriptionIcon($up ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($up ? 'danger' : 'success'),

            Stat::make('Biggest mover', $mover['name'])
                ->description($mover['desc'])
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),
        ];
    }

    /** Category with the largest spend increase vs last month. */
    private function biggestMover(
        CarbonImmutable $thisStart,
        CarbonImmutable $thisEnd,
        CarbonImmutable $lastStart,
        CarbonImmutable $lastEnd,
    ): array {
        $sumByCat = function (CarbonImmutable $s, CarbonImmutable $e) {
            return Entry::query()
                ->whereBetween('purchased_at', [$s, $e])
                ->selectRaw('category_id, SUM(amount) as t')
                ->groupBy('category_id')
                ->pluck('t', 'category_id');
        };

        $thisByCat = $sumByCat($thisStart, $thisEnd);
        $lastByCat = $sumByCat($lastStart, $lastEnd);

        $bestId = null;
        $bestDelta = 0;
        foreach ($thisByCat as $catId => $t) {
            $d = (float) $t - (float) ($lastByCat[$catId] ?? 0);
            if ($d > $bestDelta) {
                $bestDelta = $d;
                $bestId = $catId;
            }
        }

        if (! $bestId || $bestDelta <= 0) {
            return ['name' => '—', 'desc' => 'No notable increase'];
        }

        $name = $bestId ? (Category::find($bestId)?->name ?? 'Uncategorised') : 'Uncategorised';

        return ['name' => $name, 'desc' => '+'.Money::format($bestDelta, Money::current()).' vs last month'];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Entry;
use App\Models\SpendingList;
use App\Support\Money;
use App\Support\ReportPeriod;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Headline numbers for the selected period, in the account's own currency.
 * Account-scoped automatically (rendered in the per-account panels).
 */
class ReportOverviewStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        ['start' => $start, 'end' => $end, 'label' => $label] = ReportPeriod::resolve($this->pageFilters['period'] ?? null);
        $ccy = Money::current();

        $scoped = fn () => Entry::query()->whereBetween('purchased_at', [$start, $end]);

        $total = (float) $scoped()->sum('amount');
        $count = (int) $scoped()->count();
        $avg = $count > 0 ? $total / $count : 0;

        $topCategory = Entry::query()
            ->whereBetween('purchased_at', [$start, $end])
            ->whereNotNull('category_id')
            ->selectRaw('category_id, SUM(amount) as t')
            ->groupBy('category_id')
            ->orderByDesc('t')
            ->first();
        $topCategoryName = $topCategory ? (Category::find($topCategory->category_id)?->name ?? '—') : '—';

        $topPerson = SpendingList::query()
            ->where('type', SpendingList::TYPE_PERSON)
            ->withSum(['entries as spent' => fn ($q) => $q->whereBetween('purchased_at', [$start, $end])], 'amount')
            ->orderByDesc('spent')
            ->first();

        $fuelSpend = (float) Entry::query()
            ->whereBetween('purchased_at', [$start, $end])
            ->whereHas('spendingList', fn ($q) => $q->where('type', SpendingList::TYPE_VEHICLE))
            ->sum('amount');

        return [
            Stat::make('Total spent · '.$label, Money::format($total, $ccy))
                ->description($count.' '.str('entry')->plural($count))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Average / entry', Money::format($avg, $ccy))
                ->description('across the period')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('gray'),

            Stat::make('Top category', $topCategoryName)
                ->description($topCategory ? Money::format((float) $topCategory->t, $ccy) : 'No spending')
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),

            Stat::make('Top spender', $topPerson?->name ?? '—')
                ->description($topPerson && $topPerson->spent ? Money::format((float) $topPerson->spent, $ccy) : 'No spending')
                ->descriptionIcon('heroicon-m-user')
                ->color('warning'),

            Stat::make('Fuel spend', Money::format($fuelSpend, $ccy))
                ->description('vehicle lists')
                ->descriptionIcon('heroicon-m-truck')
                ->color('danger'),
        ];
    }
}

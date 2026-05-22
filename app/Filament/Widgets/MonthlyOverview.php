<?php

namespace App\Filament\Widgets;

use App\Models\Entry;
use App\Models\SpendingList;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonthlyOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $start = CarbonImmutable::now()->startOfMonth();
        $end = CarbonImmutable::now()->endOfMonth();

        $monthEntries = Entry::query()->whereBetween('purchased_at', [$start, $end]);

        $grandTotal = (float) $monthEntries->clone()->sum('amount');
        $entryCount = (int) $monthEntries->clone()->count();

        // Highest-spending person this month.
        $topPerson = SpendingList::query()
            ->where('type', SpendingList::TYPE_PERSON)
            ->withSum(['entries as spent' => fn ($q) => $q->whereBetween('purchased_at', [$start, $end])], 'amount')
            ->orderByDesc('spent')
            ->first();

        $homeTotal = $this->listTotal(SpendingList::TYPE_HOUSEHOLD, $start, $end);
        $carTotal = $this->listTotal(SpendingList::TYPE_VEHICLE, $start, $end);

        return [
            Stat::make('Total spent this month', 'Rs '.number_format($grandTotal))
                ->description($entryCount.' '.str('entry')->plural($entryCount))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Top spender', $topPerson?->name ?? '—')
                ->description($topPerson ? 'Rs '.number_format((float) $topPerson->spent) : 'No spending yet')
                ->descriptionIcon('heroicon-m-user')
                ->color('warning'),

            Stat::make('Home / General', 'Rs '.number_format($homeTotal))
                ->description('Shared household spending')
                ->descriptionIcon('heroicon-m-home')
                ->color('info'),

            Stat::make('Car / Fuel', 'Rs '.number_format($carTotal))
                ->description('Vehicle spending')
                ->descriptionIcon('heroicon-m-truck')
                ->color('danger'),
        ];
    }

    private function listTotal(string $type, CarbonImmutable $start, CarbonImmutable $end): float
    {
        return (float) Entry::query()
            ->whereBetween('purchased_at', [$start, $end])
            ->whereHas('spendingList', fn ($q) => $q->where('type', $type))
            ->sum('amount');
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\SpendingList;
use App\Support\Money;
use App\Support\ReportPeriod;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

/**
 * Spend by spending list (people / household / vehicles) for the period.
 */
class ReportPersonBreakdown extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'By list';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '280px';

    public function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        ['start' => $start, 'end' => $end] = ReportPeriod::resolve($this->pageFilters['period'] ?? null);

        $lists = SpendingList::query()
            ->withSum(['entries as spent' => fn ($q) => $q->whereBetween('purchased_at', [$start, $end])], 'amount')
            ->get()
            ->map(fn (SpendingList $l) => [
                'name' => $l->name,
                'color' => $l->color ?: '#c9621f',
                'total' => (float) ($l->spent ?? 0),
            ])
            ->filter(fn ($l) => $l['total'] > 0)
            ->sortByDesc('total')
            ->values();

        return [
            'datasets' => [[
                'label' => 'Spent ('.Money::current().')',
                'data' => $lists->map(fn ($l) => round($l['total'], 2))->all(),
                'backgroundColor' => $lists->map(fn ($l) => $l['color'])->all(),
            ]],
            'labels' => $lists->map(fn ($l) => $l['name'])->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => ['x' => ['beginAtZero' => true]],
            'plugins' => ['legend' => ['display' => false]],
        ];
    }
}

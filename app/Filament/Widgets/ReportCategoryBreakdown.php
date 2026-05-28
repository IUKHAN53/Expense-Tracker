<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Entry;
use App\Support\Money;
use App\Support\ReportPeriod;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

/**
 * Spend by category for the selected period (top 8 + "Other").
 */
class ReportCategoryBreakdown extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'By category';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '280px';

    public function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        ['start' => $start, 'end' => $end] = ReportPeriod::resolve($this->pageFilters['period'] ?? null);

        $names = Category::pluck('name', 'id');

        $rows = Entry::query()
            ->whereBetween('purchased_at', [$start, $end])
            ->selectRaw('category_id, SUM(amount) as t')
            ->groupBy('category_id')
            ->get()
            ->map(fn ($r) => [
                'name' => $r->category_id ? ($names[$r->category_id] ?? 'Unknown') : 'Uncategorised',
                'total' => (float) $r->t,
            ])
            ->filter(fn ($r) => $r['total'] > 0)
            ->sortByDesc('total')
            ->values();

        // Collapse the long tail into "Other".
        $top = $rows->take(8);
        $otherTotal = $rows->slice(8)->sum('total');
        if ($otherTotal > 0) {
            $top->push(['name' => 'Other', 'total' => $otherTotal]);
        }

        $palette = ['#c9621f', '#5d7a3d', '#b14430', '#7c6a52', '#d39a4e', '#3f6f52', '#8a5a3c', '#9aa86b', '#c08a5e'];

        return [
            'datasets' => [[
                'label' => Money::current(),
                'data' => $top->map(fn ($r) => round($r['total'], 2))->all(),
                'backgroundColor' => $top->keys()->map(fn ($i) => $palette[$i % count($palette)])->all(),
            ]],
            'labels' => $top->map(fn ($r) => $r['name'])->all(),
        ];
    }

    protected function getOptions(): array
    {
        return ['plugins' => ['legend' => ['position' => 'bottom']]];
    }
}

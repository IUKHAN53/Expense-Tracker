<?php

namespace App\Filament\Widgets;

use App\Models\Entry;
use App\Support\Money;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;

/**
 * Total spend per month for the last 12 months. A trend is inherently
 * multi-month, so this ignores the period filter and always shows a year.
 * Account-scoped (per-account panels only). Grouped in PHP for DB portability.
 */
class ReportSpendingTrend extends ChartWidget
{
    protected ?string $heading = 'Spending trend';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '260px';

    public function getDescription(): ?string
    {
        return 'Monthly total over the last 12 months ('.Money::current().')';
    }

    public function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $since = CarbonImmutable::now()->subMonths(11)->startOfMonth();

        $rows = Entry::query()
            ->where('purchased_at', '>=', $since)
            ->get(['amount', 'purchased_at'])
            ->groupBy(fn ($e) => $e->purchased_at?->format('Y-m'))
            ->map(fn ($g) => (float) $g->sum('amount'));

        $labels = [];
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = CarbonImmutable::now()->subMonths($i);
            $labels[] = $d->format('M y');
            $data[] = round($rows[$d->format('Y-m')] ?? 0, 2);
        }

        return [
            'datasets' => [[
                'label' => 'Spent ('.Money::current().')',
                'data' => $data,
                'backgroundColor' => 'rgba(201, 98, 31, 0.65)',
                'borderColor' => '#c9621f',
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => ['y' => ['beginAtZero' => true]],
            'plugins' => ['legend' => ['display' => false]],
        ];
    }
}

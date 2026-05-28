<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Receipt;
use Filament\Widgets\ChartWidget;

/**
 * AI receipt scans per month across all tenants for the last 12 months.
 * One Receipt row == one scan submitted to Gemini.
 */
class ScanUsageChart extends ChartWidget
{
    protected ?string $heading = 'AI scan usage';

    protected ?string $description = 'Receipts uploaded for parsing, per month';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '240px';

    public function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $buckets = SignupsChart::monthBuckets(12);
        $scans = SignupsChart::countByMonth(
            Receipt::query()->where('created_at', '>=', $buckets['since'])
        );

        return [
            'datasets' => [[
                'label' => 'Scans',
                'data' => array_map(fn ($k) => $scans[$k] ?? 0, $buckets['keys']),
                'borderColor' => '#c9621f',
                'backgroundColor' => 'rgba(201, 98, 31, 0.14)',
                'fill' => true,
                'tension' => 0.3,
                'pointBackgroundColor' => '#c9621f',
            ]],
            'labels' => $buckets['labels'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
            'plugins' => ['legend' => ['display' => false]],
        ];
    }
}

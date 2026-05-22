<?php

namespace App\Filament\Widgets;

use App\Models\Entry;
use App\Models\SpendingList;
use Filament\Widgets\ChartWidget;

/**
 * Per-refill km/L for the Car list. Computed by walking entries in
 * odometer order and dividing the distance since the previous fill by
 * the current fill's litres.
 */
class FuelTrend extends ChartWidget
{
    protected ?string $heading = 'Fuel economy · km per litre';

    protected ?string $description = 'Per-refill km/L for the most recent fills';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '220px';

    protected static ?int $sort = 2;

    public function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $carList = SpendingList::where('type', SpendingList::TYPE_VEHICLE)->first();
        if (! $carList) {
            return $this->empty();
        }

        $entries = Entry::query()
            ->where('spending_list_id', $carList->id)
            ->whereNotNull('fuel_liters')
            ->where('fuel_liters', '>', 0)
            ->orderBy('purchased_at')
            ->orderBy('id')
            ->get();

        $points = [];
        $prevOdo = null;
        foreach ($entries as $e) {
            $odo = $e->odometer;
            $liters = (float) $e->fuel_liters;
            if ($odo !== null && $prevOdo !== null && $odo > $prevOdo && $liters > 0) {
                $points[] = [
                    'date' => $e->purchased_at,
                    'kmpl' => round(($odo - $prevOdo) / $liters, 2),
                ];
            }
            if ($odo !== null) {
                $prevOdo = $odo;
            }
        }

        if (empty($points)) {
            return $this->empty();
        }

        // Last 14 fills, oldest-to-newest for the line.
        $points = array_slice($points, -14);

        return [
            'datasets' => [[
                'label' => 'km / L',
                'data' => array_map(fn ($p) => $p['kmpl'], $points),
                'borderColor' => '#c9621f',
                'backgroundColor' => 'rgba(201, 98, 31, 0.14)',
                'tension' => 0.3,
                'fill' => true,
                'pointRadius' => 3,
                'pointBackgroundColor' => '#c9621f',
            ]],
            'labels' => array_map(fn ($p) => $p['date']?->format('d M') ?? '', $points),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => false,
                    'ticks' => ['color' => '#7a6850'],
                    'grid' => ['color' => 'rgba(43,31,18,0.06)'],
                ],
                'x' => [
                    'ticks' => ['color' => '#7a6850'],
                    'grid' => ['display' => false],
                ],
            ],
            'plugins' => [
                'legend' => ['display' => false],
            ],
        ];
    }

    private function empty(): array
    {
        return [
            'datasets' => [['label' => 'km / L', 'data' => []]],
            'labels' => [],
        ];
    }
}

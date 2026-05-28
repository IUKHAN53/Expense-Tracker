<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Account;
use App\Models\User;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * New accounts and users per month for the last 12 months. Grouping happens in
 * PHP (not SQL) to stay portable across SQLite/MySQL.
 */
class SignupsChart extends ChartWidget
{
    protected ?string $heading = 'Growth · new signups';

    protected ?string $description = 'New households and users per month';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '240px';

    public function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $buckets = self::monthBuckets(12);

        $accounts = self::countByMonth(Account::query()->where('created_at', '>=', $buckets['since']));
        $users = self::countByMonth(User::query()->where('created_at', '>=', $buckets['since']));

        return [
            'datasets' => [
                [
                    'label' => 'Households',
                    'data' => array_map(fn ($k) => $accounts[$k] ?? 0, $buckets['keys']),
                    'backgroundColor' => 'rgba(201, 98, 31, 0.65)',
                    'borderColor' => '#c9621f',
                ],
                [
                    'label' => 'Users',
                    'data' => array_map(fn ($k) => $users[$k] ?? 0, $buckets['keys']),
                    'backgroundColor' => 'rgba(93, 122, 61, 0.55)',
                    'borderColor' => '#5d7a3d',
                ],
            ],
            'labels' => $buckets['labels'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
        ];
    }

    /**
     * @return array{since:CarbonImmutable, keys:list<string>, labels:list<string>}
     */
    public static function monthBuckets(int $count): array
    {
        $keys = [];
        $labels = [];
        for ($i = $count - 1; $i >= 0; $i--) {
            $d = CarbonImmutable::now()->subMonths($i);
            $keys[] = $d->format('Y-m');
            $labels[] = $d->format('M y');
        }

        return [
            'since' => CarbonImmutable::now()->subMonths($count - 1)->startOfMonth(),
            'keys' => $keys,
            'labels' => $labels,
        ];
    }

    /**
     * @param  Builder  $query
     * @return array<string,int>
     */
    public static function countByMonth($query): array
    {
        return $query->reorder()->get(['created_at'])
            ->groupBy(fn ($row) => $row->created_at?->format('Y-m'))
            ->map->count()
            ->all();
    }
}

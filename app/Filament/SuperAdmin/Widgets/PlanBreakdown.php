<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Account;
use Filament\Widgets\ChartWidget;

/**
 * Plan distribution across all tenants. Splits monthly Pro into active vs
 * expired so churned-but-not-downgraded accounts are visible.
 */
class PlanBreakdown extends ChartWidget
{
    protected ?string $heading = 'Revenue · plan mix';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '240px';

    public function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $free = Account::query()->where('plan', Account::PLAN_FREE)->count();
        $lifetime = Account::query()->where('plan', Account::PLAN_PRO_LIFETIME)->count();
        $monthlyActive = Account::query()
            ->where('plan', Account::PLAN_PRO_MONTHLY)
            ->where(fn ($q) => $q->whereNull('plan_expires_at')->orWhere('plan_expires_at', '>', now()))
            ->count();
        $monthlyExpired = Account::query()
            ->where('plan', Account::PLAN_PRO_MONTHLY)
            ->whereNotNull('plan_expires_at')
            ->where('plan_expires_at', '<=', now())
            ->count();

        return [
            'datasets' => [[
                'label' => 'Accounts',
                'data' => [$free, $monthlyActive, $monthlyExpired, $lifetime],
                'backgroundColor' => ['#cdbfa6', '#5d7a3d', '#b14430', '#c9621f'],
            ]],
            'labels' => ['Free', 'Pro monthly', 'Monthly expired', 'Pro lifetime'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'bottom']],
        ];
    }
}

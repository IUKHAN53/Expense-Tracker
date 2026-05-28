<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ReportBudgetVsActual;
use App\Filament\Widgets\ReportCategoryBreakdown;
use App\Filament\Widgets\ReportOverviewStats;
use App\Filament\Widgets\ReportPersonBreakdown;
use App\Filament\Widgets\ReportSpendingTrend;
use App\Filament\Widgets\ReportTopExpenses;
use App\Support\ReportPeriod;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

/**
 * Per-account Reports dashboard (/admin/reports and /app/reports). A single
 * "period" filter drives every account-scoped report widget. Lives only in the
 * per-account panels — the SuperAdmin panel has its own platform reports.
 */
class Reports extends BaseDashboard
{
    use HasFiltersForm;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $title = 'Reports';

    protected static ?int $navigationSort = 1;

    protected static string $routePath = 'reports';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('period')
                ->label('Period')
                ->options(ReportPeriod::options())
                ->default(ReportPeriod::DEFAULT)
                ->selectablePlaceholder(false)
                ->native(false),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            ReportOverviewStats::class,
            ReportSpendingTrend::class,
            ReportCategoryBreakdown::class,
            ReportPersonBreakdown::class,
            ReportBudgetVsActual::class,
            ReportTopExpenses::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}

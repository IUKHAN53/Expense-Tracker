<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\FuelTrend;
use App\Filament\Widgets\MonthlyOverview;
use App\Support\MonthRange;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Filament\Widgets\AccountWidget;

/**
 * Kharcha dashboard with a month picker. The selected month is exposed to
 * every widget that uses InteractsWithPageFilters. Widgets are listed
 * explicitly so the discovered Report* widgets (which power the Reports page)
 * don't pile onto this overview.
 */
class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            MonthlyOverview::class,
            FuelTrend::class,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('month')
                ->label('Month')
                ->options(MonthRange::options(24))
                ->default(now()->format('Y-m'))
                ->selectablePlaceholder(false)
                ->native(false),
        ]);
    }
}

<?php

namespace App\Filament\Pages;

use App\Support\MonthRange;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

/**
 * Kharcha dashboard with a month picker. The selected month is exposed to
 * every widget that uses InteractsWithPageFilters.
 */
class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

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

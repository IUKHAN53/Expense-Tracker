<?php

namespace App\Filament\Widgets;

use App\Models\SpendingList;
use App\Support\Money;
use App\Support\ReportPeriod;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Budget vs actual for lists that have a monthly_budget set. The budget is
 * pro-rated by the number of months in the selected period.
 */
class ReportBudgetVsActual extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Budget vs actual';

    public function table(Table $table): Table
    {
        ['start' => $start, 'end' => $end, 'months' => $months] = ReportPeriod::resolve($this->pageFilters['period'] ?? null);
        $ccy = Money::current();

        return $table
            ->query(
                SpendingList::query()
                    ->whereNotNull('monthly_budget')
                    ->where('monthly_budget', '>', 0)
                    ->withSum(['entries as spent' => fn ($q) => $q->whereBetween('purchased_at', [$start, $end])], 'amount')
            )
            ->paginated(false)
            ->emptyStateHeading('No budgets set')
            ->emptyStateDescription('Set a monthly budget on a list to track it here.')
            ->columns([
                TextColumn::make('name')->label('List')->weight('bold'),
                TextColumn::make('monthly_budget')
                    ->label('Budget / mo')
                    ->formatStateUsing(fn ($state) => Money::format((float) $state, $ccy)),
                TextColumn::make('period_budget')
                    ->label("Budget × {$months}mo")
                    ->state(fn (SpendingList $r) => Money::format((float) $r->monthly_budget * $months, $ccy)),
                TextColumn::make('spent')
                    ->label('Actual')
                    ->formatStateUsing(fn ($state) => Money::format((float) $state, $ccy)),
                TextColumn::make('utilisation')
                    ->label('Used')
                    ->badge()
                    ->state(function (SpendingList $r) use ($months) {
                        $budget = (float) $r->monthly_budget * $months;
                        $pct = $budget > 0 ? round((float) $r->spent / $budget * 100) : 0;

                        return $pct.'%';
                    })
                    ->color(function (SpendingList $r) use ($months) {
                        $budget = (float) $r->monthly_budget * $months;
                        $pct = $budget > 0 ? (float) $r->spent / $budget * 100 : 0;

                        return $pct > 100 ? 'danger' : ($pct > 80 ? 'warning' : 'success');
                    }),
            ]);
    }
}

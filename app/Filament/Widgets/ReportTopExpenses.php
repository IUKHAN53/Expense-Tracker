<?php

namespace App\Filament\Widgets;

use App\Models\Entry;
use App\Support\Money;
use App\Support\ReportPeriod;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Largest individual expenses in the selected period.
 */
class ReportTopExpenses extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Largest expenses';

    public function table(Table $table): Table
    {
        ['start' => $start, 'end' => $end] = ReportPeriod::resolve($this->pageFilters['period'] ?? null);
        $ccy = Money::current();

        return $table
            ->query(
                Entry::query()
                    ->whereBetween('purchased_at', [$start, $end])
                    ->with(['spendingList', 'category'])
            )
            ->defaultSort('amount', 'desc')
            ->paginated([10, 25])
            ->columns([
                TextColumn::make('purchased_at')->label('Date')->date('d M Y')->sortable(),
                TextColumn::make('item_name')->label('Item')->searchable()->wrap(),
                TextColumn::make('spendingList.name')->label('List')->badge(),
                TextColumn::make('category.name')->label('Category')->badge()->placeholder('—'),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => Money::format((float) $state, $ccy, 2)),
                TextColumn::make('original_amount')
                    ->label('Original')
                    ->alignEnd()
                    ->formatStateUsing(fn ($state, Entry $r) => $state !== null
                        ? number_format((float) $state, 2).' '.$r->original_currency
                        : '—'),
            ]);
    }
}

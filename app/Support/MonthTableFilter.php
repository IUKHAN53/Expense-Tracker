<?php

namespace App\Support;

use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * "Month" filter for Filament tables — drives a SelectFilter from
 * MonthRange::options() and applies a between-dates clause on the
 * supplied column. Defaults to the current month.
 */
class MonthTableFilter
{
    public static function make(string $column = 'purchased_at'): SelectFilter
    {
        return SelectFilter::make('month')
            ->label('Month')
            ->options(MonthRange::options(24))
            ->default(now()->format('Y-m'))
            ->selectablePlaceholder(false)
            ->native(false)
            ->query(function (Builder $query, array $data) use ($column): void {
                $month = $data['value'] ?? null;
                if (! $month || ! preg_match('/^\d{4}-\d{2}$/', $month)) {
                    return;
                }
                [$start, $end] = MonthRange::resolve($month);
                $query->whereBetween($column, [$start, $end]);
            });
    }
}

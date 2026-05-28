<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Filament\Resources\Accounts\AccountResource;
use App\Models\Account;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Busiest tenants by lifetime spend volume, with members and current-month
 * scan usage alongside. A SuperAdmin bypasses AccountScope, so the
 * withSum('entries') aggregate spans every household's entries.
 */
class TopAccountsTable extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Top accounts by activity';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Account::query()
                    ->withCount('users')
                    ->withSum('entries', 'amount')
                    ->withCount('entries')
            )
            ->defaultSort('entries_sum_amount', 'desc')
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('name')
                    ->label('Household')
                    ->searchable()
                    ->weight('bold')
                    ->url(fn (Account $record) => AccountResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Account::PLAN_PRO_LIFETIME => 'success',
                        Account::PLAN_PRO_MONTHLY => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('currency')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('users_count')
                    ->label('Members')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('entries_count')
                    ->label('Entries')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('entries_sum_amount')
                    ->label('Spend volume')
                    ->numeric()
                    ->alignEnd()
                    ->sortable()
                    ->placeholder('0'),
                TextColumn::make('scans_used_this_month')
                    ->label('Scans (mo)')
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->date('d M Y')
                    ->sortable(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Accounts\Tables;

use App\Models\Account;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users'),
                TextColumn::make('plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Account::PLAN_PRO_LIFETIME => 'success',
                        Account::PLAN_PRO_MONTHLY => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('plan_expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y')
                    ->placeholder('·'),
                TextColumn::make('scans_used_this_month')->label('Scans')->sortable(),
                TextColumn::make('created_at')->label('Joined')->date('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('plan')->options([
                    Account::PLAN_FREE => 'Free',
                    Account::PLAN_PRO_MONTHLY => 'Pro monthly',
                    Account::PLAN_PRO_LIFETIME => 'Pro lifetime',
                ]),
            ])
            ->recordActions([EditAction::make()]);
    }
}

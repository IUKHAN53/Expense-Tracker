<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Account;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('account.name')
                    ->label('Household')
                    ->searchable()
                    ->url(fn ($record) => $record->account_id
                        ? route('filament.admin.resources.accounts.edit', ['record' => $record->account_id])
                        : null),
                TextColumn::make('account.plan')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        Account::PLAN_PRO_LIFETIME => 'success',
                        Account::PLAN_PRO_MONTHLY => 'info',
                        default => 'gray',
                    })
                    ->placeholder('·'),
                TextColumn::make('account.scans_used_this_month')
                    ->label('Scans')
                    ->numeric()
                    ->alignEnd()
                    ->placeholder('·'),
                IconColumn::make('is_super_admin')
                    ->label('Admin')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_super_admin')
                    ->label('SuperAdmin')
                    ->placeholder('All')
                    ->trueLabel('SuperAdmin only')
                    ->falseLabel('Regular users only'),
                SelectFilter::make('plan')
                    ->relationship('account', 'plan')
                    ->options([
                        Account::PLAN_FREE => 'Free',
                        Account::PLAN_PRO_MONTHLY => 'Pro monthly',
                        Account::PLAN_PRO_LIFETIME => 'Pro lifetime',
                    ])
                    ->label('Plan'),
            ])
            ->recordActions([EditAction::make()]);
    }
}

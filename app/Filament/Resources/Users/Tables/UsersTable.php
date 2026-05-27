<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Account;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->label('Delete')
                    ->modalHeading('Delete this user?')
                    ->modalDescription(fn (User $record) => "{$record->email} will be removed. If they are the last user on their household, that household and all its data will be deleted too.")
                    ->modalSubmitActionLabel('Delete forever')
                    ->successNotificationTitle('User deleted')
                    ->visible(fn (?User $record) => $record
                        && Auth::user()?->isSuperAdmin()
                        && $record->id !== Auth::id()),
                // The User model's `deleted` event takes care of token
                // revocation and account cleanup so the action body itself
                // can rely on Filament's default $record->delete().
            ]);
    }
}

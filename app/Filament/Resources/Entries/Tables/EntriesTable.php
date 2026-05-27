<?php

namespace App\Filament\Resources\Entries\Tables;

use App\Models\Account;
use App\Models\User;
use App\Support\MonthTableFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class EntriesTable
{
    public static function configure(Table $table): Table
    {
        $isSuperAdmin = (bool) Auth::user()?->isSuperAdmin();

        return $table
            ->defaultSort('purchased_at', 'desc')
            ->columns([
                TextColumn::make('purchased_at')
                    ->label('Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('item_name')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('spendingList.name')
                    ->label('List')
                    ->badge()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->money('PKR')
                    ->sortable()
                    ->summarize(Sum::make()->money('PKR')),
                TextColumn::make('quantity')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdBy.name')
                    ->label('Added by')
                    ->placeholder('·')
                    ->toggleable(),
                TextColumn::make('account.name')
                    ->label('Household')
                    ->visible($isSuperAdmin)
                    ->toggleable(),
                TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scan' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                MonthTableFilter::make('purchased_at'),
                SelectFilter::make('spending_list_id')
                    ->label('List')
                    ->relationship('spendingList', 'name'),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
                SelectFilter::make('source')
                    ->options([
                        'manual' => 'Manual',
                        'scan' => 'Receipt scan',
                    ]),
                SelectFilter::make('created_by_user_id')
                    ->label('Added by')
                    ->options(fn () => self::userOptions($isSuperAdmin))
                    ->searchable(),
                SelectFilter::make('account_id')
                    ->label('Household')
                    ->options(fn () => Account::orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->visible($isSuperAdmin),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * For SuperAdmin: every user across every account.
     * For regular admins: only their household's members.
     */
    private static function userOptions(bool $isSuperAdmin): array
    {
        $query = User::query()->orderBy('name');
        if (! $isSuperAdmin) {
            $query->where('account_id', Auth::user()?->account_id);
        }

        return $query->get(['id', 'name', 'email'])
            ->mapWithKeys(fn (User $u) => [$u->id => $u->name.' ('.$u->email.')'])
            ->all();
    }
}

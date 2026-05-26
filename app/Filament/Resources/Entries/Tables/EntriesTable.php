<?php

namespace App\Filament\Resources\Entries\Tables;

use App\Support\MonthTableFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EntriesTable
{
    public static function configure(Table $table): Table
    {
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
}

<?php

namespace App\Filament\Resources\FuelRecords\Tables;

use App\Support\MonthTableFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FuelRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('purchased_at', 'desc')
            ->columns([
                TextColumn::make('purchased_at')
                    ->label('Date')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('fuel_type')
                    ->badge()
                    ->color('danger')
                    ->placeholder('—'),
                TextColumn::make('odometer')
                    ->label('Odometer')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: ',')
                    ->suffix(' km')
                    ->sortable(),
                TextColumn::make('fuel_liters')
                    ->label('Litres')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' L')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total L')->numeric(decimalPlaces: 2)),
                TextColumn::make('fuel_rate')
                    ->label('Rate')
                    ->money('PKR')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Cost')
                    ->money('PKR')
                    ->sortable()
                    ->summarize(Sum::make()->money('PKR')),
                IconColumn::make('is_full_tank')
                    ->label('Full')
                    ->boolean(),
                TextColumn::make('notes')
                    ->wrap()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                MonthTableFilter::make('purchased_at'),
                SelectFilter::make('fuel_type')
                    ->options(['E92' => 'E92', 'E95' => 'E95', 'E98' => 'E98']),
                SelectFilter::make('is_full_tank')
                    ->label('Full tank')
                    ->options([1 => 'Full', 0 => 'Partial']),
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

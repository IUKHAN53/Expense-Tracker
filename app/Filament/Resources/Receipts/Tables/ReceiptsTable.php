<?php

namespace App\Filament\Resources\Receipts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Photo')
                    ->disk('public')
                    ->square(),
                TextColumn::make('merchant')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('receipt_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fuel' => 'danger',
                        'grocery' => 'success',
                        'pharmacy' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('total')
                    ->money('PKR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'assigned' => 'success',
                        'parsed' => 'info',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('purchased_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Scanned')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('receipt_type')
                    ->options([
                        'grocery' => 'Grocery',
                        'fuel' => 'Fuel',
                        'pharmacy' => 'Pharmacy',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'parsed' => 'Parsed',
                        'assigned' => 'Assigned',
                        'failed' => 'Failed',
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

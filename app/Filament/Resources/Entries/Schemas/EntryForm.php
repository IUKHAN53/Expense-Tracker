<?php

namespace App\Filament\Resources\Entries\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('spending_list_id')
                    ->label('List')
                    ->relationship('spendingList', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('item_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rs'),
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(0)
                    ->default(1),
                TextInput::make('unit')
                    ->maxLength(50)
                    ->placeholder('kg, ltr, pcs'),
                DateTimePicker::make('purchased_at')
                    ->required()
                    ->seconds(false)
                    ->default(now()),
                Select::make('source')
                    ->options([
                        'manual' => 'Manual',
                        'scan' => 'Receipt scan',
                    ])
                    ->default('manual')
                    ->required(),
                TextInput::make('fuel_liters')
                    ->label('Fuel litres')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('fuel_rate')
                    ->label('Fuel rate (Rs/L)')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('odometer')
                    ->numeric()
                    ->minValue(0),
                Textarea::make('notes')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }
}

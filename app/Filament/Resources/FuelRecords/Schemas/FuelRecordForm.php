<?php

namespace App\Filament\Resources\FuelRecords\Schemas;

use App\Models\Category;
use App\Models\SpendingList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FuelRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            DateTimePicker::make('purchased_at')
                ->label('Date')
                ->required()
                ->default(now())
                ->seconds(false),

            Select::make('fuel_type')
                ->label('Fuel type')
                ->options(['E92' => 'E92', 'E95' => 'E95', 'E98' => 'E98'])
                ->default('E92')
                ->required()
                ->native(false),

            TextInput::make('odometer')
                ->label('Odometer (km)')
                ->numeric()
                ->required()
                ->minValue(0),

            TextInput::make('fuel_rate')
                ->label('Rate (Rs/L)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->step(0.01)
                ->prefix('Rs'),

            TextInput::make('amount')
                ->label('Amount paid (Rs)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->prefix('Rs'),

            TextInput::make('fuel_liters')
                ->label('Litres')
                ->numeric()
                ->required()
                ->minValue(0)
                ->step(0.01)
                ->helperText('= Amount ÷ Rate'),

            Toggle::make('is_full_tank')
                ->label('Full tank')
                ->default(true),

            Textarea::make('notes')
                ->rows(2)
                ->columnSpanFull(),

            Select::make('spending_list_id')
                ->label('List')
                ->relationship('spendingList', 'name')
                ->default(fn () => SpendingList::where('type', SpendingList::TYPE_VEHICLE)->value('id'))
                ->required(),

            Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                ->default(fn () => Category::where('name', 'Fuel')->value('id')),
        ]);
    }
}

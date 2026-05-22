<?php

namespace App\Filament\Resources\SpendingLists\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SpendingListForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                Select::make('type')
                    ->options([
                        'person' => 'Person',
                        'household' => 'Home / General',
                        'vehicle' => 'Vehicle / Car',
                    ])
                    ->default('person')
                    ->required(),
                ColorPicker::make('color')
                    ->required()
                    ->default('#6366f1'),
                TextInput::make('icon')
                    ->maxLength(100)
                    ->helperText('Heroicon name, e.g. heroicon-o-user'),
                TextInput::make('monthly_budget')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rs')
                    ->helperText('Optional spending limit for this list.'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }
}

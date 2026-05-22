<?php

namespace App\Filament\Resources\BankMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sender'),
                Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('sms_hash')
                    ->required(),
                DateTimePicker::make('received_at'),
                TextInput::make('amount')
                    ->numeric(),
                TextInput::make('merchant'),
                TextInput::make('direction'),
                Toggle::make('is_transaction')
                    ->required(),
                Select::make('matched_list_id')
                    ->relationship('matchedList', 'name'),
                Select::make('entry_id')
                    ->relationship('entry', 'id'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('raw_json')
                    ->columnSpanFull(),
            ]);
    }
}

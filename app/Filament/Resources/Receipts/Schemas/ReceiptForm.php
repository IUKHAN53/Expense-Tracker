<?php

namespace App\Filament\Resources\Receipts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_path')
                    ->image(),
                TextInput::make('merchant'),
                TextInput::make('receipt_type')
                    ->required()
                    ->default('other'),
                TextInput::make('total')
                    ->numeric(),
                DateTimePicker::make('purchased_at'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('raw_json')
                    ->columnSpanFull(),
                Textarea::make('error')
                    ->columnSpanFull(),
            ]);
    }
}

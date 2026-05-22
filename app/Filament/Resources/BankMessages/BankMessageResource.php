<?php

namespace App\Filament\Resources\BankMessages;

use App\Filament\Resources\BankMessages\Pages\CreateBankMessage;
use App\Filament\Resources\BankMessages\Pages\EditBankMessage;
use App\Filament\Resources\BankMessages\Pages\ListBankMessages;
use App\Filament\Resources\BankMessages\Schemas\BankMessageForm;
use App\Filament\Resources\BankMessages\Tables\BankMessagesTable;
use App\Models\BankMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BankMessageResource extends Resource
{
    protected static ?string $model = BankMessage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'SMS Imports';

    public static function form(Schema $schema): Schema
    {
        return BankMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BankMessagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankMessages::route('/'),
            'create' => CreateBankMessage::route('/create'),
            'edit' => EditBankMessage::route('/{record}/edit'),
        ];
    }
}

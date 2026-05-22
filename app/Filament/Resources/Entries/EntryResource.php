<?php

namespace App\Filament\Resources\Entries;

use App\Filament\Resources\Entries\Pages\CreateEntry;
use App\Filament\Resources\Entries\Pages\EditEntry;
use App\Filament\Resources\Entries\Pages\ListEntries;
use App\Filament\Resources\Entries\Schemas\EntryForm;
use App\Filament\Resources\Entries\Tables\EntriesTable;
use App\Models\Entry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EntryResource extends Resource
{
    protected static ?string $model = Entry::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'item_name';

    public static function form(Schema $schema): Schema
    {
        return EntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntriesTable::configure($table);
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
            'index' => ListEntries::route('/'),
            'create' => CreateEntry::route('/create'),
            'edit' => EditEntry::route('/{record}/edit'),
        ];
    }
}

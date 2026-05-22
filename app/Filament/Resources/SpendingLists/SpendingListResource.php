<?php

namespace App\Filament\Resources\SpendingLists;

use App\Filament\Resources\SpendingLists\Pages\CreateSpendingList;
use App\Filament\Resources\SpendingLists\Pages\EditSpendingList;
use App\Filament\Resources\SpendingLists\Pages\ListSpendingLists;
use App\Filament\Resources\SpendingLists\Schemas\SpendingListForm;
use App\Filament\Resources\SpendingLists\Tables\SpendingListsTable;
use App\Models\SpendingList;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SpendingListResource extends Resource
{
    protected static ?string $model = SpendingList::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Lists';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SpendingListForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpendingListsTable::configure($table);
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
            'index' => ListSpendingLists::route('/'),
            'create' => CreateSpendingList::route('/create'),
            'edit' => EditSpendingList::route('/{record}/edit'),
        ];
    }
}

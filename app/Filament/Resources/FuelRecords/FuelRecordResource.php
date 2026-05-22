<?php

namespace App\Filament\Resources\FuelRecords;

use App\Filament\Resources\FuelRecords\Pages\CreateFuelRecord;
use App\Filament\Resources\FuelRecords\Pages\EditFuelRecord;
use App\Filament\Resources\FuelRecords\Pages\ListFuelRecords;
use App\Filament\Resources\FuelRecords\Schemas\FuelRecordForm;
use App\Filament\Resources\FuelRecords\Tables\FuelRecordsTable;
use App\Models\Entry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Dedicated admin section for fuel/refuel records. Backed by the Entry
 * model but scoped to rows that have litres set.
 */
class FuelRecordResource extends Resource
{
    protected static ?string $model = Entry::class;

    protected static ?string $slug = 'fuel-records';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Fuel';

    protected static ?string $recordTitleAttribute = 'item_name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('fuel_liters')
            ->where('fuel_liters', '>', 0);
    }

    public static function form(Schema $schema): Schema
    {
        return FuelRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FuelRecordsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFuelRecords::route('/'),
            'create' => CreateFuelRecord::route('/create'),
            'edit' => EditFuelRecord::route('/{record}/edit'),
        ];
    }
}

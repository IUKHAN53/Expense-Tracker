<?php

namespace App\Filament\Resources\Receipts;

use App\Filament\Resources\Receipts\Pages\ListReceipts;
use App\Filament\Resources\Receipts\Tables\ReceiptsTable;
use App\Models\Receipt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

/**
 * Receipts are produced by the AI scan flow; humans never hand-edit one.
 * The /admin surface is read-only: list page + the per-row View modal
 * (defined in ReceiptsTable) that enlarges the photo and shows parsed
 * metadata. No create, no edit.
 */
class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-camera';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return ReceiptsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceipts::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources\SpendingLists\Pages;

use App\Filament\Resources\SpendingLists\SpendingListResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpendingLists extends ListRecords
{
    protected static string $resource = SpendingListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

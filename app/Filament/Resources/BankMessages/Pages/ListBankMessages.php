<?php

namespace App\Filament\Resources\BankMessages\Pages;

use App\Filament\Resources\BankMessages\BankMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBankMessages extends ListRecords
{
    protected static string $resource = BankMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

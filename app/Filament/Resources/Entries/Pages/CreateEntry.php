<?php

namespace App\Filament\Resources\Entries\Pages;

use App\Filament\Resources\Entries\EntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEntry extends CreateRecord
{
    protected static string $resource = EntryResource::class;
}

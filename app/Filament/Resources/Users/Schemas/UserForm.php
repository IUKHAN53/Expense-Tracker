<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->schema([
                        Placeholder::make('name')
                            ->content(fn ($record) => $record?->name),
                        Placeholder::make('email')
                            ->content(fn ($record) => $record?->email),
                        Placeholder::make('account')
                            ->label('Household')
                            ->content(fn ($record) => $record?->account?->name ?? '·'),
                        Placeholder::make('joined')
                            ->content(fn ($record) => $record?->created_at?->format('d M Y · H:i')),
                    ])->columns(2),

                Section::make('Privileges')
                    ->description('Granting SuperAdmin gives this user cross-tenant access to /admin and every other household\'s data. Use carefully.')
                    ->schema([
                        Toggle::make('is_super_admin')
                            ->label('SuperAdmin')
                            ->onColor('danger'),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Accounts\Schemas;

use App\Models\Account;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(120),
            Select::make('plan')
                ->options([
                    Account::PLAN_FREE => 'Free',
                    Account::PLAN_PRO_MONTHLY => 'Pro (monthly)',
                    Account::PLAN_PRO_LIFETIME => 'Pro (lifetime)',
                ])
                ->required()
                ->native(false),
            DateTimePicker::make('plan_expires_at')
                ->label('Plan expires')
                ->helperText('Only meaningful for the monthly plan. Leave empty for lifetime/free.')
                ->seconds(false),
            TextInput::make('scans_used_this_month')
                ->label('Scans used this month')
                ->numeric()
                ->minValue(0)
                ->helperText('Reset to 0 to grant a fresh quota immediately.'),
        ]);
    }
}

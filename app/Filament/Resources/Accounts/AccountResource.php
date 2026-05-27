<?php

namespace App\Filament\Resources\Accounts;

use App\Filament\Resources\Accounts\Pages\EditAccount;
use App\Filament\Resources\Accounts\Pages\ListAccounts;
use App\Filament\Resources\Accounts\Schemas\AccountForm;
use App\Filament\Resources\Accounts\Tables\AccountsTable;
use App\Models\Account;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * SuperAdmin-only resource for browsing every tenant account, switching their
 * plan and resetting the monthly scan counter. Hidden from the /app panel.
 */
class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Belt-and-braces: canAccess blocks every route on the resource, not just
     * the index. A regular tenant who guessed /admin/accounts/3/edit gets 403.
     */
    public static function canAccess(): bool
    {
        return (bool) Auth::user()?->isSuperAdmin();
    }

    public static function canViewAny(): bool
    {
        return self::canAccess();
    }

    public static function canCreate(): bool
    {
        // Accounts are created via signup, not the admin panel.
        return false;
    }

    public static function canDelete($record): bool
    {
        // Soft-disable destructive action — wipes a tenant's data via cascade.
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return AccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccounts::route('/'),
            'edit' => EditAccount::route('/{record}/edit'),
        ];
    }
}

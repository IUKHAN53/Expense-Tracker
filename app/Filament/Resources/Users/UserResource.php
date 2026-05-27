<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * SuperAdmin-only user browser. Lists every signup with their account and
 * plan, lets the SuperAdmin grant or revoke the SuperAdmin flag on others.
 * Account-level deletion happens via /api/account from the user themselves;
 * this panel does not delete users.
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'email';

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
        // Users register via the mobile app's POST /api/register.
        return false;
    }

    /**
     * SuperAdmin can hard-delete any user except themselves. The delete
     * action revokes the user's tokens and cascades the household if
     * they were its last member.
     */
    public static function canDelete($record): bool
    {
        $current = Auth::user();

        return (bool) ($current?->isSuperAdmin()
            && $record
            && $record->id !== $current->id);
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // Eager-load the account so the table doesn't N+1 across plan + scans.
        return parent::getEloquentQuery()->with('account');
    }
}

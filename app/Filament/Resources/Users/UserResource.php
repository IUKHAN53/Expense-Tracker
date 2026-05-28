<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use App\Support\Impersonation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    /**
     * Shared "Impersonate" action used by both the table row and the profile
     * page header. Logs the SuperAdmin in as the target inside the /app panel;
     * a return banner there restores the original session.
     */
    public static function impersonateAction(): Action
    {
        return Action::make('impersonate')
            ->label('Impersonate')
            ->icon('heroicon-o-finger-print')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Impersonate user')
            ->modalDescription(fn (User $record) => "You'll be signed in as {$record->email} in the user app. Use the banner there to return.")
            ->modalSubmitActionLabel('Impersonate')
            ->visible(fn (?User $record) => $record
                && Auth::user()?->isSuperAdmin()
                && $record->id !== Auth::id()
                && $record->account_id)
            ->action(fn (User $record) => Impersonation::start($record));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Eager-load the account so the table doesn't N+1 across plan + scans.
        return parent::getEloquentQuery()->with('account');
    }
}

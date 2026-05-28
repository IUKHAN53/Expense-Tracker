<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'account_id', 'is_super_admin', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected static function booted(): void
    {
        // Whenever a User is deleted, also revoke their API tokens and (if
        // they were the last member of their household) drop the account so
        // the cascading FK clears every list/entry/receipt/category.
        // This fires from the Filament admin, the API, tinker — anywhere.
        static::deleting(function (User $user) {
            $user->tokens()->delete();
        });

        static::deleted(function (User $user) {
            if (! $user->account_id) {
                return;
            }
            $stillUsed = static::where('account_id', $user->account_id)->exists();
            if (! $stillUsed) {
                Account::query()->whereKey($user->account_id)->delete();
            }
        });
    }

    /**
     * Panel access:
     *  - /superadmin — SuperAdmins only (god-mode, cross-tenant).
     *  - /admin, /app — any user attached to a household (own tenant only).
     * A SuperAdmin without a household can still reach /superadmin but not
     * the per-account panels, which is fine — they manage everything there.
     * Each panel calls this with its own Panel instance.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'superadmin' => $this->isSuperAdmin(),
            'admin', 'app' => (bool) $this->account_id,
            default => false,
        };
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }
}

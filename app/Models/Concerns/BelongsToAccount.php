<?php

namespace App\Models\Concerns;

use App\Models\Account;
use App\Models\Scopes\AccountScope;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Adds the AccountScope global filter and auto-stamps `account_id` from
 * the current tenant when a new row is created. Models opt in with:
 *
 *     use BelongsToAccount;
 */
trait BelongsToAccount
{
    public static function bootBelongsToAccount(): void
    {
        static::addGlobalScope(new AccountScope);

        static::creating(function ($model) {
            if (! $model->account_id && ($user = TenantContext::user()) && $user->account_id) {
                $model->account_id = $user->account_id;
            }
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}

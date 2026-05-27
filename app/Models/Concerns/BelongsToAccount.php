<?php

namespace App\Models\Concerns;

use App\Models\Account;
use App\Models\Scopes\AccountScope;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the AccountScope global filter, auto-stamps `account_id` from
 * the current tenant, and (if the table has a `created_by_user_id`
 * column) stamps the creating user too. Models opt in with:
 *
 *     use BelongsToAccount;
 */
trait BelongsToAccount
{
    public static function bootBelongsToAccount(): void
    {
        static::addGlobalScope(new AccountScope);

        static::creating(function ($model) {
            $user = TenantContext::user();

            if (! $model->account_id && $user && $user->account_id) {
                $model->account_id = $user->account_id;
            }

            if ($user
                && empty($model->created_by_user_id)
                && in_array('created_by_user_id', $model->getFillable(), true)) {
                $model->created_by_user_id = $user->id;
            }
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

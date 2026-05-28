<?php

namespace App\Models\Scopes;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Restricts queries on tenant-scoped models (SpendingList, Entry, Receipt,
 * Category) to rows owned by the current tenant. The tenant is resolved
 * through TenantContext — normally the authenticated user, but seeders /
 * CLI commands / queue jobs run without one and bypass the filter so they
 * can operate on raw rows.
 *
 * SuperAdmins bypass the filter — they see every account's data in the
 * /superadmin Filament panel.
 */
class AccountScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = TenantContext::user();

        if (! $user) {
            return;
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return;
        }

        if (! $user->account_id) {
            // Authenticated but no account — safer to see nothing than everything.
            $builder->whereRaw('1 = 0');

            return;
        }

        $builder->where($model->qualifyColumn('account_id'), $user->account_id);
    }
}

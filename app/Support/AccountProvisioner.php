<?php

namespace App\Support;

use App\Models\Account;
use App\Models\Category;
use App\Models\SpendingList;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for creating a fresh Account: makes the row,
 * attaches the owner User, then seeds its starter spending lists and
 * categories. Used by the signup endpoint, the DatabaseSeeder, and the
 * backfill migration.
 */
class AccountProvisioner
{
    /** Default spending-list bundle: Home + Car. Persons are added by the user from the app. */
    public const DEFAULT_LISTS = [
        ['name' => 'Home', 'type' => SpendingList::TYPE_HOUSEHOLD, 'color' => '#0ea5e9', 'icon' => 'heroicon-o-home'],
        ['name' => 'Car', 'type' => SpendingList::TYPE_VEHICLE, 'color' => '#ef4444', 'icon' => 'heroicon-o-truck'],
    ];

    public const DEFAULT_CATEGORIES = [
        ['name' => 'Groceries', 'color' => '#22c55e'],
        ['name' => 'Vegetables & Fruit', 'color' => '#84cc16'],
        ['name' => 'Meat', 'color' => '#dc2626'],
        ['name' => 'Dairy', 'color' => '#fbbf24'],
        ['name' => 'Snacks', 'color' => '#f97316'],
        ['name' => 'Household', 'color' => '#0ea5e9'],
        ['name' => 'Personal Care', 'color' => '#a855f7'],
        ['name' => 'Medicine', 'color' => '#14b8a6'],
        ['name' => 'Fuel', 'color' => '#ef4444'],
        ['name' => 'Utilities', 'color' => '#6366f1'],
        ['name' => 'Other', 'color' => '#64748b'],
    ];

    /**
     * Create a new Account, attach the user, and seed default lists + categories.
     * Idempotent on re-runs only if the caller hands in a fresh user;
     * existing accounts are not touched.
     */
    public static function provision(User $user, string $accountName): Account
    {
        return DB::transaction(function () use ($user, $accountName) {
            $account = Account::create([
                'name' => $accountName,
                'plan' => Account::PLAN_FREE,
                'scans_used_this_month' => 0,
                'scans_reset_at' => now()->startOfMonth(),
            ]);

            $user->forceFill(['account_id' => $account->id])->save();

            self::seedDefaults($account);

            return $account;
        });
    }

    /** Seed the starter lists + categories onto an existing (empty) account. */
    public static function seedDefaults(Account $account): void
    {
        foreach (self::DEFAULT_LISTS as $i => $row) {
            SpendingList::withoutGlobalScopes()->create([
                'account_id' => $account->id,
                'name' => $row['name'],
                'type' => $row['type'],
                'color' => $row['color'],
                'icon' => $row['icon'],
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }

        foreach (self::DEFAULT_CATEGORIES as $row) {
            Category::withoutGlobalScopes()->create([
                'account_id' => $account->id,
                'name' => $row['name'],
                'color' => $row['color'],
            ]);
        }
    }
}

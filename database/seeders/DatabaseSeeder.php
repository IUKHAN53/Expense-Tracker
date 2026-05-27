<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\AccountProvisioner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the database with one SuperAdmin user and a starter account
     * containing the default 7 spending lists + 11 categories.
     */
    public function run(): void
    {
        $existing = User::where('email', 'admin@expense.app')->first();
        if ($existing) {
            // Make sure they remain SuperAdmin on re-seeds (e.g. RefreshDatabase).
            $existing->forceFill(['is_super_admin' => true])->save();
            if (! $existing->account_id) {
                AccountProvisioner::provision($existing, 'Admin Household');
            }

            return;
        }

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@expense.app',
            'password' => Hash::make('password'),
            'is_super_admin' => true,
            'email_verified_at' => now(),
        ]);

        AccountProvisioner::provision($user, 'Admin Household');
    }
}

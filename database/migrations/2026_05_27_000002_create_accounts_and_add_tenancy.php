<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Introduces multi-tenancy. Creates the `accounts` table, gives every
 * existing user an account (the first user becomes a SuperAdmin), and
 * re-parents all existing data (spending_lists / entries / receipts /
 * categories) to that account so single-tenant prod data is preserved.
 */
return new class extends Migration
{
    public function up(): void
    {
        // --- accounts ---------------------------------------------------------
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('plan')->default('free');     // free | pro_monthly | pro_lifetime
            $table->timestamp('plan_expires_at')->nullable();
            $table->unsignedInteger('scans_used_this_month')->default(0);
            $table->date('scans_reset_at')->nullable();
            $table->timestamps();
        });

        // --- users get account_id + is_super_admin ----------------------------
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('id')->constrained('accounts')->nullOnDelete();
            $table->boolean('is_super_admin')->default(false)->after('remember_token');
        });

        // --- scoped tables get account_id -------------------------------------
        foreach (['spending_lists', 'entries', 'receipts', 'categories'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->foreignId('account_id')->nullable()->after('id')->constrained('accounts')->cascadeOnDelete();
                $table->index('account_id', "{$tableName}_account_id_index_v2");
            });
        }

        // --- backfill ---------------------------------------------------------
        // If any users exist, take the first as the founding SuperAdmin and
        // re-parent every existing list/entry/receipt/category onto a single
        // new "Default" account. New installs hit this with zero users and
        // simply skip — the seeder builds a fresh account.
        $firstUser = DB::table('users')->orderBy('id')->first();

        if ($firstUser) {
            $accountId = DB::table('accounts')->insertGetId([
                'name' => $firstUser->name ? $firstUser->name."'s Household" : 'Default',
                'plan' => 'free',
                'scans_used_this_month' => 0,
                'scans_reset_at' => now()->startOfMonth()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->where('id', $firstUser->id)->update([
                'account_id' => $accountId,
                'is_super_admin' => true,
            ]);

            // Any other pre-existing users (unlikely for this app, but be safe):
            // give them the same account so the data stays accessible. They
            // do NOT become SuperAdmins.
            DB::table('users')->whereNull('account_id')->update(['account_id' => $accountId]);

            foreach (['spending_lists', 'entries', 'receipts', 'categories'] as $tableName) {
                DB::table($tableName)->whereNull('account_id')->update(['account_id' => $accountId]);
            }
        }
    }

    public function down(): void
    {
        foreach (['spending_lists', 'entries', 'receipts', 'categories'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                try {
                    $table->dropForeign(['account_id']);
                } catch (\Throwable) {
                    // SQLite or already-dropped — ignore.
                }
                $table->dropIndex("{$tableName}_account_id_index_v2");
                $table->dropColumn('account_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropForeign(['account_id']);
            } catch (\Throwable) {
                // ignore
            }
            $table->dropColumn(['account_id', 'is_super_admin']);
        });

        Schema::dropIfExists('accounts');
    }
};

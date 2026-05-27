<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Attribute every entry, receipt, spending list and category to the user
 * who created it. Existing rows get backfilled to their account's first
 * user (usually the founder) so the admin filters work on historical
 * data too. The column is nullable so direct DB inserts that bypass the
 * model still succeed.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['entries', 'receipts', 'spending_lists', 'categories'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                $t->foreignId('created_by_user_id')
                    ->nullable()
                    ->after('account_id')
                    ->constrained('users')
                    ->nullOnDelete();
                $t->index('created_by_user_id', "{$table}_created_by_index");
            });
        }

        // Backfill: for each row, set created_by to the account's earliest user.
        foreach (['entries', 'receipts', 'spending_lists', 'categories'] as $table) {
            $rows = DB::table($table)->whereNotNull('account_id')->select('id', 'account_id')->get();
            foreach ($rows as $row) {
                $firstUserId = DB::table('users')
                    ->where('account_id', $row->account_id)
                    ->orderBy('id')
                    ->value('id');

                if ($firstUserId) {
                    DB::table($table)->where('id', $row->id)->update(['created_by_user_id' => $firstUserId]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach (['entries', 'receipts', 'spending_lists', 'categories'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                try {
                    $t->dropForeign(['created_by_user_id']);
                } catch (\Throwable) {
                    // SQLite: no separate FK to drop.
                }
                $t->dropIndex("{$table}_created_by_index");
                $t->dropColumn('created_by_user_id');
            });
        }
    }
};

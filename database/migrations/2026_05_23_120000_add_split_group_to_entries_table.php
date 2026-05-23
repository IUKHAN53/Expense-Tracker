<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `split_group_id` lets one purchase be split across several people:
 * every share is its own Entry on the relevant list, all sharing the
 * same UUID so the app can label them as "Part of a split".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->string('split_group_id', 36)->nullable()->after('possible_duplicate_of_entry_id');
            $table->index('split_group_id');
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropIndex(['split_group_id']);
            $table->dropColumn('split_group_id');
        });
    }
};

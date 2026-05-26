<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SMS importer was removed (Play Store policy bans READ_SMS for non-default
 * messaging apps). Drop the bank_messages table and the FK column on entries.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('entries', 'bank_message_id')) {
            Schema::table('entries', function (Blueprint $table) {
                try {
                    $table->dropForeign(['bank_message_id']);
                } catch (\Throwable) {
                    // SQLite: no FK to drop separately.
                }
                $table->dropColumn('bank_message_id');
            });
        }

        Schema::dropIfExists('bank_messages');
    }

    public function down(): void
    {
        // One-way migration — SMS importer is gone for good.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the dedicated fuel-tracking fields (grade + full-tank flag) and a
 * column to remember which earlier entry a new one looks like a duplicate of.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->string('fuel_type', 16)->nullable()->after('fuel_rate');
            $table->boolean('is_full_tank')->nullable()->after('fuel_type');
            // Plain column (no FK) to avoid SQLite self-referencing ALTER quirks.
            $table->unsignedBigInteger('possible_duplicate_of_entry_id')
                ->nullable()
                ->after('bank_message_id');
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn(['fuel_type', 'is_full_tank', 'possible_duplicate_of_entry_id']);
        });
    }
};

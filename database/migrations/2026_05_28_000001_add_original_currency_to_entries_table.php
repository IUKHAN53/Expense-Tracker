<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Preserve what a foreign-currency purchase actually cost. `amount` stays the
 * value in the account's base currency (frozen at save time); these columns
 * record the original receipt currency + amount and the FX rate used, so the
 * app can show "Rs 2,800 (€10)" and the original never drifts when rates move.
 * Null for normal same-currency entries.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->decimal('original_amount', 14, 2)->nullable()->after('amount');
            $table->char('original_currency', 3)->nullable()->after('original_amount');
            $table->decimal('fx_rate', 18, 8)->nullable()->after('original_currency');
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn(['original_amount', 'original_currency', 'fx_rate']);
        });
    }
};

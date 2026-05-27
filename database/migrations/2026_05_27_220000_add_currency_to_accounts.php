<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Multi-currency support. Each Account picks one display currency on
 * first sign-in; it can be changed later from Settings. The column is
 * nullable so existing accounts (and the freshly-provisioned ones from
 * register) start unset and trigger the picker screen on the next
 * /me round-trip.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $t) {
            $t->string('currency', 3)->nullable()->after('plan_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $t) {
            $t->dropColumn('currency');
        });
    }
};

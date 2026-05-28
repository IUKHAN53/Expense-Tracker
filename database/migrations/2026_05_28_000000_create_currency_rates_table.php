<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reference FX rates, refreshed daily by the `fx:refresh` command. Rows are
 * stored USD-pivoted (base = USD); any pair is derived as a cross rate. This
 * is global reference data, NOT tenant-scoped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->char('base', 3)->default('USD');
            $table->char('quote', 3);
            $table->decimal('rate', 18, 8);
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->unique(['base', 'quote']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};

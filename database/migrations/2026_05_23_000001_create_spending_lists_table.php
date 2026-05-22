<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A "spending list" is one bucket money is tracked against:
 *  - person    : a member of the household (gets their own list)
 *  - household : the shared "Home / General" list (monthly groceries etc.)
 *  - vehicle   : the "Car" list (petrol receipts auto-route here)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spending_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('person'); // person | household | vehicle
            $table->string('color')->default('#6366f1');
            $table->string('icon')->nullable();
            $table->decimal('monthly_budget', 12, 2)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spending_lists');
    }
};

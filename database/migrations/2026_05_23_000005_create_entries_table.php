<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A single purchased item charged to one spending list.
 * `source` records where it came from: manual entry, a scanned
 * receipt, or an imported transaction SMS.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spending_list_id')->constrained('spending_lists')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('receipt_id')->nullable()->constrained('receipts')->nullOnDelete();
            $table->foreignId('bank_message_id')->nullable()->constrained('bank_messages')->nullOnDelete();
            $table->string('item_name');
            $table->decimal('amount', 12, 2);
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit')->nullable(); // kg, ltr, pcs, ...
            $table->timestamp('purchased_at');
            $table->string('source')->default('manual'); // manual | scan | sms
            $table->text('notes')->nullable();
            // Fuel-specific fields (populated for vehicle/petrol entries)
            $table->decimal('fuel_liters', 10, 2)->nullable();
            $table->decimal('fuel_rate', 10, 2)->nullable();
            $table->unsignedInteger('odometer')->nullable();
            $table->timestamps();

            $table->index(['spending_list_id', 'purchased_at']);
            $table->index('purchased_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};

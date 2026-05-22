<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A transaction SMS imported from the phone's inbox. `sms_hash` makes
 * imports idempotent. Gemini parses the body into amount/merchant;
 * fuel-station merchants get matched to the Car list automatically.
 *
 * `entry_id` is intentionally a plain column (no FK) to avoid a
 * circular dependency with the `entries` table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender')->nullable();
            $table->text('body');
            $table->string('sms_hash')->unique();
            $table->timestamp('received_at')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('merchant')->nullable();
            $table->string('direction')->nullable(); // debit | credit
            $table->boolean('is_transaction')->default(false);
            $table->foreignId('matched_list_id')->nullable()->constrained('spending_lists')->nullOnDelete();
            $table->unsignedBigInteger('entry_id')->nullable();
            $table->string('status')->default('pending'); // pending | parsed | imported | ignored | failed
            $table->json('raw_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_messages');
    }
};

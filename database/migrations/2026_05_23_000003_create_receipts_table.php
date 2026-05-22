<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A scanned bill/receipt photo. Gemini parses it into line items
 * which then become `entries`. `receipt_type` drives smart routing
 * (fuel -> Car list automatically).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('image_path')->nullable();
            $table->string('merchant')->nullable();
            $table->string('receipt_type')->default('other'); // grocery | fuel | pharmacy | other
            $table->decimal('total', 12, 2)->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->string('status')->default('pending'); // pending | parsed | assigned | failed
            $table->json('raw_json')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};

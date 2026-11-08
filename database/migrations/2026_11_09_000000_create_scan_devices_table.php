<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Free-scan counter for the standalone 2.x app: one row per install (hashed id), nothing else.
    public function up(): void
    {
        Schema::create('scan_devices', function (Blueprint $table) {
            $table->id();
            $table->char('device_hash', 64)->unique();
            $table->unsignedInteger('free_used')->default(0);
            $table->unsignedInteger('pro_used')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_devices');
    }
};

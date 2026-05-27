<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_verification_codes', function (Blueprint $t) {
            $t->id();
            $t->string('email')->index();
            $t->string('code', 6);
            $t->timestamp('expires_at');
            $t->timestamp('consumed_at')->nullable();
            $t->unsignedTinyInteger('attempts')->default(0);
            $t->timestamps();
        });

        // Grandfather every existing user as verified — they signed up before
        // verification was required and shouldn't be locked out by it.
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => DB::raw('CURRENT_TIMESTAMP')]);
    }

    public function down(): void
    {
        Schema::dropIfExists('email_verification_codes');
    }
};

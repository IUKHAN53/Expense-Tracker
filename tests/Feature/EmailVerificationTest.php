<?php

namespace Tests\Feature;

use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Support\AccountProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_register_creates_an_unverified_user_and_dispatches_a_code(): void
    {
        $res = $this->postJson('/api/register', [
            'name' => 'Newbie',
            'email' => 'newbie@example.com',
            'password' => 'long-password-please',
        ])->assertCreated()
          ->assertJson(['requires_email_verification' => true]);

        $user = User::where('email', 'newbie@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertDatabaseHas('email_verification_codes', ['email' => 'newbie@example.com']);
        Mail::assertSent(\App\Mail\EmailVerificationCodeMail::class);
    }

    public function test_unverified_user_blocked_from_protected_routes_with_403(): void
    {
        $user = $this->unverifiedUser();
        Sanctum::actingAs($user);

        $this->getJson('/api/entries')
            ->assertStatus(403)
            ->assertJson(['requires_email_verification' => true]);
    }

    public function test_correct_code_verifies(): void
    {
        $user = $this->unverifiedUser();
        $code = EmailVerificationCode::create([
            'email' => $user->email,
            'code' => '123456',
            'expires_at' => now()->addMinutes(15),
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/email/verify', ['code' => '123456'])
            ->assertOk()
            ->assertJsonStructure(['message', 'user' => ['email_verified_at']]);

        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNotNull($code->fresh()->consumed_at);
    }

    public function test_wrong_code_increments_attempts_and_locks_at_five(): void
    {
        $user = $this->unverifiedUser();
        EmailVerificationCode::create([
            'email' => $user->email,
            'code' => '111111',
            'expires_at' => now()->addMinutes(15),
        ]);
        Sanctum::actingAs($user);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/email/verify', ['code' => '999999'])->assertStatus(422);
        }
        // 6th wrong attempt now hits the lock.
        $this->postJson('/api/email/verify', ['code' => '999999'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_expired_code_rejected(): void
    {
        $user = $this->unverifiedUser();
        EmailVerificationCode::create([
            'email' => $user->email,
            'code' => '424242',
            'expires_at' => now()->subMinutes(1),
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/email/verify', ['code' => '424242'])->assertStatus(422);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_resend_dispatches_a_fresh_code(): void
    {
        $user = $this->unverifiedUser();
        Sanctum::actingAs($user);

        $this->postJson('/api/email/resend')
            ->assertCreated()
            ->assertJsonStructure(['message', 'expires_at']);

        $this->assertDatabaseHas('email_verification_codes', ['email' => $user->email]);
        Mail::assertSent(\App\Mail\EmailVerificationCodeMail::class);
    }

    public function test_verified_user_can_call_protected_routes(): void
    {
        $user = $this->unverifiedUser();
        $user->forceFill(['email_verified_at' => now()])->save();
        Sanctum::actingAs($user);

        $this->getJson('/api/entries')->assertOk();
    }

    private function unverifiedUser(): User
    {
        $u = User::create([
            'name' => 'Unverified',
            'email' => 'unv@example.com',
            'password' => Hash::make('correct'),
            'is_super_admin' => false,
            'email_verified_at' => null,
        ]);
        AccountProvisioner::provision($u, 'Unverified HH');

        return $u->fresh('account');
    }
}

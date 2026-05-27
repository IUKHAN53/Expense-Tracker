<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use App\Services\GeminiService;
use App\Support\AccountProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class ScanQuotaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Stub GeminiService so tests don't hit the real API. Returns a
        // minimal valid parse so the controller's happy path runs.
        $stub = Mockery::mock(GeminiService::class);
        $stub->shouldReceive('parseReceipt')->andReturn([
            'merchant' => 'Imtiaz',
            'receipt_type' => 'grocery',
            'total' => 500.00,
            'purchased_at' => '2026-05-27',
            'fuel_liters' => null,
            'fuel_rate' => null,
            'items' => [],
            'raw' => [],
        ]);
        $this->app->instance(GeminiService::class, $stub);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_free_account_can_scan_up_to_the_monthly_cap(): void
    {
        $user = $this->makeUser();
        Sanctum::actingAs($user);

        for ($i = 1; $i <= Account::FREE_SCANS_PER_MONTH; $i++) {
            $this->postJson('/api/receipts/scan', [
                'image' => UploadedFile::fake()->image("r{$i}.jpg", 100, 100)->size(200),
            ])->assertOk();
        }

        $this->assertSame(Account::FREE_SCANS_PER_MONTH, $user->fresh()->account->scansThisMonth());
    }

    public function test_free_account_blocked_after_the_cap_with_402(): void
    {
        $user = $this->makeUser();
        Sanctum::actingAs($user);

        // Burn the quota.
        for ($i = 1; $i <= Account::FREE_SCANS_PER_MONTH; $i++) {
            $this->postJson('/api/receipts/scan', [
                'image' => UploadedFile::fake()->image("r{$i}.jpg", 100, 100)->size(200),
            ])->assertOk();
        }

        // (cap + 1)-th scan must be rejected with Payment Required.
        $this->postJson('/api/receipts/scan', [
            'image' => UploadedFile::fake()->image('one-too-many.jpg', 100, 100)->size(200),
        ])
            ->assertStatus(402)
            ->assertJsonStructure(['message', 'scans_used', 'scans_free_quota', 'is_pro'])
            ->assertJson(['is_pro' => false]);
    }

    public function test_pro_account_has_no_cap(): void
    {
        $user = $this->makeUser();
        $user->account->forceFill([
            'plan' => Account::PLAN_PRO_LIFETIME,
        ])->save();
        Sanctum::actingAs($user);

        // 5 scans (well past free cap of 3) all succeed.
        for ($i = 1; $i <= 5; $i++) {
            $this->postJson('/api/receipts/scan', [
                'image' => UploadedFile::fake()->image("r{$i}.jpg", 100, 100)->size(200),
            ])->assertOk();
        }

        $this->assertSame(5, $user->fresh()->account->scansThisMonth());
    }

    public function test_failed_gemini_parse_does_not_consume_a_scan(): void
    {
        $stub = Mockery::mock(GeminiService::class);
        $stub->shouldReceive('parseReceipt')
            ->andThrow(new \RuntimeException('Gemini quota exhausted'));
        $this->app->instance(GeminiService::class, $stub);

        $user = $this->makeUser();
        Sanctum::actingAs($user);

        $this->postJson('/api/receipts/scan', [
            'image' => UploadedFile::fake()->image('r.jpg', 100, 100)->size(200),
        ])->assertStatus(422);

        // Counter should still be zero; we don't charge users for our outages.
        $this->assertSame(0, $user->fresh()->account->scansThisMonth());
    }

    private function makeUser(): User
    {
        $user = User::create([
            'name' => 'Scanner',
            'email' => 'scanner@example.com',
            'password' => Hash::make('secret'),
            'is_super_admin' => false,
            'email_verified_at' => now(),]);
        AccountProvisioner::provision($user, 'Scanner HH');

        return $user->fresh('account');
    }
}

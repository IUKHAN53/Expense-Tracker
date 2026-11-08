<?php

namespace Tests\Feature;

use App\Services\GeminiService;
use App\Services\PlayBilling;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class V2ScanTest extends TestCase
{
    use RefreshDatabase;

    private $gemini;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gemini = Mockery::mock(GeminiService::class);
        $this->app->instance(GeminiService::class, $this->gemini);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function scan(string $device, array $extra = [])
    {
        return $this->post('/api/v2/scan', [
            'image' => UploadedFile::fake()->image('r.jpg', 100, 100),
            'device_id' => $device,
            ...$extra,
        ], ['Accept' => 'application/json']);
    }

    private function parsed(): array
    {
        return ['receipt_type' => 'grocery', 'merchant' => 'Imtiaz', 'purchased_at' => null, 'total' => 500.0,
            'fuel_liters' => null, 'fuel_rate' => null, 'items' => [['name' => 'Milk', 'amount' => 500.0]], 'raw' => ['x' => 1]];
    }

    public function test_free_scans_run_out_then_paywall(): void
    {
        $this->gemini->shouldReceive('parseReceipt')->times(3)->andReturn($this->parsed());
        $device = (string) Str::uuid();

        foreach ([2, 1, 0] as $left) {
            $this->scan($device)->assertOk()->assertJsonPath('free_left', $left)->assertJsonMissingPath('scan.raw');
        }
        $this->scan($device)->assertStatus(402)->assertJsonPath('code', 'paywall');
    }

    public function test_failed_read_does_not_use_a_free_scan(): void
    {
        $this->gemini->shouldReceive('parseReceipt')->once()->andThrow(new RuntimeException('bad'));
        $device = (string) Str::uuid();

        $this->scan($device)->assertStatus(422)->assertJsonPath('free_left', 3);
        $this->assertSame(0, (int) \DB::table('scan_devices')->value('free_used'));
    }

    public function test_pro_token_bypasses_the_free_limit(): void
    {
        $this->gemini->shouldReceive('parseReceipt')->times(5)->andReturn($this->parsed());
        $play = Mockery::mock(PlayBilling::class);
        $play->shouldReceive('isActivePro')->with('tok')->andReturn(true);
        $this->app->instance(PlayBilling::class, $play);
        $device = (string) Str::uuid();

        for ($i = 0; $i < 5; $i++) {
            $this->scan($device, ['purchase_token' => 'tok'])->assertOk()->assertJsonPath('pro', true);
        }
    }

    public function test_invalid_token_is_treated_as_free(): void
    {
        $this->gemini->shouldReceive('parseReceipt')->once()->andReturn($this->parsed());
        $device = (string) Str::uuid();
        // PlayBilling is unconfigured in tests, so any token is not Pro.
        $this->scan($device, ['purchase_token' => 'fake'])->assertOk()->assertJsonPath('pro', false)->assertJsonPath('free_left', 2);
    }

    public function test_device_id_must_be_a_uuid(): void
    {
        $this->scan('not-a-uuid')->assertStatus(422)->assertJsonValidationErrors('device_id');
    }
}

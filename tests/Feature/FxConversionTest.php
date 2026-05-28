<?php

namespace Tests\Feature;

use App\Models\CurrencyRate;
use App\Models\Entry;
use App\Models\Receipt;
use App\Models\SpendingList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FxConversionTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(): array
    {
        $this->seed();
        $token = $this->postJson('/api/login', [
            'email' => 'admin@expense.app',
            'password' => 'password',
        ])->assertOk()->json('token');

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_fx_endpoint_returns_usd_pivoted_cross_rate(): void
    {
        $headers = $this->authHeaders();

        CurrencyRate::create(['base' => 'USD', 'quote' => 'PKR', 'rate' => 280, 'fetched_at' => now()]);
        CurrencyRate::create(['base' => 'USD', 'quote' => 'EUR', 'rate' => 0.9, 'fetched_at' => now()]);

        // 1 EUR = (USD->PKR)/(USD->EUR) = 280/0.9 PKR.
        $res = $this->getJson('/api/fx?from=EUR&to=PKR', $headers)->assertOk()->json();
        $this->assertEqualsWithDelta(280 / 0.9, $res['rate'], 0.01);
        $this->assertNotNull($res['as_of']);

        $this->getJson('/api/fx?from=PKR&to=PKR', $headers)->assertOk()->assertJsonPath('rate', 1);
    }

    public function test_fx_endpoint_requires_auth(): void
    {
        $this->getJson('/api/fx?from=USD&to=PKR')->assertUnauthorized();
    }

    public function test_confirm_converts_and_keeps_original_cost(): void
    {
        $headers = $this->authHeaders();

        $account = User::where('email', 'admin@expense.app')->firstOrFail()->account;
        $account->update(['currency' => 'PKR']);

        $list = SpendingList::where('type', 'household')->firstOrFail();
        $receipt = new Receipt(['image_path' => 'receipts/x.jpg', 'status' => 'parsed']);
        $receipt->account_id = $account->id;
        $receipt->save();

        $this->postJson("/api/receipts/{$receipt->id}/confirm", [
            'currency' => 'USD',
            'fx_rate' => 280,
            'items' => [[
                'spending_list_id' => $list->id,
                'item_name' => 'Imported gadget',
                'amount' => 10,
            ]],
        ], $headers)->assertCreated();

        $entry = Entry::where('item_name', 'Imported gadget')->firstOrFail();
        $this->assertEquals(2800, (float) $entry->amount);          // converted to base (PKR)
        $this->assertEquals(10, (float) $entry->original_amount);   // original (USD) preserved
        $this->assertSame('USD', $entry->original_currency);
        $this->assertEqualsWithDelta(280, (float) $entry->fx_rate, 0.0001);
    }

    public function test_confirm_same_currency_stores_no_original(): void
    {
        $headers = $this->authHeaders();

        $account = User::where('email', 'admin@expense.app')->firstOrFail()->account;
        $account->update(['currency' => 'PKR']);

        $list = SpendingList::where('type', 'household')->firstOrFail();
        $receipt = new Receipt(['image_path' => 'receipts/y.jpg', 'status' => 'parsed']);
        $receipt->account_id = $account->id;
        $receipt->save();

        $this->postJson("/api/receipts/{$receipt->id}/confirm", [
            'currency' => 'PKR',
            'fx_rate' => 1,
            'items' => [[
                'spending_list_id' => $list->id,
                'item_name' => 'Local milk',
                'amount' => 250,
            ]],
        ], $headers)->assertCreated();

        $entry = Entry::where('item_name', 'Local milk')->firstOrFail();
        $this->assertEquals(250, (float) $entry->amount);
        $this->assertNull($entry->original_amount);
        $this->assertNull($entry->original_currency);
    }

    public function test_fx_refresh_command_populates_rates(): void
    {
        Http::fake([
            'open.er-api.com/*' => Http::response([
                'result' => 'success',
                'time_last_update_unix' => now()->timestamp,
                'rates' => [
                    'USD' => 1, 'PKR' => 280.5, 'EUR' => 0.9, 'GBP' => 0.79, 'INR' => 83,
                    'BDT' => 110, 'LKR' => 300, 'AED' => 3.67, 'SAR' => 3.75, 'CAD' => 1.36,
                    'AUD' => 1.5, 'CNY' => 7.2,
                ],
            ], 200),
        ]);

        $this->artisan('fx:refresh')->assertSuccessful();

        $this->assertDatabaseHas('currency_rates', ['base' => 'USD', 'quote' => 'PKR']);
        $this->assertEqualsWithDelta(
            280.5,
            (float) CurrencyRate::where('quote', 'PKR')->firstOrFail()->rate,
            0.01,
        );
    }
}

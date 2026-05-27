<?php

namespace Tests\Feature;

use App\Models\SpendingList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the core API flow the mobile app depends on:
 * login, listing, creating an entry, and auth enforcement.
 */
class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_core_endpoints(): void
    {
        $this->seed();

        // Login returns a token.
        $login = $this->postJson('/api/login', [
            'email' => 'admin@expense.app',
            'password' => 'password',
        ])->assertOk()->json();

        $this->assertNotEmpty($login['token']);
        $headers = ['Authorization' => "Bearer {$login['token']}"];

        // Seeded lists: Home + Car. Users add Persons from the app.
        $this->getJson('/api/lists', $headers)
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/categories', $headers)->assertOk();

        // Create an entry on the Home list.
        $list = SpendingList::where('type', 'household')->first();
        $entry = $this->postJson('/api/entries', [
            'spending_list_id' => $list->id,
            'item_name' => 'Test rice 5kg',
            'amount' => 500,
        ], $headers)->assertCreated()->json();

        $this->assertEquals(500, $entry['data']['amount']);
        $this->assertSame('manual', $entry['data']['source']);

        // It shows up in the monthly summary.
        $this->getJson('/api/summary', $headers)->assertOk();

        // The entry can be deleted.
        $this->deleteJson("/api/entries/{$entry['data']['id']}", [], $headers)->assertOk();
    }

    public function test_protected_routes_require_a_token(): void
    {
        $this->getJson('/api/lists')->assertUnauthorized();
    }

    public function test_login_rejects_bad_credentials(): void
    {
        $this->seed();

        $this->postJson('/api/login', [
            'email' => 'admin@expense.app',
            'password' => 'wrong-password',
        ])->assertStatus(422);
    }
}

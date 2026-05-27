<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Entry;
use App\Models\SpendingList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Multi-tenancy boundary: registration provisions one account with the
 * default lists/categories, and one user's data is never visible to another.
 */
class TenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_provisions_account_with_default_lists_and_categories(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Aisha',
            'email' => 'aisha@example.com',
            'password' => 'secret12345',
        ])->assertCreated();

        $this->assertNotEmpty($response->json('token'));
        $this->assertSame('aisha@example.com', $response->json('user.email'));
        $this->assertSame('free', $response->json('user.account.plan'));
        $this->assertFalse($response->json('user.account.is_pro'));

        $user = User::where('email', 'aisha@example.com')->firstOrFail();
        $this->assertNotNull($user->account_id);
        $this->assertFalse($user->isSuperAdmin());

        $account = Account::find($user->account_id);
        $this->assertSame(7, $account->spendingLists()->count());
        $this->assertSame(11, $account->categories()->count());
    }

    public function test_register_rejects_duplicate_email(): void
    {
        $this->postJson('/api/register', [
            'name' => 'A', 'email' => 'dup@example.com', 'password' => 'secret12345',
        ])->assertCreated();

        $this->postJson('/api/register', [
            'name' => 'B', 'email' => 'dup@example.com', 'password' => 'secret12345',
        ])->assertStatus(422);
    }

    public function test_forgot_password_is_silent_about_unknown_emails(): void
    {
        $this->postJson('/api/forgot-password', [
            'email' => 'nobody@example.com',
        ])->assertOk()
          ->assertJsonStructure(['message']);
    }

    public function test_users_cannot_see_each_others_entries(): void
    {
        // Provision two distinct accounts via the public register endpoint.
        $this->postJson('/api/register', [
            'name' => 'A', 'email' => 'a@example.com', 'password' => 'secret12345',
        ])->assertCreated();
        $this->postJson('/api/register', [
            'name' => 'B', 'email' => 'b@example.com', 'password' => 'secret12345',
        ])->assertCreated();

        // The tenancy boundary is what we're testing here, not the verification
        // flow; mark both newly-registered users verified so the protected
        // routes don't 403 us before we can prove the scope works.
        User::query()->whereNull('email_verified_at')->update(['email_verified_at' => now()]);

        $userA = User::where('email', 'a@example.com')->firstOrFail();
        $userB = User::where('email', 'b@example.com')->firstOrFail();
        $listA = SpendingList::withoutGlobalScopes()->where('account_id', $userA->account_id)->first();
        $listB = SpendingList::withoutGlobalScopes()->where('account_id', $userB->account_id)->first();

        // A creates an entry on their own list.
        $this->asUser($userA)->postJson('/api/entries', [
            'spending_list_id' => $listA->id, 'item_name' => 'A-only milk', 'amount' => 100,
        ])->assertCreated();

        // B sees only their own 7 lists (not 14) and zero entries.
        $this->asUser($userB)->getJson('/api/lists')
            ->assertOk()
            ->assertJsonCount(7, 'data');
        $this->asUser($userB)->getJson('/api/entries')
            ->assertOk()
            ->assertJsonCount(0, 'data');

        // B cannot fetch A's entry by id (scope hides it → 404 via route model binding).
        $entryA = Entry::withoutGlobalScopes()->where('account_id', $userA->account_id)->first();
        $this->asUser($userB)->getJson("/api/entries/{$entryA->id}")->assertNotFound();

        // B cannot post against A's list either — validation rejects the unscoped FK.
        $this->asUser($userB)->postJson('/api/entries', [
            'spending_list_id' => $listA->id, 'item_name' => 'sneak', 'amount' => 50,
        ])->assertStatus(422);

        // But B CAN post against B's own list.
        $this->asUser($userB)->postJson('/api/entries', [
            'spending_list_id' => $listB->id, 'item_name' => 'B milk', 'amount' => 50,
        ])->assertCreated();
    }

    /** Reset the auth manager between requests so cached guards don't leak users. */
    private function asUser(User $user): self
    {
        $this->app['auth']->forgetGuards();

        return $this->actingAs($user, 'sanctum');
    }
}

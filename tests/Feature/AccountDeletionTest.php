<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Entry;
use App\Models\Receipt;
use App\Models\Scopes\AccountScope;
use App\Models\SpendingList;
use App\Models\User;
use App\Support\AccountProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_requires_correct_password_and_confirmation_word(): void
    {
        $user = $this->makeProvisionedUser();
        Sanctum::actingAs($user);

        $this->deleteJson('/api/account', [
            'password' => 'wrong-password',
            'confirmation' => 'DELETE',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['password']);

        $this->deleteJson('/api/account', [
            'password' => 'correct-passphrase',
            'confirmation' => 'delete',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['confirmation']);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_delete_removes_user_account_and_all_owned_data(): void
    {
        $user = $this->makeProvisionedUser();
        $accountId = $user->account_id;

        // Seed some data we expect to cascade away.
        $list = SpendingList::create([
            'account_id' => $accountId,
            'name' => 'Home',
            'type' => SpendingList::TYPE_HOUSEHOLD,
            'color' => '#000',
            'icon' => 'home',
        ]);
        Entry::create([
            'account_id' => $accountId,
            'spending_list_id' => $list->id,
            'item_name' => 'Bread',
            'amount' => 120,
            'purchased_at' => now(),
            'source' => Entry::SOURCE_MANUAL,
        ]);
        Category::create(['account_id' => $accountId, 'name' => 'Groceries', 'color' => '#000']);
        Receipt::create([
            'account_id' => $accountId,
            'merchant' => 'Imtiaz',
            'total' => 500,
            'status' => 'confirmed',
        ]);

        $user->tokens()->create(['name' => 'phone', 'token' => hash('sha256', 'tok'), 'abilities' => ['*']]);

        Sanctum::actingAs($user);

        $this->deleteJson('/api/account', [
            'password' => 'correct-passphrase',
            'confirmation' => 'DELETE',
        ])->assertOk()
          ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('accounts', ['id' => $accountId]);
        $this->assertDatabaseMissing('spending_lists', ['account_id' => $accountId]);
        $this->assertDatabaseMissing('entries', ['account_id' => $accountId]);
        $this->assertDatabaseMissing('categories', ['account_id' => $accountId]);
        $this->assertDatabaseMissing('receipts', ['account_id' => $accountId]);
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user->id]);
    }

    public function test_delete_does_not_touch_other_accounts(): void
    {
        $victim = $this->makeProvisionedUser('victim@example.com');
        $bystander = $this->makeProvisionedUser('bystander@example.com');

        // AccountProvisioner seeded each user's 7 default lists. Bypass the
        // tenant scope when counting so the victim's auth state can't hide rows.
        $bystanderListCount = SpendingList::withoutGlobalScope(AccountScope::class)
            ->where('account_id', $bystander->account_id)
            ->count();
        $this->assertGreaterThan(0, $bystanderListCount);

        Sanctum::actingAs($victim);

        $this->deleteJson('/api/account', [
            'password' => 'correct-passphrase',
            'confirmation' => 'DELETE',
        ])->assertOk();

        $this->assertDatabaseHas('users', ['id' => $bystander->id]);
        $this->assertDatabaseHas('accounts', ['id' => $bystander->account_id]);
        $this->assertSame(
            $bystanderListCount,
            SpendingList::withoutGlobalScope(AccountScope::class)
                ->where('account_id', $bystander->account_id)
                ->count(),
        );
    }

    public function test_delete_requires_authentication(): void
    {
        $this->deleteJson('/api/account', [
            'password' => 'anything',
            'confirmation' => 'DELETE',
        ])->assertStatus(401);
    }

    private function makeProvisionedUser(string $email = 'me@example.com'): User
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => $email,
            'password' => Hash::make('correct-passphrase'),
            'is_super_admin' => false,
            'email_verified_at' => now(),]);

        AccountProvisioner::provision($user, $user->name."'s Household");

        return $user->fresh('account');
    }
}

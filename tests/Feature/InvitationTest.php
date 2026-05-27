<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AccountInvitation;
use App\Models\User;
use App\Support\AccountProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_owner_can_invite_a_new_email(): void
    {
        $owner = $this->makeUser('owner@x.com');
        Sanctum::actingAs($owner);

        $this->postJson('/api/account/invitations', ['email' => 'hamza@example.com'])
            ->assertCreated()
            ->assertJsonStructure(['message', 'invitation' => ['id', 'email', 'expires_at']]);

        $this->assertDatabaseHas('account_invitations', [
            'account_id' => $owner->account_id,
            'email' => 'hamza@example.com',
        ]);
        Mail::assertSent(\App\Mail\AccountInvitationMail::class);
    }

    public function test_invitation_blocked_when_household_at_free_member_cap(): void
    {
        $owner = $this->makeUser('owner@x.com');
        // Add two more members to fill the Free cap of 3.
        $this->addMember($owner->account_id, 'two@x.com');
        $this->addMember($owner->account_id, 'three@x.com');

        Sanctum::actingAs($owner);
        $this->postJson('/api/account/invitations', ['email' => 'four@x.com'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_pro_household_has_higher_cap(): void
    {
        $owner = $this->makeUser('owner@x.com');
        $owner->account->forceFill(['plan' => Account::PLAN_PRO_LIFETIME])->save();

        $this->addMember($owner->account_id, 'two@x.com');
        $this->addMember($owner->account_id, 'three@x.com');
        $this->addMember($owner->account_id, 'four@x.com');

        Sanctum::actingAs($owner);
        // 5th slot (4 users + 1 invite) still under PRO_MAX_MEMBERS (5).
        $this->postJson('/api/account/invitations', ['email' => 'five@x.com'])
            ->assertCreated();

        // Sixth fails.
        $this->postJson('/api/account/invitations', ['email' => 'six@x.com'])
            ->assertStatus(422);
    }

    public function test_cannot_invite_existing_household_member(): void
    {
        $owner = $this->makeUser('owner@x.com');
        $this->addMember($owner->account_id, 'two@x.com');

        Sanctum::actingAs($owner);
        $this->postJson('/api/account/invitations', ['email' => 'two@x.com'])
            ->assertStatus(422);
    }

    public function test_new_user_can_accept_invitation(): void
    {
        $owner = $this->makeUser('owner@x.com');
        $inv = AccountInvitation::create([
            'account_id' => $owner->account_id,
            'invited_by_user_id' => $owner->id,
            'email' => 'newbie@x.com',
            'token' => AccountInvitation::newToken(),
            'expires_at' => now()->addDays(7),
        ]);

        $this->postJson("/api/invitations/{$inv->token}/accept", [
            'name' => 'Newbie',
            'password' => 'long-enough-password',
        ])->assertOk()
          ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'account_id']]);

        $newUser = User::where('email', 'newbie@x.com')->first();
        $this->assertNotNull($newUser);
        $this->assertSame($owner->account_id, $newUser->account_id);
        $this->assertSame($owner->account_id, $inv->fresh()->account_id);
        $this->assertNotNull($inv->fresh()->accepted_at);
    }

    public function test_existing_user_can_accept_and_old_account_dropped(): void
    {
        $owner = $this->makeUser('owner@x.com');
        $existing = $this->makeUser('existing@x.com');
        $oldAccountId = $existing->account_id;
        $this->assertNotSame($owner->account_id, $oldAccountId);

        $inv = AccountInvitation::create([
            'account_id' => $owner->account_id,
            'invited_by_user_id' => $owner->id,
            'email' => 'existing@x.com',
            'token' => AccountInvitation::newToken(),
            'expires_at' => now()->addDays(7),
        ]);

        $this->postJson("/api/invitations/{$inv->token}/accept", [
            'password' => 'correct-passphrase',
        ])->assertOk();

        $this->assertSame($owner->account_id, $existing->fresh()->account_id);
        $this->assertDatabaseMissing('accounts', ['id' => $oldAccountId]);
    }

    public function test_accept_requires_correct_password_for_existing_user(): void
    {
        $owner = $this->makeUser('owner@x.com');
        $this->makeUser('existing@x.com');

        $inv = AccountInvitation::create([
            'account_id' => $owner->account_id,
            'invited_by_user_id' => $owner->id,
            'email' => 'existing@x.com',
            'token' => AccountInvitation::newToken(),
            'expires_at' => now()->addDays(7),
        ]);

        $this->postJson("/api/invitations/{$inv->token}/accept", [
            'password' => 'wrong',
        ])->assertStatus(422);
    }

    public function test_expired_invitation_rejected(): void
    {
        $owner = $this->makeUser('owner@x.com');
        $inv = AccountInvitation::create([
            'account_id' => $owner->account_id,
            'invited_by_user_id' => $owner->id,
            'email' => 'stale@x.com',
            'token' => AccountInvitation::newToken(),
            'expires_at' => now()->subDays(1),
        ]);

        $this->postJson("/api/invitations/{$inv->token}/accept", [
            'name' => 'X',
            'password' => 'long-enough-password',
        ])->assertStatus(410);
    }

    public function test_owner_can_remove_member_but_not_self(): void
    {
        $owner = $this->makeUser('owner@x.com');
        $other = $this->addMember($owner->account_id, 'other@x.com');

        Sanctum::actingAs($owner);

        // Can't remove self.
        $this->deleteJson("/api/account/members/{$owner->id}")
            ->assertStatus(422);

        // Can remove someone else in the same household.
        $this->deleteJson("/api/account/members/{$other->id}")
            ->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $other->id]);
    }

    public function test_cannot_remove_user_from_a_different_household(): void
    {
        $owner = $this->makeUser('owner@x.com');
        $stranger = $this->makeUser('stranger@x.com');

        Sanctum::actingAs($owner);
        $this->deleteJson("/api/account/members/{$stranger->id}")
            ->assertForbidden();
    }

    private function makeUser(string $email): User
    {
        $u = User::create([
            'name' => 'U',
            'email' => $email,
            'password' => Hash::make('correct-passphrase'),
            'is_super_admin' => false,
        ]);
        AccountProvisioner::provision($u, 'HH '.$email);

        return $u->fresh('account');
    }

    private function addMember(int $accountId, string $email): User
    {
        return User::create([
            'name' => 'M',
            'email' => $email,
            'password' => Hash::make('correct-passphrase'),
            'is_super_admin' => false,
            'account_id' => $accountId,
        ]);
    }
}

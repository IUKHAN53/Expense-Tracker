<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AccountProvisioner;
use App\Support\Impersonation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_start_impersonation(): void
    {
        [$admin, $target] = $this->makeAdminAndTarget();

        $this->actingAs($admin)
            ->get('/impersonate/'.$target->id)
            ->assertRedirect('/app');

        $this->assertSame($admin->id, session(Impersonation::SESSION_KEY));
        $this->assertSame($target->id, auth()->id());
    }

    public function test_leaving_restores_the_super_admin(): void
    {
        [$admin, $target] = $this->makeAdminAndTarget();

        $this->actingAs($admin)->get('/impersonate/'.$target->id);

        $this->get('/impersonate-leave')->assertRedirect('/superadmin');

        $this->assertNull(session(Impersonation::SESSION_KEY));
        $this->assertSame($admin->id, auth()->id());
    }

    public function test_regular_user_cannot_impersonate(): void
    {
        [, $target] = $this->makeAdminAndTarget();
        $other = $this->makeUser('other@example.com', false);

        $this->actingAs($other)
            ->get('/impersonate/'.$target->id)
            ->assertForbidden();
    }

    public function test_super_admin_cannot_impersonate_self(): void
    {
        [$admin] = $this->makeAdminAndTarget();

        $this->actingAs($admin)
            ->get('/impersonate/'.$admin->id)
            ->assertForbidden();
    }

    public function test_cannot_impersonate_user_without_account(): void
    {
        [$admin] = $this->makeAdminAndTarget();
        $orphan = $this->makeUser('orphan@example.com', false); // no account provisioned

        $this->actingAs($admin)
            ->get('/impersonate/'.$orphan->id)
            ->assertForbidden();
    }

    /** @return array{0:User,1:User} */
    private function makeAdminAndTarget(): array
    {
        $admin = $this->makeUser('admin@example.com', true);
        AccountProvisioner::provision($admin, 'Admin HH');

        $target = $this->makeUser('target@example.com', false);
        AccountProvisioner::provision($target, 'Target HH');

        return [$admin->refresh(), $target->refresh()];
    }

    private function makeUser(string $email, bool $super): User
    {
        return User::create([
            'name' => 'U', 'email' => $email,
            'password' => Hash::make('secret12345'),
            'is_super_admin' => $super, 'email_verified_at' => now(),
        ]);
    }
}

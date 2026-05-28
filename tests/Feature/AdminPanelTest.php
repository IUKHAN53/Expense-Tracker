<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AccountProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Smoke test for the three Filament panels and their authorisation:
 *  - /superadmin — SuperAdmin-only god panel (cross-tenant resources + reports).
 *  - /admin, /app — per-account panels any household member may enter.
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_superadmin_pages_render_for_super_admin(): void
    {
        $this->seed();
        $superAdmin = User::where('is_super_admin', true)->firstOrFail();

        $pages = [
            '/superadmin',
            '/superadmin/spending-lists',
            '/superadmin/spending-lists/create',
            '/superadmin/entries',
            '/superadmin/entries/create',
            '/superadmin/categories',
            '/superadmin/categories/create',
            '/superadmin/receipts',
            '/superadmin/fuel-records',
            '/superadmin/fuel-records/create',
            '/superadmin/accounts',
            '/superadmin/users',
            '/superadmin/users/'.$superAdmin->id,
        ];

        foreach ($pages as $url) {
            $this->refreshAuth()->actingAs($superAdmin)
                ->get($url)
                ->assertOk();
        }
    }

    public function test_admin_panel_pages_render_for_account_holder(): void
    {
        $user = $this->makeAccountHolder();

        $pages = [
            '/admin',
            '/admin/reports',
            '/admin/spending-lists',
            '/admin/entries',
            '/admin/categories',
            '/admin/receipts',
            '/admin/fuel-records',
        ];

        foreach ($pages as $url) {
            $this->refreshAuth()->actingAs($user)->get($url)->assertOk();
        }
    }

    public function test_app_panel_pages_render_for_regular_user(): void
    {
        $user = $this->makeAccountHolder();

        $pages = [
            '/app',
            '/app/reports',
            '/app/spending-lists',
            '/app/entries',
            '/app/categories',
            '/app/receipts',
            '/app/fuel-records',
        ];

        foreach ($pages as $url) {
            $this->refreshAuth()->actingAs($user)->get($url)->assertOk();
        }

        // SuperAdmin-only resources must 403 on the per-account panels even
        // for an authenticated household member.
        $this->refreshAuth()->actingAs($user)->get('/app/accounts')->assertForbidden();
        $this->refreshAuth()->actingAs($user)->get('/app/users')->assertForbidden();
        $this->refreshAuth()->actingAs($user)->get('/admin/accounts')->assertForbidden();
        $this->refreshAuth()->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_account_holder_can_enter_admin_but_not_superadmin(): void
    {
        $user = $this->makeAccountHolder();

        $this->refreshAuth()->actingAs($user)->get('/admin')->assertOk();
        $this->refreshAuth()->actingAs($user)->get('/superadmin')->assertForbidden();
        $this->refreshAuth()->actingAs($user)->get('/superadmin/users')->assertForbidden();
        $this->refreshAuth()->actingAs($user)->get('/superadmin/accounts')->assertForbidden();
    }

    private function makeAccountHolder(): User
    {
        $user = User::create([
            'name' => 'Regular', 'email' => 'reg@example.com',
            'password' => Hash::make('secret12345'), 'is_super_admin' => false,
            'email_verified_at' => now(),
        ]);
        AccountProvisioner::provision($user, 'Reg HH');

        return $user;
    }

    private function refreshAuth(): self
    {
        $this->app['auth']->forgetGuards();

        return $this;
    }
}

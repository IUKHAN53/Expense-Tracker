<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AccountProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Smoke test: every Filament page (SuperAdmin /admin + user /app) renders
 * without error. Also checks panel-level authorisation: only SuperAdmins
 * may enter /admin, only account holders may enter /app.
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_admin_pages_render_for_super_admin(): void
    {
        $this->seed();
        $superAdmin = User::where('is_super_admin', true)->firstOrFail();

        $pages = [
            '/admin',
            '/admin/spending-lists',
            '/admin/spending-lists/create',
            '/admin/entries',
            '/admin/entries/create',
            '/admin/categories',
            '/admin/categories/create',
            '/admin/receipts',
            '/admin/receipts/create',
            '/admin/fuel-records',
            '/admin/fuel-records/create',
            '/admin/accounts',
            '/admin/users',
        ];

        foreach ($pages as $url) {
            $this->refreshAuth()->actingAs($superAdmin)
                ->get($url)
                ->assertOk();
        }
    }

    public function test_app_panel_pages_render_for_regular_user(): void
    {
        $user = User::create([
            'name' => 'Regular', 'email' => 'reg@example.com',
            'password' => Hash::make('secret12345'), 'is_super_admin' => false,
            'email_verified_at' => now(),]);
        AccountProvisioner::provision($user, 'Reg HH');

        $pages = [
            '/app',
            '/app/spending-lists',
            '/app/entries',
            '/app/categories',
            '/app/receipts',
            '/app/fuel-records',
        ];

        foreach ($pages as $url) {
            $this->refreshAuth()->actingAs($user)->get($url)->assertOk();
        }

        // SuperAdmin-only resources must 403 on the /app panel even for
        // an authenticated regular user.
        $this->refreshAuth()->actingAs($user)->get('/app/accounts')->assertForbidden();
        $this->refreshAuth()->actingAs($user)->get('/app/users')->assertForbidden();
    }

    public function test_regular_user_cannot_enter_admin_panel(): void
    {
        $user = User::create([
            'name' => 'Regular', 'email' => 'reg@example.com',
            'password' => Hash::make('secret12345'), 'is_super_admin' => false,
            'email_verified_at' => now(),]);
        AccountProvisioner::provision($user, 'Reg HH');

        $this->refreshAuth()->actingAs($user)->get('/admin')->assertForbidden();
    }

    private function refreshAuth(): self
    {
        $this->app['auth']->forgetGuards();

        return $this;
    }
}

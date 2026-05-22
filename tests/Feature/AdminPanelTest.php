<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke test: every Filament admin page (dashboard, resource lists and
 * forms) renders without error. Catches broken column/field config.
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_admin_pages_render(): void
    {
        $this->seed();
        $user = User::first();

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
            '/admin/bank-messages',
            '/admin/bank-messages/create',
        ];

        foreach ($pages as $url) {
            $this->actingAs($user)
                ->get($url)
                ->assertOk();
        }
    }
}

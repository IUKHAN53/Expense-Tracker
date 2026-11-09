<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_renders_marketing_landing(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('A household ledger', false);
        $response->assertSee('Where the', false);
    }

    public function test_marketing_pages_render(): void
    {
        $this->get('/pricing')->assertOk()->assertSee('Pro', false);
        $this->get('/about')->assertOk()->assertSee('Irfan Ullah', false);
        $this->get('/contact')->assertOk()->assertSee('hello@iukhan.tech', false);
        $this->get('/privacy')->assertOk()->assertSee('Privacy notice', false);
        $this->get('/terms')->assertOk()->assertSee('Terms of use', false);
    }

    public function test_pricing_matches_google_play_and_has_no_1x_leftovers(): void
    {
        $this->get('/pricing?cc=PK')->assertOk()
            ->assertSee('PKR', false)->assertSee('300', false)->assertSee('2,500', false)
            ->assertSee('id=com.iukhan.kharcha', false);

        foreach (['/', '/pricing', '/about', '/contact', '/terms'] as $url) {
            $this->get($url)->assertOk()
                ->assertDontSee('/admin/login', false)
                ->assertDontSee('Lifetime', false)
                ->assertDontSee('household member', false);
        }
    }

    public function test_sitemap_is_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $response->assertSee('<urlset', false);
        $response->assertSee('/pricing', false);
    }
}

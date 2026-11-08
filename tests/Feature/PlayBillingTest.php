<?php

namespace Tests\Feature;

use App\Services\PlayBilling;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PlayBillingTest extends TestCase
{
    private string $keyFile;

    protected function setUp(): void
    {
        parent::setUp();
        // Windows PHP builds ship without a default openssl.cnf; point at the bundled one if present.
        $cnf = getenv('OPENSSL_CONF') ?: (is_file($f = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf') ? $f : null);
        $opts = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA] + ($cnf ? ['config' => $cnf] : []);
        $pk = openssl_pkey_new($opts);
        openssl_pkey_export($pk, $pem, null, $opts);
        $this->keyFile = tempnam(sys_get_temp_dir(), 'sa');
        file_put_contents($this->keyFile, json_encode(['client_email' => 'svc@x.iam.gserviceaccount.com', 'private_key' => $pem]));
        config(['services.google_play.key_path' => $this->keyFile, 'services.google_play.product_id' => 'kharcha_pro']);
    }

    protected function tearDown(): void
    {
        @unlink($this->keyFile);
        parent::tearDown();
    }

    private function fakeSub(array $sub, int $status = 200): void
    {
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['access_token' => 'at']),
            'androidpublisher.googleapis.com/*' => Http::response($sub, $status),
        ]);
    }

    public function test_active_subscription_is_pro_and_request_is_signed(): void
    {
        $this->fakeSub(['subscriptionState' => 'SUBSCRIPTION_STATE_ACTIVE', 'lineItems' => [['productId' => 'kharcha_pro']]]);

        $this->assertTrue(app(PlayBilling::class)->isActivePro('tok-a'));
        Http::assertSent(fn ($r) => str_contains($r->url(), 'oauth2') && count(explode('.', $r['assertion'])) === 3);
        Http::assertSent(fn ($r) => str_contains($r->url(), '/applications/com.iukhan.kharcha/purchases/subscriptionsv2/tokens/tok-a')
            && $r->hasHeader('Authorization', 'Bearer at'));
    }

    public function test_expired_or_other_product_or_unknown_token_is_not_pro(): void
    {
        $this->fakeSub(['subscriptionState' => 'SUBSCRIPTION_STATE_EXPIRED', 'lineItems' => [['productId' => 'kharcha_pro']]]);
        $this->assertFalse(app(PlayBilling::class)->isActivePro('tok-b'));

        $this->fakeSub(['subscriptionState' => 'SUBSCRIPTION_STATE_ACTIVE', 'lineItems' => [['productId' => 'other']]]);
        $this->assertFalse(app(PlayBilling::class)->isActivePro('tok-c'));

        $this->fakeSub([], 410);
        $this->assertFalse(app(PlayBilling::class)->isActivePro('tok-d'));
    }
}

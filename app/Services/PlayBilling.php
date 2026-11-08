<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Verifies Kharcha Pro subscriptions with the Google Play Developer API,
 * using a service account key (JSON) that has access to the Play Console app.
 */
class PlayBilling
{
    private const ACTIVE = ['SUBSCRIPTION_STATE_ACTIVE', 'SUBSCRIPTION_STATE_IN_GRACE_PERIOD'];

    public function isConfigured(): bool
    {
        $path = (string) config('services.google_play.key_path');

        return $path !== '' && is_readable($path);
    }

    /** True when the purchase token is a currently active Pro subscription. */
    public function isActivePro(string $purchaseToken): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        return Cache::remember('play-sub:'.hash('sha256', $purchaseToken), now()->addMinutes(30), function () use ($purchaseToken) {
            try {
                $sub = $this->subscription($purchaseToken);
            } catch (RuntimeException $e) {
                Log::warning('Play subscription check failed', ['error' => $e->getMessage()]);

                return false;
            }

            $productId = (string) config('services.google_play.product_id');
            $hasProduct = collect($sub['lineItems'] ?? [])->contains(fn ($li) => ($li['productId'] ?? null) === $productId);

            return $hasProduct && in_array($sub['subscriptionState'] ?? '', self::ACTIVE, true);
        });
    }

    /** @return array<string,mixed> */
    private function subscription(string $purchaseToken): array
    {
        $package = (string) config('services.google_play.package');
        $url = 'https://androidpublisher.googleapis.com/androidpublisher/v3/applications/'
            .rawurlencode($package).'/purchases/subscriptionsv2/tokens/'.rawurlencode($purchaseToken);

        $res = Http::timeout(15)->withToken($this->accessToken())->get($url);
        if ($res->status() === 404 || $res->status() === 400 || $res->status() === 410) {
            return []; // unknown / invalid token: not Pro
        }
        if ($res->failed()) {
            throw new RuntimeException('Play API error '.$res->status());
        }

        return (array) $res->json();
    }

    private function accessToken(): string
    {
        return Cache::remember('play-access-token', now()->addMinutes(50), function () {
            $key = json_decode((string) file_get_contents((string) config('services.google_play.key_path')), true);
            if (! is_array($key) || empty($key['client_email']) || empty($key['private_key'])) {
                throw new RuntimeException('Invalid Google service account key file.');
            }

            $now = time();
            $b64 = fn (string $s) => rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
            $unsigned = $b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])).'.'.$b64(json_encode([
                'iss' => $key['client_email'],
                'scope' => 'https://www.googleapis.com/auth/androidpublisher',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]));
            if (! openssl_sign($unsigned, $sig, $key['private_key'], OPENSSL_ALGO_SHA256)) {
                throw new RuntimeException('Could not sign the Google token request.');
            }

            $res = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $unsigned.'.'.$b64($sig),
            ]);
            if ($res->failed() || ! $res->json('access_token')) {
                throw new RuntimeException('Google token exchange failed ('.$res->status().').');
            }

            return (string) $res->json('access_token');
        });
    }
}

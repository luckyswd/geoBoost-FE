<?php

declare(strict_types=1);

namespace App\Services\Shopify;

use App\Entity\Shop;
use App\Services\Cache\Redis;
use App\Services\ShopLogger;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShopifyService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public static function shopifyInstallValidation(
        string|null $domain,
        string|null $code,
        string|null $hmac,
        array $query
    ): void {
        if (!$domain || !$code || !$hmac) {
            ShopLogger::create($domain)->info("Missing parameters. domain: $domain code: $code hmac: $hmac");

            throw new \Exception('Missing parameters.');
        }

        unset($query['hmac']);
        ksort($query);

        $computedHmac = hash_hmac('sha256', http_build_query($query), getenv('SHOPIFY_SECRET_KEY'));

        if (!hash_equals($hmac, $computedHmac)) {
            ShopLogger::create($domain)->info("Invalid HMAC validation.");

            throw new \Exception('Invalid HMAC validation.');
        }
    }

    public function registerAppUninstalledWebhook(Shop $shop): void
    {
        $canonicalHost = getenv('CANONICAL_HOST');
        $apiVersion = getenv('SHOPIFY_API_VERSION');
        $domain = $shop->getDomain();

        $response = $this->httpClient->request("POST", "https://$domain/admin/api/$apiVersion/webhooks.json", [
            'headers' => [
                'X-Shopify-Access-Token' => $shop->getAccessToken(),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'webhook' => [
                    'topic' => 'app/uninstalled',
                    'address' => "$canonicalHost/shopify/webhook/uninstalled",
                    'format' => 'json',
                ],
            ],
        ]);

        if ($response->getStatusCode() !== 201) {
            #TODO add logs
        }
    }

    public static function getRedirectUrl(string $domain): string {
        $shopifyApiKey = getenv('SHOPIFY_API_KEY');

        return "https://$domain/admin/apps/$shopifyApiKey";
    }

    public function getAccessToken(
        string $domain,
        string $code,
    ): string {
        $shopifyApiKey = getenv('SHOPIFY_API_KEY');
        $redisKeyAccessToken = "$domain:access_token";

        if (Redis::get($redisKeyAccessToken)) {
            return Redis::get($redisKeyAccessToken);
        }

        $accessTokenResponse = $this->httpClient->request('POST', "https://$domain/admin/oauth/access_token", [
            'json' => [
                'client_id' => $shopifyApiKey,
                'client_secret' => getenv('SHOPIFY_SECRET_KEY'),
                'code' => $code,
            ]
        ]);

        $responseData = $accessTokenResponse->toArray();
        $accessToken = $responseData['access_token'] ?? null;

        if (!$accessToken) {
            ShopLogger::create($domain)->info("Missing access_token");

            throw new \Exception('Missing access_token');
        }

        Redis::set($redisKeyAccessToken, $accessToken);

        return $accessToken;
    }
}

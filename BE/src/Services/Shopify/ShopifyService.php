<?php

declare(strict_types=1);

namespace App\Services\Shopify;

use App\Entity\Shop;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShopifyService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public static function shopifyInstallValidation(
        string|null $domain,
        string|null $code,
        string|null $hmac,
        array $query,
        LoggerInterface $logger,
    ): void {
        if (!$domain || !$code || !$hmac) {
            $logger->error("Missing parameters. domain: $domain code: $code hmac: $hmac");

            throw new \Exception('Missing parameters.');
        }

        unset($query['hmac']);
        ksort($query);

        $computedHmac = hash_hmac('sha256', http_build_query($query), getenv('SHOPIFY_SECRET_KEY'));

        if (!hash_equals($hmac, $computedHmac)) {
            $logger->error("Invalid HMAC validation. domain: $domain code: $code hmac: $hmac");

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
            $this->logger->error("Fail registerAppUninstalledWebhook domain: $domain");
        }
    }
}

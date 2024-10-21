<?php

namespace App\Services\Shopify;

use App\Entity\Shop;
use App\Enum\Scopes;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Graphql;
use Shopify\Clients\Rest;
use Shopify\Context;

class ShopifyApiService
{
    public static Rest|Graphql|null $client = null;

    // Example use:  $this->getClient()->get('products', ['limit' => $limit]);
    public static function client(
        Shop $shop,
        bool $isGraphql = false,
    ): Rest|Graphql {
        if (self::$client) {
            return self::$client;
        }

        Context::initialize(
            apiKey: getenv('SHOPIFY_API_KEY'),
            apiSecretKey: getenv('SHOPIFY_SECRET_KEY'),
            scopes: Scopes::getAll(),
            hostName: $shop->getDomain(),
            sessionStorage: new FileSessionStorage('/tmp/php_sessions'),
            apiVersion: getenv('SHOPIFY_API_VERSION'),
        );

        if ($isGraphql) {
            self::$client = new Graphql($shop->getDomain(), $shop->getAccessToken());
        } else {
            self::$client = new Rest($shop->getDomain(), $shop->getAccessToken());
        }

        return self::$client;
    }
}
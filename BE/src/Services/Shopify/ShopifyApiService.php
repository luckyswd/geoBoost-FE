<?php

namespace App\Services\Shopify;

use App\Entity\Shop;
use App\Enum\Scopes;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Rest;
use Shopify\Context;

class ShopifyApiService
{
    // Example use:  $this->getClient()->get('products', ['limit' => $limit]);
    public static function client(
        Shop $shop
    ): Rest {
        Context::initialize(
            apiKey: getenv('SHOPIFY_API_KEY'),
            apiSecretKey: getenv('SHOPIFY_SECRET_KEY'),
            scopes: Scopes::getAll(),
            hostName: $shop->getDomain(),
            sessionStorage: new FileSessionStorage('/tmp/php_sessions'),
            apiVersion: getenv('SHOPIFY_API_VERSION'),
        );

        return new Rest($shop->getDomain(), $shop->getAccessToken());
    }
}
<?php

namespace App\Services\Shopify\RESTAdminAPI;

use App\Entity\Shop;
use App\Services\Shopify\ShopifyApiService;
use Shopify\Clients\Rest;

abstract class BaseAdminAPI
{
    protected readonly string $shopifyApiUrl;
    protected readonly string $accessToken;
    protected readonly Rest $httpClient;
    protected readonly Shop $shop;
    protected readonly string $apiVersion;

    public function __construct(
        Shop $shop,
    ) {
        $this->shopifyApiUrl = 'https://' . $shop->getDomain();
        $this->httpClient = ShopifyApiService::client($shop);
        $this->apiVersion = getenv('SHOPIFY_API_VERSION');
        $this->shop = $shop;
    }
}

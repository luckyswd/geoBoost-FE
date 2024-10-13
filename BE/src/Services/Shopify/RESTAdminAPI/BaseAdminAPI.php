<?php

namespace App\Services\Shopify\RESTAdminAPI;

use App\Entity\Shop;
use App\Services\Shopify\ShopifyApiService;
use Psr\Log\LoggerInterface;
use Shopify\Auth\Session;
use Shopify\Clients\Rest;
use Shopify\Utils;

abstract class BaseAdminAPI
{
    protected string $shopifyApiUrl;
    protected string $accessToken;
    protected Rest $httpClient;

    public function __construct(
        Shop $shop,
        LoggerInterface $logger,
    )
    {
        $this->shopifyApiUrl = 'https://' . $shop->getDomain();
        $this->httpClient = ShopifyApiService::client($shop);
        $this->apiVersion = getenv('SHOPIFY_API_VERSION');
        $this->shop = $shop;
        $this->logger = $logger;
    }
}
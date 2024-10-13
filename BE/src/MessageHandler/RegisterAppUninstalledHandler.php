<?php

namespace App\MessageHandler;

use App\Message\RegisterAppUninstalled;
use App\Repository\ShopRepository;
use App\Services\Shopify\ShopifyService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

//EXAMPLE
#[AsMessageHandler]
class RegisterAppUninstalledHandler
{
    public function __construct(
        private ShopRepository $shopRepository,
        private ShopifyService $shopifyService,
    ) {
    }

    public function __invoke(RegisterAppUninstalled $registerAppUninstalled): void
    {
        $shop = $this->shopRepository->findOneBy(['id' => $registerAppUninstalled->getShopId()]);

        $this->shopifyService->registerAppUninstalledWebhook($shop);
    }
}

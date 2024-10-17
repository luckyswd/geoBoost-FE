<?php

namespace App\Handler\Setting;

use App\Entity\Shop;
use App\Services\Shopify\RESTAdminAPI\OnlineStore\ScriptTagService;
use App\Services\ShopLogger;

class ActivatedHandler
{
    public function __invoke(
        Shop $shop,
        bool $isActive,
    ): void {
        $scriptTagService = new ScriptTagService($shop);

        if ($isActive) {
            $scriptTagService->addCustomScriptTag('https://staging-truewealthadvisorygroup.kinsta.cloud/app/themes/twag/dist/scripts/popup.js');
        } else {
//            $scriptTagService->deleteCustomScriptTag();
        }
    }
}
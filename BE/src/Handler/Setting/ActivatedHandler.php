<?php

namespace App\Handler\Setting;

use App\Entity\Shop;
use App\Services\Shopify\RESTAdminAPI\OnlineStore\ScriptTagService;

class ActivatedHandler
{
    public function __invoke(
        Shop $shop,
        bool $isAdd,
    ): void {
        $scriptTagService = new ScriptTagService($shop);

        if ($isAdd) {
            $scriptTagService->addCustomScriptTag(getenv('CANONICAL_HOST') . '/popup.js');
        } else {
            $scriptTagService->deleteCustomScriptTag(getenv('CANONICAL_HOST') . '/popup.js');
        }
    }
}
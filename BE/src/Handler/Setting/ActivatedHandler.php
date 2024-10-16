<?php

namespace App\Handler\Setting;

use App\Entity\Shop;
use App\Services\Shopify\RESTAdminAPI\OnlineStore\ScriptTagService;

class ActivatedHandler
{
    public function __invoke(
        Shop $shop,
        bool $isActive,
    ): void {
        if ($isActive) {
            (new ScriptTagService($shop))->addCustomScriptTag('https://staging-truewealthadvisorygroup.kinsta.cloud/app/themes/twag/dist/scripts/popup.js');
        }
    }
}
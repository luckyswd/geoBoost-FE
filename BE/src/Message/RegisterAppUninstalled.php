<?php

namespace App\Message;

//EXAMPLE
readonly class RegisterAppUninstalled
{
    public function __construct(
        private int $shopId
    ) {
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }
}

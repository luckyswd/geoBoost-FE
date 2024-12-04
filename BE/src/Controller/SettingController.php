<?php

namespace App\Controller;

use App\Enum\SettingKey;
use App\Handler\Setting\ActivatedHandler;
use App\Services\Setting\SettingService;
use App\Services\Shop\ShopService;
use App\Services\ShopLogger;
use App\Traits\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/setting')]
class SettingController extends AbstractController
{
    use ApiResponseTrait;

    #[Route('/', name: 'get_setting', methods: ["GET"])]
    public function getSetting(
        SettingService $settingService,
        Request $request,
    ): JsonResponse {
        $shop = ShopService::getShop($request);
        $key = $request->get('key');

        if ($key !== 'all' && !SettingKey::tryFrom($key)) {
            return $this->error('Invalid key provided', Response::HTTP_BAD_REQUEST);
        }

        try {
            if ($key === 'all') {
                return $this->success($settingService->getAllSetting(shop: $shop));
            }

            return $this->success([
                'value' => $settingService->getValueByKey($shop, $key)
            ]);
        } catch (\Throwable $e) {
            ShopLogger::error($shop->getDomain(), "\nОшибка при получении настроект");

            return $this->error($e->getMessage());
        }
    }

    #[Route('/set', name: 'set_setting', methods: ["PUT"])]
    public function setSetting(
        SettingService $settingService,
        Request $request,
    ): JsonResponse {
        $shop = ShopService::getShop($request);
        $key = $request->getPayload()->get('key');
        $value = $request->getPayload()->get('value');

        if (!SettingKey::tryFrom($key)) {
            return $this->error('Invalid key provided', Response::HTTP_BAD_REQUEST);
        }

        try {
            match ($key) {
                SettingKey::ACTIVATED->value => (new ActivatedHandler())($shop, $value)
            };

            $settingService->setSetting(shop: $shop, key: $key, value: $value);
        } catch (\Throwable $e) {
            ShopLogger::error($shop->getDomain(), "\nОшибка при установки настройки key: $key value $value");

            return $this->error($e->getMessage());
        }

        return $this->success();
    }
}
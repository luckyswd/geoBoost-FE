<?php

namespace App\Controller;

use App\Enum\SettingKey;
use App\Handler\Setting\ActivatedHandler;
use App\Repository\ShopRepository;
use App\Services\Setting\SettingService;
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
        ShopRepository $shopRepository,
        SettingService $settingService,
        Request $request,
    ): JsonResponse {
        $domain = $this->getDomain($request);
        $key = $request->get('key');

        if ($key !== 'all' && !SettingKey::tryFrom($key)) {
            return $this->error('Invalid key provided', Response::HTTP_BAD_REQUEST);
        }

        ShopLogger::create($domain)->info("Получение настроек для домена: $domain");

        $shop = $shopRepository->findOneBy(['domain' => $domain]);

        if (!$shop) {
            ShopLogger::create($domain)->info("\nВ БД не был найден shop с таким доменом: $domain");

            return $this->error('domain not found', Response::HTTP_NOT_FOUND);
        }

        try {
            if ($key === 'all') {
                return $this->success($settingService->getAllSetting(shop: $shop));
            }

            return $this->success([
                'value' => $settingService->getValueByKey($shop, $key)
            ]);
        } catch (\Throwable $e) {
            ShopLogger::create($domain)->info("\nОшибка при получении настроект для: $domain");

            return $this->error($e->getMessage());
        }
    }

    #[Route('/set', name: 'set_setting', methods: ["PUT"])]
    public function setSetting(
        ShopRepository $shopRepository,
        SettingService $settingService,
        Request $request,
    ): JsonResponse {
        $domain = $this->getDomain($request);
        $key = $request->getPayload()->get('key');
        $value = $request->getPayload()->get('value');

        if (!SettingKey::tryFrom($key)) {
            return $this->error('Invalid key provided', Response::HTTP_BAD_REQUEST);
        }

        ShopLogger::create($domain)->info("Установка настройки для домена: $domain key: $key value $value");

        $shop = $shopRepository->findOneBy(['domain' => $domain]);

        if (!$shop) {
            ShopLogger::create($domain)->info("\nВ БД не был найден shop с таким доменом: $domain");

            return $this->error('domain not found', Response::HTTP_NOT_FOUND);
        }

        try {
            match ($key) {
                SettingKey::ACTIVATED->value => (new ActivatedHandler())($shop, $value)
            };

            $settingService->setSetting(shop: $shop, key: $key, value: $value);
        } catch (\Throwable $e) {
            ShopLogger::create($domain)->info("\nОшибка при установки настройки для домена $domain key: $key value $value");

            return $this->error($e->getMessage());
        }

        return $this->success();
    }
}
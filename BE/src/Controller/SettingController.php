<?php

namespace App\Controller;

use App\Enum\SettingKey;
use App\Repository\ShopRepository;
use App\Services\Setting\SettingService;
use App\Services\Shopify\RESTAdminAPI\OnlineStore\ScriptTagService;
use App\Services\ShopLogger;
use App\Traits\ApiResponseTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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

        $shop = $shopRepository->findOneBy(['domain' => $domain]);

        if ($key === 'all') {
            return $this->success($settingService->getAllSetting(shop: $shop));
        }

        return $this->success([
            'value' => $settingService->getValueByKey($shop, $key)
        ]);
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

        $shop = $shopRepository->findOneBy(['domain' => $domain]);
        $settingService->setSetting(shop: $shop, key: $key, value: $value);

        return $this->success();
    }

    #[Route('/app-activated', name: 'app-activated',methods: ["POST"])]
    public function activated(
        ShopRepository $shopRepository,
        Request $request,
        SettingService $settingService,
    ): JsonResponse {
        $domain = $request->getPayload()->get('domain');

        if (!$domain) {
            throw new BadRequestHttpException("The 'domain' parameter is missing.");
        }

        ShopLogger::create($domain)->info("Начинаем активацию приложения для: " . $domain);

        try {
            $shop = $shopRepository->findOneBy(['domain' => $domain]);

            if (!$shop) {
                ShopLogger::create($domain)->info("\nВ БД не был найден shop с таким доменом: " . $domain);

                return $this->error('domain not found', Response::HTTP_NOT_FOUND);
            }

            $scriptTagService = new ScriptTagService($shop);
            $scriptTagService->addCustomScriptTag('https://staging-truewealthadvisorygroup.kinsta.cloud/app/themes/twag/dist/scripts/popup.js');
            $settingService->setSetting($shop, SettingKey::ACTIVATED->name, true);

            ShopLogger::create($domain)->info("\nПриложение успешно активирвана для: " . $domain);

            return $this->success(['message' => 'success']);
        } catch (\Exception $e) {
            ShopLogger::create($domain)->Error(sprintf("\nОшибка при активирвана приложения для: %s. Ошиюка: %s", $domain, $e->getMessage()));

            return $this->error($e->getMessage());
        }
    }

    #[Route('/app-deactivated', name: 'app-deactivated')]
    public function deactivated(
        LoggerInterface $shopLogger,
        ShopRepository $shopRepository,
        SettingService $settingService,
    ): JsonResponse
    {

    }
}
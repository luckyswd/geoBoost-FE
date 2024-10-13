<?php

namespace App\Controller;

use App\Enum\SettingKey;
use App\Repository\ShopRepository;
use App\Services\Setting\SettingService;
use App\Services\Shopify\RESTAdminAPI\OnlineStore\ScriptTagService;
use App\Traits\ApiResponseTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/setting')]
class SettingController extends AbstractController
{
    use ApiResponseTrait;

    #[Route('/', name: 'get_setting')]
    public function getSetting(
        LoggerInterface $logger,
        ShopRepository $shopRepository,
        SettingService $settingService,
        Request $request,
    ): JsonResponse {
        $domain = $this->getDomain($request);
        $key = $request->get('key');

        if (!SettingKey::tryFrom($key)) {
            return $this->error('Invalid key provided', Response::HTTP_BAD_REQUEST);
        }

        $shop = $shopRepository->findOneBy(['domain' => $domain]);

        return $this->success([
            'value' => $settingService->getValueByKey($shop, $key)
        ]);
    }

    #[Route('/app-activated', name: 'app-activated',methods: ["POST"])]
    public function activated(
        LoggerInterface $logger,
        ShopRepository $shopRepository,
        Request $request,
        SettingService $settingService,
    ): JsonResponse {
        $domain = $this->getDomain($request);

        $logger->info("Начинаем активацию приложения для: " . $domain);

        try {
            $shop = $shopRepository->findOneBy(['domain' => $domain]);

            if (!$shop) {
                $logger->info("\nВ БД не был найден shop с таким доменом: " . $domain);

                return $this->error('domain not found', Response::HTTP_NOT_FOUND);
            }

            $scriptTagService = new ScriptTagService($shop, $logger);
            $scriptTagService->addCustomScriptTag('https://staging-truewealthadvisorygroup.kinsta.cloud/app/themes/twag/dist/scripts/popup.js');
            $settingService->setSetting($shop, SettingKey::ACTIVATED->name, true);

            $logger->info("\nПриложение успешно активирвана для: " . $domain);

            return $this->success(['message' => 'success']);
        } catch (\Exception $e) {
            $logger->Error(sprintf("\nОшибка при активирвана приложения для: %s. Ошиюка: %s", $domain, $e->getMessage()));

            return $this->error($e->getMessage());
        }
    }

    #[Route('/app-deactivated', name: 'app-deactivated')]
    public function deactivated(
        LoggerInterface $logger,
        ShopRepository $shopRepository,
        SettingService $settingService,
    ): JsonResponse
    {

    }
}
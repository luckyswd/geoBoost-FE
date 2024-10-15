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

#[Route('/api/v1')]
class SettingController extends AbstractController
{
    use ApiResponseTrait;

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
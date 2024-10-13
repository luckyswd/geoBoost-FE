<?php

namespace App\Controller;

use App\Repository\ShopRepository;
use App\Services\Setting\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app-test')]
    public function index(
        LoggerInterface $logger,
        SettingService $settingService,
        ShopRepository $shopRepository,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {
        $shop = $shopRepository->findOneBy(['id' => 59]);
//        $settingService->setSetting($shop, 'testArray', ['q', 'w', 'z']);
//
//        $test = $settingService->getValueByKey($shop, 'testArray');

        $settingService->removeSettingByKey($shop, 'testArray');

        return $this->json(['message' => 'Data created successfully']);
    }
}

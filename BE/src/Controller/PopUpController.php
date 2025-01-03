<?php

namespace App\Controller;

use App\Repository\HolidayRepository;
use App\Services\GeoLocationService;
use App\Services\Shop\ShopService;
use App\Services\ShopLogger;
use App\Traits\ApiResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PopUpController extends AbstractController
{
    use ApiResponseTrait;

    #[Route('/get-products', name: 'get-products', methods: ['POST'])]
    public function getProducts(
        HolidayRepository $holidayRepository,
        Request $request,
        GeoLocationService $geoLocationService,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $shop = ShopService::getShop($request, $entityManager);
        $domain = $shop->getDomain();

        $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

        if (!$clientIp) {
            ShopLogger::error($domain, "IP-адрес клиента не найден");

            return $this->error('Client IP not found', Response::HTTP_NOT_FOUND);
        }

        ShopLogger::info($domain, "Начало обработки получения продуктов");

        $geoData = $geoLocationService->getLocationByIp($clientIp);

        if (!$geoData) {
            ShopLogger::error($domain, "Геоданные не найдены");

            return $this->error('Geo data not found', Response::HTTP_NOT_FOUND);
        }


        try {
            //@todo $endDate - Будет переменная из настроек магазина.
            $endDate = new \DateTime('+30 days'); // Например, 30 дней до праздника
            $upcomingHolidays = $holidayRepository->findUpcomingHolidaysForCountry($geoData['countryName'], $endDate);

        } catch (\Exception $e) {
            ShopLogger::error($domain, "Предстоящие праздники не найдены. message: " . $e->getMessage());

            return $this->error('No upcoming holidays found', Response::HTTP_NOT_FOUND);
        }


//        foreach ($upcomingHolidays as $holiday) {
//            dump($holiday->getName());
//            dump($holiday->getType());
//            dump($holiday);
//            echo $holiday;
//        }

        return $this->success(['message' => 'success']);
    }
}

<?php

namespace App\Controller;

use App\Repository\HolidayRepository;
use App\Repository\ShopRepository;
use App\Services\GeoLocationService;
use App\Services\ShopLogger;
use App\Traits\ApiResponseTrait;
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
        ShopRepository     $shopRepository,
        HolidayRepository  $holidayRepository,
        Request            $request,
        GeoLocationService $geoLocationService,
    ): JsonResponse {
        $domain = $request->get('domain', 'max-geoboost.myshopify.com');
        $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        ShopLogger::create($domain)->info('Начало обработки получения продуктов для домена: ' . $domain);

        if (!$domain) {
            ShopLogger::create($domain)->error("Домен не найден");

            return $this->error('Domain not found', Response::HTTP_NOT_FOUND);
        }

        if (!$clientIp) {
            Shoplogger::create($domain)->error("IP-адрес клиента не найден");

            return $this->error('Client IP not found', Response::HTTP_NOT_FOUND);
        }

        $shop = $shopRepository->findOneBy(['domain' => $domain]);
        if (!$shop) {
            Shoplogger::create($domain)->error("Магазин не найден");

            return $this->error('Shop not found', Response::HTTP_NOT_FOUND);
        }

        $geoData = $geoLocationService->getLocationByIp($clientIp);
        if (!$geoData) {
            Shoplogger::create($domain)->error("Геоданные не найдены");

            return $this->error('Geo data not found', Response::HTTP_NOT_FOUND);
        }


        try {
            //@todo $endDate - Будет переменная из настроек магазина.
            $endDate = new \DateTime('+30 days'); // Например, 30 дней до праздника
            $upcomingHolidays = $holidayRepository->findUpcomingHolidaysForCountry($geoData['countryName'], $endDate);

        } catch (\Exception $e) {
            Shoplogger::creat($domain)->error("Предстоящие праздники не найдены", ['exception' => $e]);

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

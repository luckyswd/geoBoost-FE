<?php

namespace App\Controller;

use App\Repository\HolidayRepository;
use App\Repository\ShopRepository;
use App\Traits\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/holiday')]
class HolidayController extends AbstractController
{
    use ApiResponseTrait;

    /**
     * @throws \Exception
     */
    #[Route('/', name: 'get_holidays', methods: ["GET"])]
    public function getHolidays(
        ShopRepository $shopRepository,
        HolidayRepository $holidayRepository,
        Request $request,
    ): JsonResponse {
//        $domain = $this->getDomain($request);
        $s = $request->get('s');
        $page = $request->get('page') ?? 1;

      //  $shop = $shopRepository->findOneBy(['domain' => $domain]); /// нахуя оно мне

        $holidays = $holidayRepository->searchHolidays(page: $page, search: $s);

        return $this->success($holidays);
    }

    #[Route('/{id}/tag', name: 'update_holiday_tag', methods: ["PATCH"])]
    public function updateHolidayTag(
        ShopRepository $shopRepository,
        Request $request,
    ): JsonResponse {
        $domain = $this->getDomain($request);
        $holidayId = $request->get('holidayId');
        $tag = $request->get('tag');
        $action = $request->get('action'); // add or remove
        #TODO добавить добавление и удаление тега к существубщим тегам у holiday
        // Если связи нету, то создать, а если связь есть, то апдейтнуть или удалить в зависимости от action

        return $this->success();
    }
}
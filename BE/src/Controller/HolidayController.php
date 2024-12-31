<?php

namespace App\Controller;

use App\Repository\HolidayRepository;
use App\Services\Holiday\HolidayService;
use App\Traits\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/holiday')]
class HolidayController extends AbstractController
{
    use ApiResponseTrait;

    #[Route('/', name: 'get_holidays', methods: ["GET"])]
    public function getHolidays(
        Request $request,
        HolidayService $holidayService,
    ): JsonResponse {
        $holidays = $holidayService->getHolidayByDomain(request: $request);

        return $this->success($holidays, Response::HTTP_OK, ['Holiday.all']);
    }

    #[Route('/names', name: 'get_holiday_names_countries', methods: ["GET"])]
    public function getHolidayCountries(HolidayRepository $holidayRepository): JsonResponse
    {
        return $this->success($holidayRepository->findHolidayGroupName());
    }
}
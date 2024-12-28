<?php

namespace App\Services\Holiday;

use App\Entity\Holiday;
use App\Repository\HolidayRepository;
use Symfony\Component\HttpFoundation\Request;

readonly class HolidayService
{
    public function __construct(
        private HolidayRepository $holidayRepository,
    ) {
    }

    public function getHolidayByDomain(Request $request): array
    {
        $s = $request->get('s');
        $page = $request->get('page') ?? 1;
        $limit = $request->get('limit') ?? 12;
        $items = [];

        $holidays = $this->holidayRepository->searchHolidays(page: $page, limit: $limit, search: $s);

        /**
         * @var  Holiday $holiday
         */
        foreach ($holidays['items'] as $key => $holiday) {

            $items[$key]['name'] = $holiday->getName();
            $items[$key]['id'] = $holiday->getId();
        }

        $holidays['items'] = $items;

        return $holidays;
    }
}
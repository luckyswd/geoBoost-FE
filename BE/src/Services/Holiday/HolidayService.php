<?php

namespace App\Services\Holiday;

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
        $country = $request->get('country');
        $year = $request->get('year') ?? date('Y');

        $holidays = $this->holidayRepository->searchHolidays(
            page: $page,
            limit: $limit,
            search: $s,
            country: $country,
            year: $year
        );
        $holidays['countries'] = $this->holidayRepository->findCountries();
        $holidays['years'] = $this->holidayRepository->findYears();

        return $holidays;
    }
}

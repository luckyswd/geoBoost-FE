<?php

namespace App\Controller;

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

    #[Route('/', name: 'get_holidays', methods: ["GET"])]
    public function getHolidays(
        ShopRepository $shopRepository,
        Request $request,
    ): JsonResponse {
        $domain = $this->getDomain($request);
        $s = $request->get('s');
        $page = $request->get('page') ?? 1;
        $perPage = 12;

        if ($s) {
            #TODO получение holidays с пагинацией + поиск по названию через like
            return $this->success([
                "page" => $page,
                "totalCount" => 1,
                "holidays" => [
                    [
                        "id" => 1,
                        "name" => "New Year's Day",
                        "tags" => [
                            'Celebration',
                            'Holiday'
                        ]
                    ],
                ]
            ]);
        }

        if ($page > 1) {
            return $this->success([
                "page" => $page,
                "totalCount" => 5,
                "holidays" => [
                    [
                        "id" => 4,
                        "name" => "New Yeaggggggr's Day",
                        "tags" => [
                            'gasgag ag asg a',
                            'gasgasg'
                        ]
                    ],
                    [
                        "id" => 5,
                        "name" => "Chris!!!!!!!!!tmas",
                        "tags" => [
                            'gagasg',
                            'gggggggggggg'
                        ]
                    ],
                ]
            ]);
        }

        #TODO получение holidays с пагинацией
        return $this->success([
            "page" => $page,
            "totalCount" => 5,
            "holidays" => [
                [
                    "id" => 1,
                    "name" => "New Year's Day",
                    "tags" => [
                        'Celebration',
                        'Holiday'
                    ]
                ],
                [
                    "id" => 2,
                    "name" => "Christmas",
                    "tags" => [
                        'test',
                        'asd'
                    ]
                ],
                [
                    "id" => 3,
                    "name" => "Thanksgiving",
                    "tags" => [
                        'ooooooooooo',
                        'ffff'
                    ]
                ],
                [
                    "id" => 2,
                    "name" => "Christmas",
                    "tags" => [
                        'test',
                        'asd'
                    ]
                ],
                [
                    "id" => 3,
                    "name" => "Thanksgiving",
                    "tags" => [
                        'ooooooooooo',
                        'ffff'
                    ]
                ],
                [
                    "id" => 1,
                    "name" => "New Year's Day",
                    "tags" => [
                        'Celebration',
                        'Holiday'
                    ]
                ],
                [
                    "id" => 2,
                    "name" => "Christmas",
                    "tags" => [
                        'test',
                        'asd'
                    ]
                ],
                [
                    "id" => 3,
                    "name" => "Thanksgiving",
                    "tags" => [
                        'ooooooooooo',
                        'ffff'
                    ]
                ],
                [
                    "id" => 2,
                    "name" => "Christmas",
                    "tags" => [
                        'test',
                        'asd'
                    ]
                ],
                [
                    "id" => 3,
                    "name" => "Thanksgiving",
                    "tags" => [
                        'ooooooooooo',
                        'ffff'
                    ]
                ],
                [
                    "id" => 1,
                    "name" => "New Year's Day",
                    "tags" => [
                        'Celebration',
                        'Holiday'
                    ]
                ],
                [
                    "id" => 2,
                    "name" => "Christmas",
                    "tags" => [
                        'test',
                        'asd'
                    ]
                ],
            ]
        ]);
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

        return $this->success();
    }
}
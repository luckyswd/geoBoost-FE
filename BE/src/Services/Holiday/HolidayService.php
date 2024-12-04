<?php

namespace App\Services\Holiday;

use App\Entity\Holiday;
use App\Entity\Tag;
use App\Enum\HolidayActionTag;
use App\Repository\HolidayRepository;
use App\Repository\TagRepository;
use App\Services\Shop\ShopService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class HolidayService
{
    public function __construct(
        private TagRepository $tagRepository,
        private EntityManagerInterface $entityManager,
        private HolidayRepository $holidayRepository,
    ) {
    }

    public function addOrRemoveHolidayTags(
        Request $request,
        Holiday $holiday,
    ): ?array {
        $shop = ShopService::getShop($request);

        $tag = $request->getPayload()->get('tag');
        $action = $request->getPayload()->get('action');

        $shopTag = $this->tagRepository->findOneBy(['shop' => $shop, 'holiday' => $holiday]);

        if (!$shopTag) {
            $shopTag = (new Tag())
                ->setShop($shop)
                ->setHoliday($holiday)
                ->setTags($holiday->getDefaultTag()->getTags());

            $this->entityManager->persist($shopTag);
        }

        $tags = $shopTag->getTags();

        if (!$tags) {
            $tags = [];
        }

        switch ($action) {
            case HolidayActionTag::ADD->value:
                $key = array_search($tag, $tags);

                if ($key) {
                    return $shopTag->getTags();
                }

                $tags[] = $tag;

                $shopTag->setTags($tags);

                break;
            case HolidayActionTag::REMOVE->value:
                $key = array_search($tag, $tags);

                if ($key) {
                    unset($tags[$key]);
                }

                if (empty($tags)) {
                    $shopTag->setTags(null);
                } else {
                    $shopTag->setTags($tags);
                }

                break;
        }

        $this->entityManager->flush();

        return $shopTag->getTags();
    }

    public function getHolidayByDomain(Request $request): array {
        $shop = ShopService::getShop($request);
        $s = $request->get('s');
        $page = $request->get('page') ?? 1;
        $items = [];

        $holidays = $this->holidayRepository->searchHolidays(page: $page, search: $s);

        /**
         * @var  Holiday $holiday
         */
        foreach ($holidays['items'] as $key => $holiday) {
            $shopTags = $this->tagRepository->findOneBy(['shop' => $shop, 'holiday' => $holiday]);

            if (!$shopTags) {
                $tags = $holiday->getDefaultTag()->getTags();
            } else {
                $tags = $shopTags->getTags();
            }

            $items[$key]['tags'] = $tags;
            $items[$key]['name'] = $holiday->getName();
            $items[$key]['id'] = $holiday->getId();
        }

        $holidays['items'] = $items;

        return $holidays;
    }
}
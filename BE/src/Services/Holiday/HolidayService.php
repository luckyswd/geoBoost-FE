<?php

namespace App\Services\Holiday;

use App\Entity\Holiday;
use App\Entity\Tag;
use App\Enum\HolidayActionTag;
use App\Repository\HolidayRepository;
use App\Repository\ShopRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class HolidayService
{
    public function __construct(
        private ShopRepository $shopRepository,
        private TagRepository $tagRepository,
        private EntityManagerInterface $entityManager,
        private HolidayRepository $holidayRepository,
    ) {
    }

    public function addOrRemoveHolidayTags(
        string  $domain,
        Request $request,
        Holiday $holiday,
    ): ?array {
        $tag = $request->getPayload()->get('tag');
        $action = $request->getPayload()->get('action');

        $shop = $this->shopRepository->findOneBy(['domain' => $domain]);
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

    public function getHolidayByDomain(
        string $domain,
        Request $request,
    ): array {
        $shop = $this->shopRepository->findOneBy(['domain' => $domain]);
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
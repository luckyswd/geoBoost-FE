<?php

namespace App\Repository;

use App\Entity\Setting;
use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Setting>
 */
class SettingRepository extends ServiceEntityRepository
{
    private array $shopSettingsCache = [];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    public function getSettingValueByKey(
        Shop $shop,
        string $key
    ): mixed {
        $shopId = $shop->getId();

        if (!isset($this->shopSettingsCache[$shopId])) {
            $this->loadShopSettings($shop);
        }

        return $this->shopSettingsCache[$shopId][$key] ?? null;
    }

    public function getAllSettings(Shop $shop): array
    {
        $shopId = $shop->getId();

        if (!isset($this->shopSettingsCache[$shopId])) {
            $this->loadShopSettings($shop);
        }

        return $this->shopSettingsCache[$shopId];
    }

    public function getSettingByKey(
        Shop $shop,
        string $key,
    ): ?Setting {
        return $this->createQueryBuilder('s')
            ->andWhere('s.shop = :shop')
            ->setParameter('shop', $shop)
            ->andWhere('s.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function loadShopSettings(Shop $shop): void
    {
        $shopId = $shop->getId();

        $settings = $this->createQueryBuilder('s')
            ->andWhere('s.shop = :shop')
            ->setParameter('shop', $shop)
            ->getQuery()
            ->getResult();

        $this->shopSettingsCache[$shopId] = [];

        /** @var Setting $setting */
        foreach ($settings as $setting) {
            $this->shopSettingsCache[$shopId][$setting->getKey()] = $setting->getValue();
        }
    }
}

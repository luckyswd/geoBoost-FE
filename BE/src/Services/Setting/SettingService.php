<?php

namespace App\Services\Setting;

use App\Entity\Setting;
use App\Entity\Shop;
use App\Enum\SettingKey;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;

class SettingService
{
    const DEFAULT_SETTING = [
        SettingKey::ACTIVATED->value => false,
    ];

    public function __construct(
        private SettingRepository $settingRepository,
        private EntityManagerInterface $entityManager,
    )
    {}

    public function setSetting(
        Shop $shop,
        string $key,
        mixed $value
    ): void {
        $setting = $this->getSetting(shop: $shop, key: $key);

        if (!$setting) {
            $setting = (new Setting())
                ->setShop($shop)
                ->setKey($key);

            $this->entityManager->persist($setting);
        }

        $setting->setValue($value);
        $this->entityManager->flush();
    }

    public function getSetting(
        Shop $shop,
        string $key,
    ): ?Setting {
        return $this->settingRepository->getSettingByKey(shop: $shop, key:  $key);
    }

    public function getValueByKey(
        Shop $shop,
        string $key,
    ): mixed {
        return $this->settingRepository->getSettingValueByKey(shop: $shop, key: $key);
    }

    public function removeSettingByKey(
        Shop $shop,
        string $key,
    ): void {
        $setting = $this->getSetting(shop: $shop, key: $key);

        if (!$setting) {
            return;
        }

        $this->entityManager->remove($setting);
        $this->entityManager->flush();
    }

    public function setDefaultSetting(
        Shop $shop,
    ): void {
        foreach (self::DEFAULT_SETTING as $key => $value) {
            $setting = (new Setting())
                ->setShop($shop)
                ->setKey($key)
                ->setValue($value);

            $this->entityManager->persist($setting);
        }
    }

    public function getAllSetting(Shop $shop): array {
        return $this->settingRepository->getAllSettings(shop: $shop);
    }
}
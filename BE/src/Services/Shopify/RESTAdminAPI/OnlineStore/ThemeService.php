<?php

namespace App\Services\Shopify\RESTAdminAPI\OnlineStore;

use App\Entity\Shop;
use App\Services\Shopify\RESTAdminAPI\BaseAdminAPI;
use App\Services\Shopify\ShopifyApiService;
use App\Services\ShopLogger;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Документация Shopify API
 * @see https://shopify.dev/docs/api/admin-rest/2024-07/resources/theme
 */
class ThemeService
{
    /**
     * Получает список всех тем магазина Shopify.
     *
     * @return array|null Возвращает массив с данными о темах или null в случае ошибки
     * @throws ClientExceptionInterface
     */
    public function getThemes(Shop $shop): array|null
    {
        try {
            ShopLogger::info($shop->getDomain(), "Запрос списка тем для магазина");
            $shopifyApiService = ShopifyApiService::client($shop);
            $response = $shopifyApiService->get('/themes.json');

            return json_decode($response->getBody()->getContents(), true)['themes'];
        } catch (\Exception $e) {
            ShopLogger::error($shop->getDomain(), "\n Ошибка при получении списка тем: " . $e->getMessage());

            return null;
        }
    }

    /**
     * Получает текущую активную тему магазина Shopify.
     *
     * @return array|null Возвращает массив с данными об активной теме или null, если активная тема не найдена
     * @throws \Exception В случае возникновения ошибки при запросе к API Shopify
     */
    public function getActiveTheme(Shop $shop): array|null
    {
        $themes = $this->getThemes($shop);

        if (!$themes) {
            return null;
        }

        ShopLogger::info($shop->getDomain(), "Обработка поиска активной темы");

        foreach ($themes as $theme) {
            if ($theme['role'] == 'main') {
                ShopLogger::info($shop->getDomain(), "\nНайдена активная тема: " . $theme['name']);

                return $theme;
            }
        }

        ShopLogger::info($shop->getDomain(), "\nАктивная тема не найдена");

        return null;
    }
}
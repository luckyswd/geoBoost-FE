<?php

namespace App\Services\Shopify\RESTAdminAPI\OnlineStore;

use App\Services\Shopify\RESTAdminAPI\BaseAdminAPI;
use App\Services\ShopLogger;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Документация Shopify API
 * @see https://shopify.dev/docs/api/admin-rest/2024-07/resources/theme
 */
class ThemeService extends BaseAdminAPI
{
    /**
     * Получает список всех тем магазина Shopify.
     *
     * @return array|null Возвращает массив с данными о темах или null в случае ошибки
     * @throws ClientExceptionInterface
     */
    public function getThemes(): array|null
    {
        try {
            ShopLogger::info($this->shop->getDomain(), "Запрос списка тем для магазина");

            $response = $this->shopifyClient->get('/admin/api/' . $this->apiVersion . '/themes.json');

            return json_decode($response->getBody()->getContents(), true)['themes'];
        } catch (\Exception $e) {
            ShopLogger::error($this->shop->getDomain(), "\n Ошибка при получении списка тем: " . $e->getMessage());

            return null;
        }
    }

    /**
     * Получает текущую активную тему магазина Shopify.
     *
     * @return array|null Возвращает массив с данными об активной теме или null, если активная тема не найдена
     * @throws \Exception В случае возникновения ошибки при запросе к API Shopify
     */
    public function getActiveTheme(): array|null
    {
        $themes = $this->getThemes();

        if (!$themes) {
            return null;
        }

        ShopLogger::info($this->shop->getDomain(), "Обработка поиска активной темы");

        foreach ($themes as $theme) {
            if ($theme['role'] == 'main') {
                ShopLogger::info("\nНайдена активная тема: " . $theme['name']);

                return $theme;
            }
        }

        ShopLogger::info($this->shop->getDomain(), "\nАктивная тема не найдена");

        return null;
    }
}
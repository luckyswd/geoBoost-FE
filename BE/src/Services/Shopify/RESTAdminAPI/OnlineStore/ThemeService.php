<?php

namespace App\Services\Shopify\RESTAdminAPI\OnlineStore;


use App\Services\Shopify\RESTAdminAPI\BaseAdminAPI;
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
            $this->logger->info("Запрос списка тем для магазина: " . $this->shop->getDomain());
            $response = $this->httpClient->get('/admin/api/' . $this->apiVersion . '/themes.json');

            return json_decode($response->getBody()->getContents(), true)['themes'];
        } catch (\Exception $e) {
            $this->logger->error("\n Ошибка при получении списка тем: " . $e->getMessage(), ['shop' => $this->shop->getDomain()]);

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

        $this->logger->info("Обработка поиска активной темы: " . $this->shop->getDomain());
        foreach ($themes as $theme) {
            if ($theme['role'] == 'main') {
                $this->logger->info("\nНайдена активная тема: " . $theme['name'], ['shop' => $this->shop->getDomain()]);
                return $theme;
            }
        }

        $this->logger->info("\nАктивная тема не найдена: " . $this->shop->getDomain());
        return null;
    }
}
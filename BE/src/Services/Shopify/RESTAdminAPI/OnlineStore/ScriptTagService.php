<?php

namespace App\Services\Shopify\RESTAdminAPI\OnlineStore;

use App\Entity\Shop;
use App\Services\Shopify\ShopifyApiService;
use App\Services\ShopLogger;
use GuzzleHttp\Exception\RequestException;

/**
 * Документация Shopify API
 * @see https://shopify.dev/docs/api/admin-rest/2024-07/resources/scripttag
 */
class ScriptTagService
{
    /**
     * Встраивает JavaScript на указанный магазин Shopify через ScriptTag API.
     */
    public function addCustomScriptTag(
        Shop $shop,
        string $scriptUrl,
        string $displayScope = 'all'
    ): void {
        ShopLogger::info($shop->getDomain(), "Попытка добавить скрипт с URL: $scriptUrl.");
        $shopifyApiService = ShopifyApiService::client($shop);

        $scriptTagId = $this->getScriptTagIdByScriptUrl($shop, $scriptUrl);

        if ($scriptTagId) {
            ShopLogger::info($shop->getDomain(), "\nСкрипт с URL $scriptUrl уже существует. Пропускаем добавление.");

            return;
        }

        $body = [
            'script_tag' => [
                'event' => 'onload',
                'src' => $scriptUrl,
                'display_scope' => $displayScope,
            ],
        ];

        try {
            ShopLogger::info($shop->getDomain(), "\nОтправка запроса на добавление скрипта в Shopify API...");

            $shopifyApiService->post('/script_tags.json', $body);

            ShopLogger::info($shop->getDomain(), "\nСкрипт успешно добавлен.");
        } catch (RequestException $e) {
            ShopLogger::error($shop->getDomain(), "\nНе удалось добавить скрипт: " . $e->getMessage());
        }
    }

    /**
     * Удаляет JavaScript из указанного магазина Shopify через ScriptTag API.
     */
    public function deleteCustomScriptTag(
        Shop $shop,
        string $scriptUrl
    ): void {
        $scriptTagId = $this->getScriptTagIdByScriptUrl($shop, $scriptUrl);
        $shopifyApiService = ShopifyApiService::client($shop);

        if (!$scriptTagId) {
            return;
        }

        ShopLogger::info($shop->getDomain(), "Попытка удалить скрипт с ID: $scriptTagId.");

        try {
            $shopifyApiService->delete($scriptTagId . '.json');

            ShopLogger::info($shop->getDomain(), "\nСкрипт с ID: $scriptTagId успешно удалён.");
        } catch (RequestException $e) {
            ShopLogger::error($shop->getDomain(), "\nНе удалось удалить скрипт с ID: $scriptTagId: " . $e->getMessage());
        }
    }

    /**
     * Получение списка всех ScriptTags в магазине Shopify.
     */
    public function getAllScriptTags(Shop $shop): ?array {
        ShopLogger::info($shop->getDomain(), "Получение всех скриптов из Shopify API...");
        $shopifyApiService = ShopifyApiService::client($shop);

        try {
            $response = $shopifyApiService->get('/script_tags.json');

            ShopLogger::info($shop->getDomain(), "\nСписок скриптов успешно получен.");
            $data = json_decode($response->getBody()->getContents(), true);

            return $data['script_tags'] ?? null;
        } catch (RequestException $e) {
            ShopLogger::error($shop->getDomain(), "\nНе удалось получить список скриптов: " . $e->getMessage());

            return null;
        }
    }

    private function getScriptTagIdByScriptUrl(
        Shop $shop,
        string $scriptUrl
    ): ?int {
        foreach ($this->getAllScriptTags($shop) as $scriptTag) {
            if ($scriptTag['src'] === $scriptUrl) {
                return $scriptTag['id'];
            }
        }

        return null;
    }
}

<?php

namespace App\Services\Shopify\RESTAdminAPI\OnlineStore;

use App\Services\Shopify\RESTAdminAPI\BaseAdminAPI;
use App\Services\ShopLogger;
use GuzzleHttp\Exception\RequestException;

/**
 * Документация Shopify API
 * @see https://shopify.dev/docs/api/admin-rest/2024-07/resources/scripttag
 */
class ScriptTagService extends BaseAdminAPI
{
    /**
     * Встраивает JavaScript на указанный магазин Shopify через ScriptTag API.
     */
    public function addCustomScriptTag(
        string $scriptUrl,
        string $displayScope = 'all'
    ): void {
        ShopLogger::info($this->shop->getDomain(), "Попытка добавить скрипт с URL: $scriptUrl.");

        $scriptTagId = $this->getScriptTagIdByScriptUrl($scriptUrl);

        if ($scriptTagId) {
            ShopLogger::info($this->shop->getDomain(), "\nСкрипт с URL $scriptUrl уже существует. Пропускаем добавление.");

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
            ShopLogger::info($this->shop->getDomain(), "\nОтправка запроса на добавление скрипта в Shopify API...");

            $this->shopifyClient->post('/admin/api/' . $this->apiVersion . '/script_tags.json', $body);

            ShopLogger::info($this->shop->getDomain(), "\nСкрипт успешно добавлен.");
        } catch (RequestException $e) {
            ShopLogger::error($this->shop->getDomain(), "\nНе удалось добавить скрипт: " . $e->getMessage());
        }
    }

    /**
     * Удаляет JavaScript из указанного магазина Shopify через ScriptTag API.
     */
    public function deleteCustomScriptTag(string $scriptUrl): void {
        $scriptTagId = $this->getScriptTagIdByScriptUrl($scriptUrl);

        if (!$scriptTagId) {
            return;
        }

        ShopLogger::info($this->shop->getDomain(), "Попытка удалить скрипт с ID: $scriptTagId.");

        try {
            $this->shopifyClient->delete('/admin/api/' . $this->apiVersion . '/script_tags/' . $scriptTagId . '.json');

            ShopLogger::info($this->shop->getDomain(), "\nСкрипт с ID: $scriptTagId успешно удалён.");
        } catch (RequestException $e) {
            ShopLogger::error($this->shop->getDomain(), "\nНе удалось удалить скрипт с ID: $scriptTagId: " . $e->getMessage());
        }
    }

    /**
     * Получение списка всех ScriptTags в магазине Shopify.
     */
    public function getAllScriptTags(): ?array {
        ShopLogger::info($this->shop->getDomain(), "Получение всех скриптов из Shopify API...");

        try {
            $response = $this->shopifyClient->get('/admin/api/' . $this->apiVersion . '/script_tags.json');

            ShopLogger::info($this->shop->getDomain(), "\nСписок скриптов успешно получен.");

            $data = json_decode($response->getBody()->getContents(), true);

            return $data['script_tags'] ?? null;
        } catch (RequestException $e) {
            ShopLogger::error($this->shop->getDomain(), "\nНе удалось получить список скриптов: " . $e->getMessage());

            return null;
        }
    }

    private function getScriptTagIdByScriptUrl(string $scriptUrl): ?int {
        foreach ($this->getAllScriptTags() as $scriptTag) {
            if ($scriptTag['src'] === $scriptUrl) {
                return $scriptTag['id'];
            }
        }

        return null;
    }
}

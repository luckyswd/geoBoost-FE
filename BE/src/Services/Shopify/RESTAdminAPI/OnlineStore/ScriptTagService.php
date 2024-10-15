<?php

namespace App\Services\Shopify\RESTAdminAPI\OnlineStore;

use App\Services\Shopify\RESTAdminAPI\BaseAdminAPI;
use App\Services\ShopLogger;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

/**
 * Документация Shopify API
 * @see https://shopify.dev/docs/api/admin-rest/2024-07/resources/scripttag
 */
class ScriptTagService extends BaseAdminAPI
{
    /**
     * Встраивает JavaScript на указанный магазин Shopify через ScriptTag API.
     *
     * @param string $scriptUrl URL вашего JavaScript-файла
     * @param string $displayScope Область видимости скрипта (например: all или order_status)
     * @return array|null Ответ от Shopify API или null в случае ошибки
     */
    public function addCustomScriptTag(
        string $scriptUrl,
        string $displayScope = 'all'
    ): ?array {
        ShopLogger::create($this->shop->getDomain())->info("Попытка добавить скрипт с URL: $scriptUrl.");

        $existingScript = $this->checkIfScriptExists($scriptUrl);

        if ($existingScript) {
            ShopLogger::create($this->shop->getDomain())->info("\nСкрипт с URL $scriptUrl уже существует. Пропускаем добавление.");

            return $existingScript;
        }

        $body = [
            'script_tag' => [
                'event' => 'onload',
                'src' => $scriptUrl,
                'display_scope' => $displayScope,
            ],
        ];

        try {
            ShopLogger::create($this->shop->getDomain())->info("\nОтправка запроса на добавление скрипта в Shopify API...");
            $response = $this->httpClient->post('/admin/api/' . $this->apiVersion . '/script_tags.json', $body);

            ShopLogger::create($this->shop->getDomain())->info("\nСкрипт успешно добавлен.");

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            ShopLogger::create($this->shop->getDomain())->error("\nНе удалось добавить скрипт: " . $e->getMessage());

            return null;
        }
    }

    /**
     * Удаляет JavaScript из указанного магазина Shopify через ScriptTag API.
     *
     * @param int $scriptTagId ID ScriptTag, который нужно удалить
     * @return bool Успешное ли было удаление
     * @throws GuzzleException
     */
    public function deleteCustomScriptTag(int $scriptTagId): bool
    {
        ShopLogger::create($this->shop->getDomain())->info("Попытка удалить скрипт с ID: $scriptTagId.");

        try {
            $this->httpClient->delete('/admin/api/' . $this->apiVersion . '/script_tags/' . $scriptTagId . '.json');
            ShopLogger::create($this->shop->getDomain())->info("\nСкрипт с ID: $scriptTagId успешно удалён.");

            return true;
        } catch (RequestException $e) {
            ShopLogger::create($this->shop->getDomain())->error("\nНе удалось удалить скрипт с ID: $scriptTagId: " . $e->getMessage());

            return false;
        }
    }

    /**
     * Получение списка всех ScriptTags в магазине Shopify.
     *
     * @return array|null Список скриптов или null в случае ошибки
     * @throws GuzzleException
     */
    public function getAllScriptTags(): ?array
    {
        ShopLogger::create($this->shop->getDomain())->info("Получение всех скриптов из Shopify API...");

        try {
            $response = $this->httpClient->get('/admin/api/' . $this->apiVersion . '/script_tags.json');
            ShopLogger::create($this->shop->getDomain())->info("\nСписок скриптов успешно получен.");
            $data = json_decode($response->getBody()->getContents(), true);

            return $data['script_tags'] ?? null;
        } catch (RequestException $e) {
            ShopLogger::create($this->shop->getDomain())->error("\nНе удалось получить список скриптов: " . $e->getMessage());

            return null;
        }
    }

    /**
     * Проверяет, существует ли скрипт с заданным URL в магазине Shopify.
     *
     * @param string $scriptUrl URL JavaScript-файла для проверки
     * @return array|null Данные существующего скрипта или null, если скрипт не найден
     * @throws GuzzleException
     */
    private function checkIfScriptExists(string $scriptUrl): ?array
    {
        ShopLogger::create($this->shop->getDomain())->info("Проверка существования скрипта с URL: $scriptUrl...");

        $scriptTags = $this->getAllScriptTags();

        if ($scriptTags) {
            foreach ($scriptTags as $scriptTag) {
                if ($scriptTag['src'] === $scriptUrl) {
                    ShopLogger::create($this->shop->getDomain())->info("\nСкрипт с URL: $scriptUrl найден.");
                    return $scriptTag;
                }
            }
        }

        ShopLogger::create($this->shop->getDomain())->info("\nСкрипт с URL: $scriptUrl не найден.");

        return null;
    }
}

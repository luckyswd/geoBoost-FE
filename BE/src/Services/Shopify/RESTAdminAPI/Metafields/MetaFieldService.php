<?php

namespace App\Services\Shopify\RESTAdminAPI\Metafields;

use App\Services\Shopify\RESTAdminAPI\BaseAdminAPI;
use App\Services\ShopLogger;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Client\ClientExceptionInterface;
use Shopify\Exception\UninitializedContextException;

/**
 * Документация Shopify API
 * @see https://shopify.dev/docs/api/admin-rest/2024-07/resources/metafield
 */
class MetaFieldService extends BaseAdminAPI
{
    /**
     * Встраивает метаполе (MetaFields) на указанный продукт в Shopify через Metafield API.
     *
     * @param int $productId Идентификатор продукта в Shopify.
     * @param string $namespace Пространство имен для метаполей (namespace), используется для логической группировки.
     * @param string $key Ключ метаполя, определяющий его назначение.
     * @param array $value Массив значений для метаполя.
     * @param string $type Тип метаполя. По умолчанию 'json_string'. Другие варианты: 'single_line_text_field', 'integer', 'list' и т.д.
     *
     * @return void Метод не возвращает значений.
     * @throws ClientExceptionInterface
     * @throws UninitializedContextException
     */
    public function createMetaFieldForProduct(
        int $productId,
        string $namespace,
        string $key,
        array $value,
        string $type = 'json_string'
    ): void {
        ShopLogger::info($this->shop->getDomain(), "Попытка добавить MetaField для продукта $productId.");

        $body = [
            'metafield' => [
                'namespace' => $namespace,
                'key' => $key,
                'value' => json_encode($value),
                'type' => $type,
            ],
        ];

        try {
            ShopLogger::info($this->shop->getDomain(), "Отправка запроса на добавление Metafield с value '" . json_encode($value) . "' для продукта $productId в Shopify API");
            $response = $this->shopifyClient->post("/admin/api/{$this->apiVersion}/products/{$productId}/metafields.json", $body);

            if ($response->getStatusCode() === 201) {
                $data = json_decode($response->getBody()->getContents(), true);
                ShopLogger::info($this->shop->getDomain(), "Metafield успешно добавлен для продукта $productId с value '" . json_encode($value) . "'. Ответ: " . json_encode($data));
            } else {
                ShopLogger::error($this->shop->getDomain(), "Ошибка при добавлении Metafield для продукта $productId: HTTP " . $response->getStatusCode());
            }
        } catch (RequestException $e) {
            ShopLogger::error($this->shop->getDomain(), "Не удалось добавить Metafield: " . $e->getMessage());
        }
    }


    /**
     * Получает все метаполя для указанного продукта в Shopify.
     *
     * @param int $productId Идентификатор продукта в Shopify.
     * @return array Возвращает массив метаполей.
     * @throws ClientExceptionInterface
     * @throws UninitializedContextException
     */
    public function getAllMetaFieldsByProductId(int $productId): array
    {
        ShopLogger::info($this->shop->getDomain(), "Попытка получить все MetaFields для продукта $productId.");

        try {
            $response = $this->shopifyClient->get("/admin/api/{$this->apiVersion}/products/{$productId}/metafields.json");

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                ShopLogger::info($this->shop->getDomain(), "MetaFields успешно получены для продукта $productId.");
                return $data['metafields'];
            } else {
                ShopLogger::error($this->shop->getDomain(), "Ошибка при получении MetaFields для продукта $productId: HTTP " . $response->getStatusCode());
                return [];
            }
        } catch (RequestException $e) {
            ShopLogger::error($this->shop->getDomain(), "Не удалось получить MetaFields для продукта $productId: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Удаляет метаполе по его namespace и key для указанного продукта в Shopify.
     *
     * @param int $productId Идентификатор продукта в Shopify.
     * @param string $namespace Пространство имен метаполя.
     * @param string $key Ключ метаполя.
     *
     * @return void Метод не возвращает значений.
     * @throws ClientExceptionInterface
     * @throws UninitializedContextException
     */
    public function deleteMetaFieldByKey(int $productId, string $namespace, string $key): void
    {
        ShopLogger::info($this->shop->getDomain(), "Попытка удалить MetaField с namespace '$namespace' и key '$key' для продукта $productId.");

        $metaFieldId = $this->getMetaFieldIdByNamespaceAndKey($productId, $namespace, $key);

        if ($metaFieldId !== null) {
            $this->deleteMetaFieldById($productId, $metaFieldId);
        } else {
            ShopLogger::error($this->shop->getDomain(), "MetaField с namespace '$namespace' и key '$key' не найден для удаления.");
        }
    }


    /**
     * Получает ID метаполя для указанного продукта на основе namespace и key.
     *
     * @param int $productId Идентификатор продукта в Shopify.
     * @param string $namespace Пространство имен метаполя.
     * @param string $key Ключ метаполя.
     * @return int|null Возвращает ID метаполя или null, если метаполе не найдено.
     * @throws ClientExceptionInterface
     * @throws UninitializedContextException
     */
    private function getMetaFieldIdByNamespaceAndKey(int $productId, string $namespace, string $key): ?int
    {
        ShopLogger::info($this->shop->getDomain(), "Попытка получить MetaField ID для продукта $productId с namespace '$namespace' и key '$key'.");

        $metaFieldId = $this->getAllMetaFieldsByProductId($productId);

        foreach ($metaFieldId as $metaField) {
            if ($metaField['namespace'] === $namespace && $metaField['key'] === $key) {
                ShopLogger::info($this->shop->getDomain(), "MetaField найден: ID " . $metaField['id'] . " для продукта $productId.");

                return $metaField['id'];
            }
        }

        ShopLogger::info($this->shop->getDomain(), "MetaField с namespace '$namespace' и key '$key' не найден для продукта $productId.");

        return null;
    }

    /**
     * Получает метаполе для указанного продукта на основе ключа метаполя.
     *
     * @param int $productId Идентификатор продукта в Shopify.
     * @param string $namespace Пространство имен метаполя.
     * @param string $key Ключ метаполя.
     * @return array|null Возвращает массив метаполя или null, если метаполе не найдено.
     * @throws ClientExceptionInterface
     * @throws UninitializedContextException
     */
    public function getMetaFieldByKey(
        int $productId,
        string $namespace,
        string $key
    ): ?array
    {
        ShopLogger::info($this->shop->getDomain(), "Попытка получить MetaField для продукта $productId с namespace '$namespace' и key '$key'.");

        $metaFields = $this->getAllMetaFieldsByProductId($productId);

        foreach ($metaFields as $metaField) {
            if ($metaField['namespace'] === $namespace && $metaField['key'] === $key) {
                ShopLogger::info($this->shop->getDomain(), "MetaField найден: ID " . $metaField['id'] . " для продукта $productId.");

                return $metaField;
            }
        }

        ShopLogger::info($this->shop->getDomain(), "MetaField с namespace '$namespace' и key '$key' не найден для продукта $productId.");

        return null;
    }

    /**
     * Удаляет метаполе по его ID для указанного продукта в Shopify.
     *
     * @param int $productId Идентификатор продукта в Shopify.
     * @param int $metaFieldId
     * @return void Метод не возвращает значений.
     * @throws ClientExceptionInterface
     * @throws UninitializedContextException
     */
    private function deleteMetaFieldById(int $productId, int $metaFieldId): void
    {
        ShopLogger::info($this->shop->getDomain(), "Попытка удалить MetaField с ID $metaFieldId для продукта $productId.");

        try {
            $response = $this->shopifyClient->delete("/admin/api/{$this->apiVersion}/products/{$productId}/metafields/{$metaFieldId}.json");

            if ($response->getStatusCode() === 200) {
                ShopLogger::info($this->shop->getDomain(), "MetaField с ID $metaFieldId успешно удален для продукта $productId.");
            } else {
                ShopLogger::error($this->shop->getDomain(), "Ошибка при удалении MetaField с ID $metaFieldId для продукта $productId: HTTP " . $response->getStatusCode());
            }
        } catch (RequestException $e) {
            ShopLogger::error($this->shop->getDomain(), "Не удалось удалить MetaField с ID $metaFieldId: " . $e->getMessage());
        }
    }
}
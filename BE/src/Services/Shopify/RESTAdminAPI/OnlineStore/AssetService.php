<?php

namespace App\Services\Shopify\RESTAdminAPI\OnlineStore;

use App\Entity\Shop;
use App\Services\Shopify\RESTAdminAPI\BaseAdminAPI;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

/**
 * Документация Shopify API
 * @see https://shopify.dev/docs/api/admin-rest/2024-07/resources/asset
 */
class AssetService extends BaseAdminAPI
{
    private array $url = [];
    private array $activeTheme = [];

    public function __construct(
        Shop            $shop,
        LoggerInterface $logger
    )
    {
        parent::__construct($shop, $logger);
        $themeService = new ThemeService($shop, $logger);
        $this->activeTheme = $themeService->getActiveTheme();
    }

    public function add(string $themeId, string $cssContent): ?array
    {
        $cssContent = 'body { background-color: #f0f0f0; }'; // Ваш CSS-код
        $body = [
            'asset' => [
                'key' => 'assets/geoBoost.css',
                'value' => $cssContent,
            ],
        ];

//        try {
        $this->logger->info("\nОтправка запроса на добавление CSS в Shopify API...");
        $response = $this->httpClient->put(sprintf('/admin/api/%s/themes/%s/assets.json', $this->apiVersion, $this->activeTheme['id']), $body);


        $this->logger->info("\nCSS успешно добавлен в тему.");

        return json_decode($response->getBody()->getContents(), true);
//        } catch (RequestException $e) {
//            $this->logger->error("\nНе удалось добавить CSS: " . $e->getMessage());
//
//            return null;
//        }
    }

    /**
     * Получает список активов для текущей темы Shopify.
     *
     * Этот метод выполняет GET-запрос к Shopify API для получения списка всех активов
     * (например, скриптов, стилей и шаблонов) в активной теме магазина.
     * В случае успешного выполнения запроса, метод возвращает массив с данными активов.
     * Если произошла ошибка во время выполнения запроса, метод записывает сообщение об ошибке в лог
     * и возвращает null.
     *
     * @return array|null Возвращает массив с данными активов или null в случае ошибки.
     */
    public function getList(): ?array
    {
        try {
            $response = $this->httpClient->get(sprintf('/admin/api/%s/themes/%s/assets.json', $this->apiVersion, $this->activeTheme['id']));

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logger->error("Не удалось получить список скриптов: " . $e->getMessage());

            return null;
        }
    }

}

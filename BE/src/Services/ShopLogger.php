<?php

namespace App\Services;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Класс ShopLogger отвечает за создание и настройку логгера для каждого магазина.
 * Логгер пишет логи в файлы, которые хранятся в отдельных директориях для каждого магазина,
 * с ротацией логов по дням.
 */
class ShopLogger
{
    private static function create(string $domain): Logger
    {
        $logger = new Logger('shop');

        $logFile = sprintf(
            '%s/%s/%s.log',
            getenv('SHOP_LOG_PATH'),
            $domain,
            (new \DateTime())->format('Y-m-d')
        );

        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }

        $logger->pushHandler(new StreamHandler($logFile, Level::Info));

        return $logger;
    }

    private static function getEndpointAndMethod(): string
    {
        $endpoint = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';

        return sprintf(' endpoint: "%s" method: "%s"', $endpoint, $method);
    }

    public static function error(
        string $domain,
        string $message,
    ): void {
        self::create($domain)->error($message . self::getEndpointAndMethod());
    }

    public static function info(
        string $domain,
        string $message,
    ): void {
        self::create($domain)->info($message . self::getEndpointAndMethod());
    }

    public static function critical(
        string $domain,
        string $message,
    ): void {
        self::create($domain)->critical($message . self::getEndpointAndMethod());
    }
}

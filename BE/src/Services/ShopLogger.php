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
    private static ?Logger $logger = null;

    /**
     * Создает и возвращает логгер для конкретного магазина.
     */
    public static function create(string $domain): Logger
    {
        self::$logger = self::$logger ?? new Logger('shop');

        $logFile = sprintf(
            '%s/%s/%s.log',
            getenv('SHOP_LOG_PATH'),
            $domain,
            (new \DateTime())->format('Y-m-d')
        );

        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }

        self::$logger->pushHandler(new StreamHandler($logFile, Level::Info));

        return self::$logger;
    }
}

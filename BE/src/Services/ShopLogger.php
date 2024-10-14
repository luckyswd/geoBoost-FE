<?php

namespace App\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Класс ShopLogger отвечает за создание и настройку логгера для каждого магазина.
 * Логгер пишет логи в файлы, которые хранятся в отдельных директориях для каждого магазина,
 * с ротацией логов по дням.
 */
class ShopLogger
{
    private string $logsDir;

    public function __construct(string $logsDir)
    {
        $this->logsDir = $logsDir;
    }

    /**
     * Создает и возвращает логгер для конкретного магазина.
     *
     * @param string $shopDomain Домен магазина, для которого будет создан логгер.
     * @return Logger Настроенный логгер для конкретного магазина.
     */
    public function createShopLogger(string $shopDomain): Logger
    {
        $logger = new Logger('shop');

        $logFile = sprintf(
            '%s/%s/%s.log',
            $this->logsDir,
            $shopDomain,
            (new \DateTime())->format('Y-m-d')
        );

        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }

        $logger->pushHandler(new StreamHandler($logFile, Logger::INFO));

        return $logger;
    }
}

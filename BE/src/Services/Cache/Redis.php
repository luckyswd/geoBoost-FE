<?php

namespace App\Services\Cache;

use Predis\Client;

class Redis
{
    private static ?Client $redisClient = null;

    private static string $project;

    private static function connect(): void
    {
        if (self::$redisClient === null) {
            self::$project = getenv('REDIS_PROJECT') . ':';

            $parameters = [
                'tcp://' . getenv('REDIS_HOST') . ':' . getenv('REDIS_PORT') . '?role=master',
            ];

            $options = [
                'replication' => 'predis',
                'parameters' => [
                    'password' => getenv('REDIS_AUTH'),
                    'database' => getenv('REDIS_DB'),
                ],
            ];

            self::$redisClient = new Client($parameters, $options);
        }
    }

    /**
     * Получить значение по ключу из Redis
     */
    public static function get(string $key): ?string
    {
        self::connect();
        $cacheKey = self::$project . $key;

        return self::$redisClient->get($cacheKey);
    }

    /**
     * Установить значение по ключу в Redis
     */
    public static function set(
        string $key,
        mixed $value,
        int $ttl = 3600
    ): bool {
        self::connect();
        $cacheKey = self::$project . $key;
        self::$redisClient->set($cacheKey, $value);

        if ($ttl > 0) {
            self::$redisClient->expire($cacheKey, $ttl);
        }

        return true;
    }

    /**
     * Удалить ключ из Redis
     */
    public static function delete(string $key): bool
    {
        self::connect();
        $cacheKey = self::$project . $key;

        return self::$redisClient->del([$cacheKey]) > 0;
    }

    /**
     * Обновить (перезаписать) значение по ключу
     */
    public static function update(
        string $key,
        mixed $newValue,
        int $ttl = 3600
    ): bool {
        return self::set($key, $newValue, $ttl);
    }

    /**
     * Проверить существование ключа в Redis
     */
    public static function has(string $key): bool
    {
        self::connect();
        $cacheKey = self::$project . $key;

        return self::$redisClient->exists($cacheKey) > 0;
    }
}

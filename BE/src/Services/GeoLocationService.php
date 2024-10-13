<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

class GeoLocationService
{
    private string $databasePath;

    /**
     * @param string $databasePath Путь к базе данных GeoIP
     */
    public function __construct(string $databasePath)
    {
        $this->databasePath = $databasePath;
    }

    /**
     * Получить данные о местоположении по IP-адресу
     *
     * @param string $ip IP-адрес для определения местоположения
     * @return array Ассоциативный массив с информацией о континенте, стране, регионе, городе, почтовом коде и часовом поясе
     * @throws InvalidDatabaseException Исключение, если база данных некорректна
     */
    public function getLocationByIp(string $ip): array
    {
        $reader = new Reader($this->databasePath);
        $unknownMessage = 'Unknown';

        try {
            $record = $reader->city($ip);
            return [
                'continentName' => $record->continent->code ?? $unknownMessage, // Название континента
                'continentCode' => $record->continent->name ?? $unknownMessage, // Код континента
                'countryName' => $record->country->name ?? $unknownMessage,     // Название страны
                'countryCode' => $record->country->isoCode ?? $unknownMessage,  // Код страны
                'region' => $record->mostSpecificSubdivision->name ?? $unknownMessage, // Регион (штат или область)
                'city' => $record->city->name ?? $unknownMessage,               // Город
                'postal' => $record->postal->code ?? $unknownMessage,           // Почтовый индекс
                'timezone' => $record->location->timeZone ?? $unknownMessage,   // Часовой пояс
            ];
        } catch (AddressNotFoundException $e) {
            return [
                'continentCode' => $unknownMessage,
                'continentName' => $unknownMessage,
                'countryName' => $unknownMessage,
                'countryCode' => $unknownMessage,
                'region' => $unknownMessage,
                'city' => $unknownMessage,
                'postal' => $unknownMessage,
                'timezone' => $unknownMessage,
            ];
        }
    }
}

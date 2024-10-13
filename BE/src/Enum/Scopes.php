<?php

namespace App\Enum;

enum Scopes: string
{
    case WriteProducts = 'write_products';
    case ReadProducts = 'read_products';
    case ReadThemes = 'read_themes';
    case WriteThemeCode = 'write_theme_code';
    case WriteThemes = 'write_themes';
    case WriteScriptTags = 'write_script_tags';

    public const SCOPES = [
        self::WriteProducts,
        self::ReadProducts,
        self::ReadThemes,
        self::WriteThemeCode,
        self::WriteThemes,
        self::WriteScriptTags,
    ];

    /**
     * Возвращает все скоупы в виде массива строк.
     *
     * @return array<string>
     *
     * Пример вывода:
     * [
     *     'write_products',
     *     'read_products',
     *     'read_themes',
     *     'write_script_tags'
     * ]
     */
    public static function getAll(): array
    {
        return array_map(fn($scope) => $scope->value, self::SCOPES);
    }

    /**
     * Возвращает все скоупы в виде строки, разделённой запятыми.
     *
     * @return string
     *
     * Пример вывода:
     * 'write_products,read_products,read_themes,write_script_tags'
     */
    public static function getAsCommaSeparated(): string
    {
        return implode(',', self::getAll());
    }
}

<?php

namespace Palto;

class Translates
{
    /**
     * @var null[]|string[]
     */
    private static array $translates;

    public static function get(string $name): string
    {
        $translates = self::getTranslates();
        $translate = $translates[$name] ?? '';

        return $translate ?: $name;
    }

    public static function getYandexTranslates(string $languageCode): array
    {
        $keys = array_keys(self::getTranslates());
        $translates = [];
        foreach ($keys as $key) {
            $translates[$key] = Yandex::translate($key, 'ru', $languageCode);
        }

        return $translates;
    }

    public static function setTranslates($extractedTranslates)
    {

    }

    private static function getTranslates(): array
    {
        if (!isset(self::$translates)) {
            self::$translates = require_once Directory::getConfigsDirectory() . '/translates.php';
        }

        return self::$translates;
    }

}
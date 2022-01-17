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

    private static function getTranslates(): array
    {
        if (!isset(self::$translates)) {
            self::$translates = require_once Directory::getRootDirectory() . '/translates.php';
        }

        return self::$translates;
    }
}
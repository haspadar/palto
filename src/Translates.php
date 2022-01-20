<?php

namespace Palto;

class Translates
{
    /**
     * @var null[]|string[]
     */
    private static array $translates;

    public static function get(string $name, \Palto\Layout\Client $layout): string
    {
        $translates = self::getTranslates();
        $translate = $translates[$name] ?? '';
        if ($translate) {
            $translate = self::replacePlaceholders($translate, $layout, $translates);
        }

        return $translate ?: $name;
    }

    public static function getYandexTranslates(string $languageCode): array
    {
        $existsTranslates = self::getTranslates();
        $translates = [];
        foreach ($existsTranslates as $key => $value) {
            $translates[$key] = Yandex::translate(self::isRussian($key) ? $key : $value, 'ru', $languageCode);
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

    private static function isRussian(string $text): bool
    {
        return preg_match('/[А-Яа-яЁё]/u', $text);
    }

    private static function replacePlaceholders(string $translate, \Palto\Layout\Client $layout, array $translates): string
    {
        return strtr($translate, [
             ':AD' => $layout->getAd() ? $layout->getAd()->getTitle() : '',
             ':ADDRESS_WITH_REGION' => (
                 $layout->getAd() && $layout->getAd()->getAddress()
                     ? $layout->getAd()->getAddress() . ', '
                     : ''
                ) . $layout->getRegion()->getTitle(),
             ':ADDRESS' => $layout->getAd() && $layout->getAd()->getAddress()
                 ? $layout->getAd()->getAddress()
                 : '',
             ':CATEGORIES' => $layout->getCategory()
                    ? implode(' - ', $layout->getCategory()->getWithParentsTitles())
                    : '',
              ':REGION' => $layout->getRegion()->getTitle(),
              ':REGION_PREPOSITIONAL' => Russian::regionPrepositional($layout->getRegion()->getTitle()),
              ':CATEGORY_IN_REGION' => $layout->getCategory()
                  ? $layout->getCategory()->getTitle()
                    . ' '
                    . ($translates['в'] ?? 'in')
                    . ' '
                    . Russian::regionPrepositional($layout->getRegion()->getTitle())
                  : $layout->getRegion()->getTitle()
        ]);
    }
}
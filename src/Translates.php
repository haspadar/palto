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

    public static function saveTranslates(array $extractedTranslates, mixed $defaultTranslates, string $fileName)
    {
        $merged = [];
        foreach ($defaultTranslates as $key => $defaultTranslate) {
            $merged[$key] = isset($extractedTranslates[$key]) && $extractedTranslates[$key]
                ? $extractedTranslates[$key]
                : $defaultTranslate;
        }

        $translatesFile = file_get_contents($fileName);
        $lines = explode(PHP_EOL, $translatesFile);
        foreach ($lines as &$line) {
            $isTranslate = mb_strpos($line, '    \'') !== false;
            if ($isTranslate) {
                $nameFrom = mb_strpos($line, '\'');
                $nameTo = mb_strpos($line, '\'', $nameFrom + 1);
                $translateKey = mb_substr($line, $nameFrom + 1, $nameTo - $nameFrom - 1);
                if ($translateKey) {
                    $line = '    \'' . $translateKey . '\' => \'' . $merged[$translateKey] . '\',';
                }
            }
        }

        $replacedContent = implode(PHP_EOL, $lines);
        file_put_contents(
            \Palto\Directory::getConfigsDirectory() . '/translates.old.php',
            file_get_contents(\Palto\Directory::getConfigsDirectory() . '/translates.php')
        );
        file_put_contents(
            \Palto\Directory::getConfigsDirectory() . '/translates.php',
            $replacedContent
        );
    }

    public static function extractTranslates(): array
    {
        $patterns = [
            'partials/header.inc' => [
                'logo_alt' => ['<img src="/img/logo.png" alt="', 0, '"'],
                'html_lang' => ['<html lang="', 0, '"'],
            ],
            'partials/footer.inc' => [
                'footer_text' => ['<td class="tdfooter">', 1, ' | <!--LiveInternet'],
                'cookie_text' => ['<div>', 0, '</div>'],
                'СОГЛАСЕН' => ['cookie_accept">', 0, '</button>', 'ACCEPT'],
            ],
            'partials/pager.inc' => [
                'Предыдущая' => ['previousPageUrl\')?>">« ', 0, '</a>'],
                'Следующая' => ['nextPageUrl\')?>"> ', 0, ' »</a>']
            ],
            '404.php' => [
                '404_h1_ad' => ['<h1>', 0, '</h1>'],
                '404_h1_list' => ['<h1>', 1, '</h1>'],
                '404_h2' => ['<h2>', 0, '</h2>'],
            ],
            'ad.php' => [
                'ad_title' => ['generateHtmlTitle(', 0, ')'],
                'Показать телефон' => ['<?php if ($this->getAd()->getSellerPhone()) :?>
            ', 0, '<?php else :?>'],
                'Нет телефона' => ['<?php else :?>', 0, '<?php endif;?>'],
                'Связаться' => ['nofollow">🤙', 0, '</a>'],
                'Пожаловаться на объявление' => ['send-abuse">⚠️', 0, '</a>'],
                'Жалоба' => ['<label>', 1, ':'],
                'Ваша жалоба успешно отправлена.' => ['display: none">', 0, '</p>'],
                'Похожие объявления' => ['<h2>', 0, '</h2>'],
                'Регион' => ['getRegion()):?>', 0, '<?php endif;?>'],
                'Время публикации' => ['post_time">⏱', 0, ':'],
            ],
            'static/index.php' => [
                'index_h1' => ['<h1>', 0, '</h1>'],
                'index_title' => ['\'title\' => \'', 0, '\''],
                'index_description' => ['\'description\' => \'', 0, '\'']
            ],
            'static/hot.php' => [
                'Горячие объявления' => ['<h2 style="color: #d91b39;">🔥', 0, '</h2>'],
                'Новые объявления' => ['<h2>🔔', 0, '</h2>'],
                'Категории' => ['<h2>🗂 ', 0, '</h2>']
            ],
            'static/categories-list.php' => [
                'categories_title' => ['\'title\' => \'', 0, '\''],
                'categories_description' => ['\'description\' => \'', 0, '\''],
                'categories_h1' => ['<h1>', 0, '</h1>'],
            ],
            'static/regions-list.php' => [
                'regions_title' => ['\'title\' => \'', 0, '\''],
                'regions_description' => ['\'description\' => \'', 0, '\''],
                'regions_h1' => ['<h1>', 0, '</h1>'],
            ],
            'list.php' => [
                'list_title' => ['generateHtmlTitle()  . \'', 0, '\','],
                'list_description' => ['$this->generateHtmlDescription(\'', 0, '\')'],
                'в' => ['$this->getCategory()->getTitle()?> ', 0, ' <?php endif;?><?= $this->getRegion()->getTitle()'],
            ],
            'static/registration.php' => [
                'registration_title' => ['\'title\' => \'', 0, '\''],
                'registration_description' => ['\'description\' => \'', 0, '\''],
                'registration_h1' => ['<h1>', 0, '</h1>'],
                'Зарегистрировать' => ['<button>', 0, '<button>'],
                'Забыли пароль?' => ['<div><a href="#">', 0, '</a>'],
                'Войти' => ['<button class="button">', 0, '</button>'],
            ]
        ];
        $translates = [];
        foreach ($patterns as $file => $fileReplaces) {
            foreach ($fileReplaces as $translateKey => $fileReplace) {
                if ($fileReplace) {
                    $translates[$file][$translateKey] = self::extractLayoutTranslate($fileReplace[0], $fileReplace[1], $fileReplace[2], \Palto\Directory::getLayoutsDirectory() . '/client/' . $file);
                }
            }
        }

        $translates['ad.php']['ad_title'] = ':CATEGORIES - :ADDRESS - ' . ($translates['ad.php']['ad_title'] ? $translates['ad.php']['ad_title'] . ' ' : '') .  ':REGION';
        $translates['list.php']['list_title'] = ':CATEGORIES - :REGION' . ($translates['list.php']['list_title'] ? ' ' . $translates['list.php']['list_title'] : '');
        $translates['list.php']['list_description'] = ($translates['list.php']['list_description'] ? $translates['list.php']['list_description'] . ' ' : '') . ':CATEGORIES - :REGION';
        $translatesValues = [];
        foreach ($translates as $file => $fileTranslates) {
            foreach ($fileTranslates as $translateKey => $translate) {
                $translatesValues[$translateKey] = $translate;
            }
        }

        return $translatesValues;
    }

    public static function extractLayoutTranslate(string $after, int $keyNumber, string $before, string $layout): string
    {
        $content = file_get_contents($layout);
        $afterPosition = mb_strpos($content, $after);
        if ($afterPosition !== false) {
            while ($keyNumber--) {
                $afterPosition = mb_strpos($content, $after, $afterPosition + 1);
            }

            if ($afterPosition !== false) {
                $afterPosition = $afterPosition + mb_strlen($after);
                $beforePosition = mb_strpos($content, $before, $afterPosition);
                if ($beforePosition !== false) {
                    return trim(mb_substr($content, $afterPosition, $beforePosition - $afterPosition));
                }
            }
        }

        return '';
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
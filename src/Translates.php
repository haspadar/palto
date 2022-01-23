<?php

namespace Palto;

use Palto\Layout\Client;

class Translates
{
    /**
     * @var null[]|string[]
     */
    private static array $translates;

    public static function get(string $name, Client $layout): string
    {
        $translates = self::getTranslates();
        $translate = $translates[$name] ?? '';
        if ($translate) {
            $translate = self::replacePlaceholders($translate, $layout, $translates);
            $translate = trim(strtr($translate, [
                '-  -' => '-',
                ': :' => ':',
                '| |' => '|'
            ]));
            if (in_array(mb_substr($translate, 0, 1), ['-', ':', '_'])) {
                $translate = trim(mb_substr($translate, 1));
            }
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
            Directory::getConfigsDirectory() . '/translates.old.php',
            file_get_contents(Directory::getConfigsDirectory() . '/translates.php')
        );
        file_put_contents(
            Directory::getConfigsDirectory() . '/translates.php',
            $replacedContent
        );
    }

    public static function extractTranslates(): array
    {
        $patterns = [
            'partials/header.inc' => [
                'logo_alt' => ['<img src="/img/logo.png" alt="', 0, '"'],
                'html_lang' => ['<html lang="', 0, '"'],
                'Добавить объявление' => ['/registration">', 0, '</a>']
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
//                'ad_title' => ['generateHtmlTitle(', 0, ')'],
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
                'index_description' => ['\'description\' => \'', 0, '\''],

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
                'list_description' => ['$this->generateHtmlDescription(\'', 0, '\')'],
                'list_h1' => ['    ?>: ', 0, '</h1>'],
                'в' => ['$this->getCategory()->getTitle()?> ', 0, '<?php'],
            ],
            'static/registration.php' => [
                'registration_title' => ['\'title\' => \'', 0, '\''],
                'registration_description' => ['\'description\' => \'', 0, '\''],
                'registration_h1' => ['<h1>', 0, '</h1>'],

                'Зарегистрировать' => ['<button>', 0, '<button>'],
                'Забыли пароль?' => ['<div><a href="#">', 0, '</a>'],
                'Войти' => ['<button class="button">', 0, '</button>'],
                'Авторизация' => ['<h2>', 0, '</h2>'],
                'Регистрация' => ['<h2>', 1, '</h2>'],
                'или' => ['<p>', 0, '</p>'],
            ]
        ];
        $fromDonorTranslate = self::extractLayoutTranslate('<?=$this->getRegion()->getTitle()?>', 0, '</span>', Directory::getLayoutsDirectory() . '/client/' . 'ad.php');
        $translates = [];
        foreach ($patterns as $file => $fileReplaces) {
            foreach ($fileReplaces as $translateKey => $fileReplace) {
                if ($fileReplace) {
                    $extracted = self::extractLayoutTranslate($fileReplace[0], $fileReplace[1], $fileReplace[2], Directory::getLayoutsDirectory() . '/client/' . $file);
                    $translates[$file][$translateKey] = $extracted;
                }
            }
        }

//        $translates['ad.php']['ad_title'] = ':CATEGORIES - :ADDRESS - ' . ($translates['ad.php']['ad_title'] ? $translates['ad.php']['ad_title'] . ' ' : '') .  ':REGION';
        if (!$translates['static/registration.php']['Зарегистрировать']) {
            $translates['static/registration.php']['Зарегистрировать'] = self::extractLayoutTranslate('<button class="button">', 1, '</button>', Directory::getLayoutsDirectory() . '/client/static/' . 'registration.php');
        }

        $listTitleVariant = self::extractLayoutTranslate('$this->generateHtmlTitle(\'', 0, '\'', Directory::getLayoutsDirectory() . '/client/list.php');
        if ($listTitleVariant) {
            $translates['list.php']['list_title'] = ':CATEGORIES - ' . $listTitleVariant . ' :REGION_PREPOSITIONAL';
        } else {
            $listTitleVariant = self::extractLayoutTranslate('generateHtmlTitle()  . \'', 0, '\',', Directory::getLayoutsDirectory() . '/client/list.php');
            $translates['list.php']['list_title'] = $listTitleVariant . ' :REGION_PREPOSITIONAL';
        }

        $translates['list.php']['list_description'] = ($translates['list.php']['list_description'] ? $translates['list.php']['list_description'] . ' ' : '') . ':CATEGORIES - :REGION';
        $translates['list.php']['list_h1'] = ':CATEGORY_IN_REGION: ' . ($translates['list.php']['list_h1'] ? $translates['list.php']['list_h1'] . ' ' : '');
        $translates['ad.php']['ad_h1'] = ':AD <span style="color:#999"> ' . $translates['в'] . '  :REGION_PREPOSITIONAL ' . $fromDonorTranslate . '</span>';
        $translatesValues = [];
        foreach ($translates as $file => $fileTranslates) {
            foreach ($fileTranslates as $translateKey => $translate) {
                $translatesValues[$translateKey] = strtr(
                    $translate, [
                        'from craigslist' => $fromDonorTranslate,
                        'с olx' => $fromDonorTranslate
                    ]
                );
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

    private static function replacePlaceholders(string $translate, Client $layout, array $translates): string
    {
        return trim(strtr($translate, [
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
                  : $layout->getRegion()->getTitle(),
        ]));
    }
}
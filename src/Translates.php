<?php

namespace Palto;

use Palto\Layout\Client;

class Translates
{
    private static array $translates = [];

    public static function getValue(string $name): string
    {
        $translates = self::getTranslates();
        if (isset($translates[$name]) && $translates[$name]) {
            return $translates[$name]->getValue();
        }

        return $name;
    }

    public static function getYandexTranslates(string $languageCode): array
    {
//        $existsTranslates = (new Model\Translates())->getTranslates();
//        $translates = [];
//        foreach ($existsTranslates as $key => $value) {
//            $translates[$key] = Yandex::translate(self::isRussianSymbol($key) ? $key : $value, 'ru', $languageCode);
//        }
//
//        return $translates;

        return [];
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
                '???????????????? ????????????????????' => ['/registration">', 0, '</a>']
            ],
            'partials/footer.inc' => [
                'footer_text' => ['<td class="tdfooter">', 1, ' | <!--LiveInternet'],
                'cookie_text' => ['<div>', 0, '</div>'],
                '????????????????' => ['cookie_accept">', 0, '</button>', 'ACCEPT'],
            ],
            'partials/pager.inc' => [
                '????????????????????' => ['previousPageUrl\')?>">?? ', 0, '</a>'],
                '??????????????????' => ['nextPageUrl\')?>"> ', 0, ' ??</a>']
            ],
            '404.php' => [
                '404_h1_ad' => ['<h1>', 0, '</h1>'],
                '404_h1_list' => ['<h1>', 1, '</h1>'],
                '404_h2' => ['<h2>', 0, '</h2>'],
            ],
            'ad.php' => [
//                'ad_title' => ['generateHtmlTitle(', 0, ')'],
                '???????????????? ??????????????' => ['<?php if ($this->getAd()->getSellerPhone()) :?>
            ', 0, '<?php else :?>'],
                '?????? ????????????????' => ['<?php else :?>', 0, '<?php endif;?>'],
                '??????????????????' => ['nofollow">????', 0, '</a>'],
                '???????????????????????? ???? ????????????????????' => ['send-abuse">??????', 0, '</a>'],
                '????????????' => ['<label>', 1, ':'],
                '???????? ???????????? ?????????????? ????????????????????.' => ['display: none">', 0, '</p>'],
                '?????????????? ????????????????????' => ['<h2>', 0, '</h2>'],
                '????????????' => ['getRegion()):?>', 0, '<?php endif;?>'],
                '?????????? ????????????????????' => ['post_time">???', 0, ':'],
            ],
            'static/index.php' => [
                'index_h1' => ['<h1>', 0, '</h1>'],
                'index_title' => ['\'title\' => \'', 0, '\''],
                'index_description' => ['\'description\' => \'', 0, '\''],

                '?????????????? ????????????????????' => ['<h2 style="color: #d91b39;">????', 0, '</h2>'],
                '?????????? ????????????????????' => ['<h2>????', 0, '</h2>'],
                '??????????????????' => ['<h2>???? ', 0, '</h2>']
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
                '??' => ['$this->getCategory()->getTitle()?> ', 0, '<?php'],
            ],
            'static/registration.php' => [
                'registration_title' => ['\'title\' => \'', 0, '\''],
                'registration_description' => ['\'description\' => \'', 0, '\''],
                'registration_h1' => ['<h1>', 0, '</h1>'],

                '????????????????????????????????' => ['<button>', 0, '<button>'],
                '???????????? ?????????????' => ['<div><a href="#">', 0, '</a>'],
                '??????????' => ['<button class="button">', 0, '</button>'],
                '??????????????????????' => ['<h2>', 0, '</h2>'],
                '??????????????????????' => ['<h2>', 1, '</h2>'],
                '??????' => ['<p>', 0, '</p>'],
            ]
        ];
        $fromDonorTranslate = self::extractLayoutTranslate('<?=$this->getRegion()->getTitle()?>', 0, '</span>', self::getLayoutsDirectory() . '/client/' . 'ad.php');
        $translates = [];
        foreach ($patterns as $file => $fileReplaces) {
            foreach ($fileReplaces as $translateKey => $fileReplace) {
                if ($fileReplace) {
                    $extracted = self::extractLayoutTranslate($fileReplace[0], $fileReplace[1], $fileReplace[2], self::getLayoutsDirectory() . '/client/' . $file);
                    $translates[$file][$translateKey] = $extracted;
                }
            }
        }

//        $translates['ad.php']['ad_title'] = ':CATEGORIES - :ADDRESS - ' . ($translates['ad.php']['ad_title'] ? $translates['ad.php']['ad_title'] . ' ' : '') .  ':REGION';
        if (!$translates['static/registration.php']['????????????????????????????????']) {
            $translates['static/registration.php']['????????????????????????????????'] = self::extractLayoutTranslate('<button class="button">', 1, '</button>', self::getLayoutsDirectory() . '/client/static/' . 'registration.php');
        }


        $translates['static/hot.php']['hot_h1'] = $translates['static/index.php']['index_h1'];

        $listTitleVariant = self::extractLayoutTranslate('$this->generateHtmlTitle(\'', 0, '\'', self::getLayoutsDirectory() . '/client/list.php');
        if (!$listTitleVariant) {
            $listTitleVariant = self::extractLayoutTranslate('generateHtmlTitle()  . \'', 0, '\',', self::getLayoutsDirectory() . '/client/list.php');
        }

        $translates['list.php']['list_title'] = ':CATEGORIES - ' . $listTitleVariant . ' :REGION_PREPOSITIONAL';

        $translates['list.php']['list_description'] = ($translates['list.php']['list_description'] ? $translates['list.php']['list_description'] . ' ' : '') . ':CATEGORIES - :REGION';
        $translates['list.php']['list_h1'] = ':CATEGORY_IN_REGION' . ($translates['list.php']['list_h1'] ? ': ' . $translates['list.php']['list_h1'] . ' ' : '');
        $translates['ad.php']['ad_h1'] = ':AD ' . $translates['list.php']['??'] . '  :REGION_PREPOSITIONAL ' . $fromDonorTranslate;
        $translatesValues = [];
        foreach ($translates as $file => $fileTranslates) {
            foreach ($fileTranslates as $translateKey => $translate) {
                $translatesValues[$translateKey] = strtr(
                    $translate, [
                        'from craigslist' => $fromDonorTranslate,
                        '?? olx' => $fromDonorTranslate
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

    public static function replacePlaceholders(string $translate, ?Region $region, ?Category $category, ?Ad $ad)
    {
        $regionTitle = $region ? $region->getTitle() : '';
        $translates = self::getTranslates();

        return trim(strtr(html_entity_decode($translate), [
            ':AD' => $ad ? $ad->getTitle() : '',
            ':ADDRESS_WITH_REGION' => (
                $ad && $ad->getAddress()
                    ? $ad->getAddress() . ', '
                    : ''
                ) . $regionTitle,
            ':ADDRESS' => $ad && $ad->getAddress()
                ? $ad->getAddress()
                : '',
            ':CATEGORIES' => $category
                ? implode(' - ', $category->getTitles())
                : '',
            ':CATEGORY_1' => self::getLevelCategoryTitle($category, 1),
            ':CATEGORY_2' => self::getLevelCategoryTitle($category, 2),
            ':CATEGORY_3' => self::getLevelCategoryTitle($category, 3),
            ':CATEGORY_4' => self::getLevelCategoryTitle($category, 4),

            ':REGION_1_ABBREVIATION' => self::getLevelRegionAbbreviation($region, 1),
            ':REGION_2_ABBREVIATION' => self::getLevelRegionAbbreviation($region, 2),
            ':REGION_3_ABBREVIATION' => self::getLevelRegionAbbreviation($region, 3),

            ':REGION_0' => self::getLevelRegionTitle($region, 0),
            ':REGION_1' => self::getLevelRegionTitle($region, 1),
            ':REGION_2' => self::getLevelRegionTitle($region, 2),
            ':REGION_3' => self::getLevelRegionTitle($region, 3),

            ':REGION_ABBREVIATION' => $region ? $region->getAbbreviation() : '',
            ':REGION_PREPOSITIONAL' => $region ? Russian::regionPrepositional($region->getTitle()) : '',
            ':REGION' => $regionTitle,
            ':CATEGORY_IN_REGION' => $category
                ? $category->getTitle()
                    . ' '
                    . ($translates['??']->getValue() ?? 'in')
                    . ' '
                    . ($region ? Russian::regionPrepositional($region->getTitle()) : '')
                : $regionTitle,
            ':CATEGORY' => $category ? $category->getTitle() : '',
        ]));
    }

    public static function updateByName(string $name, string $value)
    {
        (new \Palto\Model\Translates())->updateByName($name, $value);
    }

    public static function replace(string $key, string $value)
    {
        $translates = file_get_contents(Directory::getConfigsDirectory() . '/translates.php');
        $lines = explode(PHP_EOL, $translates);
        foreach ($lines as &$line) {
            if (mb_strpos(trim($line), "'" . $key . "'") === 0) {
                $line = "    '" . $key . "' => '" . $value . "',";
            }
        }

        file_put_contents(
            Directory::getConfigsDirectory() . '/translates.php',
            implode(PHP_EOL, $lines)
        );
    }

    public static function removeExtra(string $translate): string
    {
        $replaced = trim(strtr($translate, [
            '-  -' => '-',
            ': :' => ':',
            '| |' => '|'
        ]));
        if (in_array(mb_substr($replaced, 0, 1), ['-', ':', '_'])) {
            $replaced = trim(mb_substr($replaced, 1));
        }

        return $replaced;
    }

    /**
     * @return Translate[]
     */
    public static function getTranslates(): array
    {
        if (!self::$translates) {
            self::$translates = [];
            foreach ((new Model\Translates())->getTranslates() as $translate) {
                self::$translates[$translate['name']] = new Translate($translate);
            }
        }

        return self::$translates;
    }

    private static function isRussianSymbol(string $text): bool
    {
        return preg_match('/[??-????-??????]/u', $text);
    }


    private static function getLayoutsDirectory(): string
    {
        return Directory::getRootDirectory() . '/layouts';
    }

    private static function getLevelCategoryTitle(?Category $category, int $level): string
    {
        if (!$category || $category->getLevel() < $level) {
            return '';
        }

        if ($category->getLevel() == $level) {
            return $category->getTitle();
        }

        foreach ($category->getParents() as $parent) {
            if ($parent->getLevel() == $level) {
                return $parent->getTitle();
            }
        }

        return '';
    }

    private static function getLevelRegionAbbreviation(?Region $region, int $level): string
    {
        if (!$region || $region->getLevel() < $level) {
            return '';
        }

        if ($region->getLevel() == $level) {
            return $region->getAbbreviation();
        }

        foreach ($region->getParents() as $parent) {
            if ($parent->getLevel() == $level) {
                return $parent->getAbbreviation();
            }
        }

        return '';
    }

    private static function getLevelRegionTitle(?Region $region, int $level): string
    {
        if (!$region || $region->getLevel() < $level) {
            return '';
        }

        if ($region->getLevel() == $level) {
            return $region->getTitle();
        }

        foreach ($region->getParents() as $parent) {
            if ($parent->getLevel() == $level) {
                return $parent->getTitle();
            }
        }

        return '';
    }

    public static function update(array $updates, int $id)
    {
        (new \Palto\Model\Translates())->update($updates, $id);
    }

    public static function getById(int $id): Translate
    {
        return new Translate((new \Palto\Model\Translates())->getById($id));
    }
}
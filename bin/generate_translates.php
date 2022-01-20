#!/usr/bin/php
<?php

require_once __DIR__ . '/autoload_require_composer.php';

$layoutsDirectory = $argv[1] ?? '';
$extractedTranslates = extractTranslates($layoutsDirectory);
$isRussian = mb_substr($extractedTranslates['html_lang'], 0, 2) == 'ru';
$fileName = \Palto\Directory::getConfigsDirectory() . ($isRussian ? '/translates.russian.php' : '/translates.english.php');
$defaultTranslates = require_once $fileName;
$extractedTranslates['ad_h1'] = ':AD <span style="color:#999"> ' . $defaultTranslates['в'] . ' :ADDRESS_WITH_REGION ' . ($isRussian ? 'c ' : 'from ') . \Palto\Directory::getProjectShortName();

saveTranslates($extractedTranslates, $defaultTranslates, $fileName);

$extractedCounters = extractCounters($layoutsDirectory);

saveCounters($extractedCounters, \Palto\Directory::getConfigsDirectory() . '/counters.php');

function saveTranslates(array $extractedTranslates, mixed $defaultTranslates, string $fileName)
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
    file_put_contents(\Palto\Directory::getConfigsDirectory() . '/translates.old.php', file_get_contents(\Palto\Directory::getConfigsDirectory() . '/translates.php'));
    file_put_contents(\Palto\Directory::getConfigsDirectory() . '/translates.php', $replacedContent);
}

function saveCounters($extractedCounters, $fileName)
{
    $content = "<?php
return [
    'liveinternet' => '" . ($extractedCounters['liveinternet'] ?? '') . "',

    'google' => '" . ($extractedCounters['google'] ?? '') . "'
];";
    file_put_contents($fileName, $content);
}

function extractTranslates(string $layoutsDirectory)
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
                $translates[$file][$translateKey] = extractLayoutTranslate($fileReplace[0], $fileReplace[1], $fileReplace[2], \Palto\Directory::getRootDirectory() . '/' . $layoutsDirectory . '/client/' . $file);
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

function extractLayoutTranslate(string $after, int $keyNumber, string $before, string $layout): string
{
    $content = file_get_contents($layout);
    $afterPosition = mb_strpos($content, $after);
    if ($afterPosition !== false) {
        while ($keyNumber--) {
            $afterPosition = mb_strpos($content, $after, $afterPosition + 1);
        }

        if ($afterPosition !== false) {
            $afterPosition = $afterPosition + mb_strlen($after);
            if ($after == 'getRegion()):?>') {
                \Palto\Debug::dump($afterPosition);
                \Palto\Debug::dump($layout);
                \Palto\Debug::dump($keyNumber);
                exit;
            }

            $beforePosition = mb_strpos($content, $before, $afterPosition);
            if ($beforePosition !== false) {
                return trim(mb_substr($content, $afterPosition, $beforePosition - $afterPosition));
            }
        }
    }

    return '';
}

function extractCounters($layoutsDirectory)
{
    $patterns = [
        'partials/footer.inc' => [
            'liveinternet' => ['<!--LiveInternet counter-->', 0, '<!--/LiveInternet-->'],
        ],
        'list.php' => [
            'google' => ['</h1>', 0, '<?php if ($flashMessage)']
        ]
    ];

    $counters = [];
    foreach ($patterns as $file => $fileReplaces) {
        foreach ($fileReplaces as $translateKey => $fileReplace) {
            if ($fileReplace) {
                $counters[$translateKey] = extractLayoutTranslate($fileReplace[0], $fileReplace[1], $fileReplace[2], \Palto\Directory::getRootDirectory() . '/' . $layoutsDirectory . '/client/' . $file);
            }
        }
    }

    return $counters;
}
#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Update;

require_once __DIR__ . '/autoload_require_composer.php';

$extractedTranslates = extractTranslates();
\Palto\Debug::dump($extractedTranslates);exit;

//\Palto\Debug::dump(\Palto\Translates::getYandexTranslates('ru'));
//$translates = require_once 'translates.php';
//$codes = \Palto\Yandex::getLanguageCodes();
//\Palto\Debug::dump($codes);exit;
//$translate = \Palto\Yandex::translate('Привет','ru', 'en');
//\Palto\Debug::dump($translate);

function extractTranslates()
{
    $replaces = [
        'partials/header.inc' => [
            'logo_alt' => ['<img src="/img/logo.png" alt="', 0, '"'],
            'html_lang' => ['<html lang="', 0, '"'],
        ],
        'partials/footer.inc' => [
            'Частные бесплатные объявления в %s' => ['class="footer">', 0, '</a>'],
            'Агрегатор всех местных досок объявлений' => ['</a> - ', 0, ' | <a href="'],
            'Контакты' => ['class="footer">', 1, ': '],
            'Текст про куки' => ['cookie_notification">
    <div>', 0, '</div>', 'This website uses cookies to personalise content and ads, to provide social media features and to analyse our traffic. We also share information about your use of our site with our social media, advertising and analytics partners who may combine it with other information that you’ve provided to them or that they’ve collected from your use of their services.'],
            'СОГЛАСЕН' => ['cookie_accept">', 0, '</button>', 'ACCEPT'],
            'Следующая' => ''
        ],
        'partials/pager.inc' => [
            'Предыдущая' => ['previousPageUrl\')?>">« ', 0, '</a>'],
            'Следующая' => ['nextPageUrl\')?>"> ', 0, ' »</a>']
        ],
        '404.php' => [
            'Объявление было удалено' => ['<h1>', 0, '</h1>'],
            'Не найдено' => ['<h1>', 1, '</h1>'],
        ],
        'static/categories-list.php' => [[]]
    ];
    $translates = [];
    foreach ($replaces as $file => $fileReplaces) {
        foreach ($fileReplaces as $translateKey => $fileReplace) {
            if ($fileReplace) {
                $translates[$file][$translateKey] = extractLayoutTranslate($fileReplace[0], $fileReplace[1], $fileReplace[2], $file);
            }
        }
    }

    return $translates;
}

function extractLayoutTranslate(string $after, int $keyNumber, string $before, string $layout): string
{
    $content = file_get_contents(\Palto\Directory::getStructureLayoutsDirectory() . '/' . $layout);
    $afterPosition = mb_strpos($content, $after);
    if ($keyNumber == 1) {
        $afterPosition = mb_strpos($content, $after, $afterPosition + 1);
    }

    $afterPosition = $afterPosition + mb_strlen($after);
    $beforePosition = mb_strpos($content, $before, $afterPosition);
    if ($beforePosition !== false) {
        return mb_substr($content, $afterPosition, $beforePosition - $afterPosition);
    }

    return '';
}
<?php

use Palto\Categories;
use Palto\Parser;
use Symfony\Component\DomCrawler\Crawler;
use Palto\Logger;

require realpath(dirname(__DIR__) . '/../../') . '/vendor/autoload.php';

//olx.kz

$donorUrl = Parser::checkDonorUrl();
$level1Response = \Pylesos\PylesosService::get($donorUrl . '/sitemap', [], \Palto\Config::getEnv());
$categoriesDocument = new Crawler($level1Response->getResponse());
$categoriesDocument->filter('h3')->each(function (Crawler $level1Category, $i) use (&$level1Categories) {
    if ($level1Category->filter('a span')->count()) {
        $level1CategoryTitle = $level1Category->filter('a span')->text();
        $level1CategoryUrl = $level1Category->filter('a')->first()->attr('href');
        Logger::debug($level1CategoryTitle . '(' . $level1CategoryUrl . ')');
        Categories::safeAdd(
            [
                'title' => $level1CategoryTitle,
                'donor_url' => $level1CategoryUrl,
            ]
        );
    }
});
$levelCategories = [];
$categoriesDocument->filter('.part25>ul>li')->each(function (Crawler $level2Category, $i) use (&$levelCategories, $level1Categories) {
    $urlParts = explode('/', $level2Category->filter('a')->first()->attr('href'));
    unset($urlParts[count($urlParts) - 1]);
    unset($urlParts[count($urlParts) - 1]);
    $level1CategoryDonorUrl = implode('/', $urlParts) . '/';
    $level1Category = Categories::getByDonorUrl($level1CategoryDonorUrl, 1);
    $level2CategoryTitle = $level2Category->filter('a')->first()->text();
    $level2CategoryUrl = $level2Category->filter('a')->first()->attr('href');
    Logger::debug($level2CategoryTitle . '(' . $level2CategoryUrl . ')');
    $level2 = Categories::safeAdd(
        [
            'parent_id' => $level1Category->getId(),
            'title' => $level2CategoryTitle,
            'donor_url' => $level2CategoryUrl,
        ]
    );
    $level2Category->filter('ul>li')->each(function (Crawler $level3Category, $_) use (&$level3Categories, $level2, $level1Category) {
        $level3CategoryTitle = $level3Category->filter('a')->first()->text();
        $level3CategoryUrl = $level3Category->filter('a')->first()->attr('href');
        Categories::safeAdd(
            [
                'parent_id' => $level2->getId(),
                'title' => $level3CategoryTitle,
                'donor_url' => $level3CategoryUrl,
                'level' => 3
            ]
        );
    });
});
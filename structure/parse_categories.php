<?php

use Palto\Palto;
use Pylesos\PylesosService;
use Symfony\Component\DomCrawler\Crawler;

const DONOR_URL = 'http://www.olx.pl';

require 'vendor/autoload.php';

$palto = new Palto();
$level1Response = PylesosService::get(DONOR_URL . '/sitemap', [], $palto->getEnv());
$categoriesDocument = new Crawler($level1Response->getResponse());
$categoriesDocument->filter('h3')->each(function (Crawler $level1Category, $i) use (&$level1Categories, $palto) {
    if ($level1Category->filter('a span')->count()) {
        $level1CategoryTitle = $level1Category->filter('a span')->text();
        $level1CategoryUrl = $level1Category->filter('a')->first()->attr('href');
        $palto->getLogger()->debug($level1CategoryTitle . '(' . $level1CategoryUrl . ')');
        $palto->getCategoryId(
            [
                'title' => $level1CategoryTitle,
                'donor_url' => $level1CategoryUrl,
                'level' => 1,
                'tree_id' => $palto->getDb()->queryFirstField('SELECT MAX(tree_id) FROM categories') + 1,
                'url' => $palto->findCategoryUrl($level1CategoryTitle, 1),
                'create_time' => (new DateTime())->format('Y-m-d H:i:s')
            ]
        );
    }
});
$levelCategories = [];
$categoriesDocument->filter('.part25>ul>li')->each(function (Crawler $level2Category, $i) use (&$levelCategories, $level1Categories, $palto) {
    $urlParts = explode('/', $level2Category->filter('a')->first()->attr('href'));
    unset($urlParts[count($urlParts) - 1]);
    unset($urlParts[count($urlParts) - 1]);
    $level1CategoryDonorUrl = implode('/', $urlParts) . '/';
    $level1Category = $palto->getDb()->queryFirstRow(
        'SELECT * FROM categories WHERE donor_url = %s AND level = %d',
        $level1CategoryDonorUrl,
        1
    );
    $level2CategoryTitle = $level2Category->filter('a')->first()->text();
    $level2CategoryUrl = $level2Category->filter('a')->first()->attr('href');
    $palto->getLogger()->debug($level2CategoryTitle . '(' . $level2CategoryUrl . ')');
    $level2Id = $palto->getCategoryId(
        [
            'parent_id' => $level1Category['id'],
            'title' => $level2CategoryTitle,
            'donor_url' => $level2CategoryUrl,
            'level' => 2,
            'tree_id' => $level1Category['tree_id'],
            'url' => $palto->findCategoryUrl($level2CategoryTitle, 2),
            'create_time' => (new DateTime())->format('Y-m-d H:i:s')
        ]
    );
    $level2Category->filter('ul>li')->each(function (Crawler $level3Category, $_) use (&$level3Categories, $level2Id, $palto, $level1Category) {
        $level3CategoryTitle = $level3Category->filter('a')->first()->text();
        $level3CategoryUrl = $level3Category->filter('a')->first()->attr('href');
        $palto->getCategoryId(
            [
                'parent_id' => $level2Id,
                'title' => $level3CategoryTitle,
                'donor_url' => $level3CategoryUrl,
                'level' => 3,
                'tree_id' => $level1Category['tree_id'],
                'url' => $palto->findCategoryUrl($level3CategoryTitle, 3),
                'create_time' => (new DateTime())->format('Y-m-d H:i:s')
            ]
        );
    });
});
<?php

use Palto\Categories;
use Palto\Parser;
use Symfony\Component\DomCrawler\Crawler;
use Palto\Logger;

require realpath(dirname(__DIR__) . '/../../') . '/vendor/autoload.php';

$donorUrl = Parser::checkDonorUrl();
$level1Response = file_get_contents('categories.html');
$categoriesDocument = new Crawler(file_get_contents('categories.html'));
$categoriesDocument->filter('._88a995fa')->each(function (Crawler $menu) use ($donorUrl) {
    $level1CategoryLink = $menu->filter('a')->first();
    $level1CategoryTitle = $level1CategoryLink->text();
    $level1CategoryUrl = $level1CategoryLink->attr('href');
    Logger::debug($level1CategoryUrl);
    $level1 = Categories::safeAdd([
        'title' => $level1CategoryTitle,
        'donor_url' => $donorUrl . $level1CategoryUrl,
    ]);
    $menu->filter('a')->each(function (Crawler $categoryLevel2, $i) use ($level1, $menu, $donorUrl) {
        $categoryLevel2Title = $categoryLevel2->text();
        $categoryLevel2Url = $categoryLevel2->attr('href');
        $isLevel1 = $i == 0;
        if (!$isLevel1) {
            Logger::debug('Level 2 category ' . $categoryLevel2Title . '(' . $categoryLevel2Url . ')');
            $level2 = Categories::safeAdd([
                'parent_id' => $level1->getId(),
                'title' => $categoryLevel2Title,
                'donor_url' => $donorUrl . $categoryLevel2Url,
            ]);
            $menu->filter('ul li a')->each(function (Crawler $categoryLevel3, $i) use ($level2, $donorUrl) {
                $level3CategoryTitle = $categoryLevel3->text();
                $level3CategoryUrl = $categoryLevel3->attr('href');
                Logger::debug('Level 3 ' . $level3CategoryTitle . '(' . $level3CategoryUrl . ')');
                Categories::safeAdd([
                    'parent_id' => $level2->getId(),
                    'title' => $level3CategoryTitle,
                    'donor_url' => $donorUrl . $level3CategoryUrl,
                ]);
            });
        }
    });
});

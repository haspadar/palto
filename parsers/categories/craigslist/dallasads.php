<?php

use Palto\Categories;
use Symfony\Component\DomCrawler\Crawler;
use Palto\Logger;


require realpath(dirname(__DIR__) . '/../../') . '/vendor/autoload.php';

$donorUrl = \Palto\Parser::checkDonorUrl();
$level1Response = \Pylesos\PylesosService::get($donorUrl, [], \Palto\Config::getEnv());
$categoriesDocument = new Crawler($level1Response->getResponse());
if (!$categoriesDocument->filter('.col')->count()) {
    \Palto\Debug::dump($level1Response->getResponse(), 'No found categories');
}

$categoriesDocument->filter('.col')->each(function (Crawler $menu) use ($donorUrl) {
    $leve1Title = strip_tags($menu->filter('h3 a span')->text());
    $level1DonorUrl = $menu->filter('a')->attr('href');
    if ($leve1Title !== 'discussion forums') {
        $level1 = Categories::safeAdd(
            [
                'title' => ucfirst($leve1Title),
                'donor_url' => $donorUrl . $level1DonorUrl,
            ]
        );
        Logger::debug('Added ' . $leve1Title . '(' . $level1DonorUrl . ')');
        $menu->filter('ul li')->each(function (Crawler $li) use ($donorUrl, $level1) {
            $leve2Title = strip_tags($li->filter('a span')->text());
            $level2DonorUrl = $li->filter('a')->attr('href');
            Categories::safeAdd(
                [
                    'title' => ucfirst($leve2Title),
                    'donor_url' => $donorUrl . $level2DonorUrl,
                    'parent_id' => $level1->getId(),
                ]
            );
            Logger::debug('Added ' . $leve2Title . '(' . $level2DonorUrl . ')');
        });
    }
});
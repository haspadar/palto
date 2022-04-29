<?php

use Symfony\Component\DomCrawler\Crawler;
use Palto\Logger;

require realpath(dirname(__DIR__) . '/../../../') . '/vendor/autoload.php';

$donorUrl = 'https://en.wikipedia.org/wiki/List_of_cat_breeds';
$response = \Pylesos\PylesosService::get($donorUrl, [], \Palto\Config::getEnv());
$categoriesDocument = new Crawler($response->getResponse());

$catsGroup = \Palto\Categories::safeAdd(
    [
        'title' => 'Cats',
        'donor_url' => $donorUrl
    ]
);

$categoriesDocument->filter('.wikitable th a')->each(function (Crawler $a) use ($catsGroup) {
    $breed = $a->text();
    \Palto\Categories::safeAdd(
        [
            'title' => $breed,
            'parent_id' => $catsGroup->getId()
        ]
    );
    Logger::debug('Added ' . $breed);
});
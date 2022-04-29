<?php

use Symfony\Component\DomCrawler\Crawler;
use Palto\Logger;

require realpath(dirname(__DIR__) . '/../../') . '/vendor/autoload.php';

$donorUrl = 'http://whoyougle.com/services/dogs/list';
$response = \Pylesos\PylesosService::get($donorUrl, [], \Palto\Config::getEnv());
$categoriesDocument = new Crawler($response->getResponse());

$dogsGroup = \Palto\Categories::safeAdd(
    [
        'title' => 'Dogs',
        'donor_url' => $donorUrl
    ]
);

$categoriesDocument->filter('.apply-filter')->each(function (Crawler $tr) use ($dogsGroup) {
    $breed = $tr->filter('td')->eq(1)->text();
    \Palto\Categories::safeAdd(
        [
            'title' => $breed,
            'parent_id' => $dogsGroup->getId()
        ]
    );
    Logger::debug('Added ' . $breed);
});
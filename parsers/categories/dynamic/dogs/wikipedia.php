<?php

use Symfony\Component\DomCrawler\Crawler;
use Palto\Logger;

require realpath(dirname(__DIR__) . '/../../../') . '/vendor/autoload.php';

$donorUrl = 'https://en.wikipedia.org/wiki/List_of_dog_breeds';
$response = \Pylesos\PylesosService::get($donorUrl, [], \Palto\Config::getEnv());
$categoriesDocument = new Crawler($response->getResponse());

$dogsGroup = \Palto\Categories::safeAdd(
    [
        'title' => 'Dogs',
        'donor_url' => $donorUrl
    ]
);

$categoriesDocument->filter('.div-col a')->each(function (Crawler $a) use ($dogsGroup) {
    $breed = $a->text();
    if (substr($breed, 0, 1) != '[') {
        \Palto\Categories::safeAdd(
            [
                'title' => $breed,
                'parent_id' => $dogsGroup->getId()
            ]
        );
        Logger::debug('Added ' . $breed);
    }
});
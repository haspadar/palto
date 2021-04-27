<?php

use Palto\Palto;
use Pylesos\PylesosService;
use Pylesos\Scheduler;
use simplehtmldom\HtmlDocument;

require 'vendor/autoload.php';

$palto = new Palto();
$scheduler = new Scheduler($palto->getEnv());
$scheduler->run(
    function () use ($palto) {
        foreach ($palto->getDb()->query("SELECT * FROM categories WHERE level = %d", 2) as $level2) {
            $palto->getLogger()->debug('Parsing category ' . $level2['title']);
            $fullLevel2Url = 'https://losangeles.craigslist.org' . $level2['url'];
            $level2Response = PylesosService::download($fullLevel2Url, $palto->getEnv());
            $level2Document = new HtmlDocument($level2Response->getResponse());
            $ads = $level2Document->find('.result-row');
            $palto->getLogger()->debug('Found ' . count($ads) . ' ads');
            foreach ($ads as $resultRow) {
                $adUrl = $resultRow->find('h3.result-heading a', 0)->href;
                if (isUrlsRegionsEquals($adUrl, $fullLevel2Url)) {
                    parseAd($palto, $adUrl, $level2);
                }
            }
        }
    }
);

function parseAd($palto, $adUrl, $level2) {
    $adResponse = PylesosService::download($adUrl, $palto->getEnv());
    $adDocument = new HtmlDocument($adResponse->getResponse());
    $regionId = getRegionId($adDocument, $palto);
    $ad = [
        'title' => $adDocument->find('#titletextonly', 0)->innertext,
        'url' => $adUrl,
        'category_id' => $level2['id'],
        'text' => trim(explode(
            '</div></div>',
            $adDocument->find('#postingbody', 0)->innertext)[1]
        ),
        'address' => $adDocument->find('#titletextonly small', 0)
            ? strtr(trim($adDocument->find('#titletextonly small', 0)->innertext), [
                '(' => '',
                ')' => '',
            ]) : '',
        'coordinates' => getCoordinates($adDocument),
        'post_time' => (new DateTime($adDocument->find('.postinginfos .postinginfo time', 0)->datetime))
            ->format('Y-m-d H:i:s'),
        'region_id' => $regionId,
    ];
    addAd($ad, getImages($adDocument), $palto);
}

function getImages($adDocument) {
    $images = [];
    foreach ($adDocument->find('#thumbs a') as $link) {
        $bigImage = $link->href;
        $smallImage = $link->find('img', 0)->src;
        $images[] = ['big' => $bigImage, 'small' => $smallImage];
    }

    return $images;
}

function getCoordinates($adDocument) {
    $latitude = $adDocument->find('#map', 0)->{'data-latitude'};
    $longitude = $adDocument->find('#map', 0)->{'data-longitude'};
    $accuracy = $adDocument->find('#map', 0)->{'data-accuracy'};

    return implode(',', [$latitude, $longitude, $accuracy]);
}

function getRegionId(HtmlDocument $adDocument, Palto $palto) {
    $link = $adDocument->find('.subarea a', 0);
    if ($link) {
        $regionTitle = $link->innertext;
        $regionUrl = $link->href;
        $found = $palto->getDb()->queryFirstRow('SELECT * FROM regions WHERE url = %s', $regionUrl);
        if (!$found) {
            $palto->getDb()->insert('regions', [
                'url' => $regionUrl,
                'title' => $regionTitle,
                'level' => 1
            ]);
            $palto->getLogger()->debug('Added region ' . $regionTitle);

            return $palto->getDb()->insertId();
        }

        return $found['id'];
    }

    return null;
}

function addAd(array $data, array $images, Palto $palto) {
    $url = $data['url'];
    $found = $palto->getDb()->queryFirstRow('SELECT * FROM ads WHERE url = %s', $url);
    if (!$found) {
        $palto->getDb()->insert('ads', $data);
        $adId = $palto->getDb()->insertId();
        foreach ($images as $image) {
            $palto->getDb()->insert('ads_images', [
                'small' => $image['small'],
                'big' => $image['big'],
                'ad_id' => $adId,
            ]);
        }

        $palto->getLogger()->debug('Added ad with ' . count($images) . ' images');

        return $adId;
    }

    $palto->getLogger()->debug('Ignored ad with existing url');

    return $found['id'];
}

function isUrlsRegionsEquals($url1, $url2) {
    return explode('.', $url1)[0] == explode('.', $url2)[0];
}
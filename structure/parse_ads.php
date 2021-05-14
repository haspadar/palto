<?php

use Palto\Palto;
use Palto\Status;
use Pylesos\PylesosService;
use Pylesos\Scheduler;
use simplehtmldom\HtmlDocument;

const DONOR_URL = 'https://losangeles.craigslist.org';

require 'vendor/autoload.php';

$palto = new Palto();
$pid = Status::getParserPid(Status::PARSE_ADS_SCRIPT);
$palto->getLogger()->info('Started ads parsing with pid=' . $pid);
$scheduler = new Scheduler($palto->getEnv());
$scheduler->run(
    function () use ($palto, $pid) {
        $level2Categories = $palto->getDb()->query("SELECT * FROM categories WHERE level = %d", 2);
        if ($level2Categories) {
            shuffle($level2Categories);
            $level2CategoriesCount = count($level2Categories);
            foreach ($level2Categories as $level2Key => $level2) {
                $logContent = [
                    'iteration' => ($level2Key + 1) . '/' . $level2CategoriesCount,
                    'pid' => $pid
                ];
                $palto->getLogger()->info('Parsing category ' . $level2['title'], $logContent);
                parseCategory($palto, $level2, $level2['donor_url'], $logContent);
            }
        } else {
            $palto->getLogger()->info('Categories not found');
        }
    }
);

function parseCategory(Palto $palto, array $category, string $url, array $logContent = []) {
    $fullLevel2Url = DONOR_URL . $url;
    $level2Response = PylesosService::get($fullLevel2Url, $palto->getEnv());
    $level2Document = new HtmlDocument($level2Response->getResponse());
    $ads = $level2Document->find('.result-row');
    $extendedLogContext = array_merge([
        'category' => $category['title'],
        'url' => $fullLevel2Url
    ], $logContent);
    $palto->getLogger()->info('Found ' . count($ads) . ' ads', $extendedLogContext);
    $addedAdsCount = 0;
    foreach ($ads as $resultRow) {
        $adUrl = $resultRow->find('h3.result-heading a', 0)->href;
        if (isUrlsRegionsEquals($adUrl, $fullLevel2Url)) {
            if (!$palto->isAdUrlExists($adUrl)) {
                $isAdded = parseAd($palto, $adUrl, $category);
                if ($isAdded) {
                    $addedAdsCount++;
                }
            } else {
                $palto->getLogger()->debug('Ad with url ' . $adUrl . ' already exists');
            }
        }
    }

    $palto->getLogger()->info('Added ' . $addedAdsCount . ' ads from page ' . $url, $extendedLogContext);
    $nextPageSelector = '.paginator .buttons a.next]';
    if ($level2Document->find($nextPageSelector, 0)) {
        $palto->getLogger()->debug('Parsing next page ' . $level2Document->find($nextPageSelector, 0)->href);
        parseCategory($palto, $category, $level2Document->find($nextPageSelector, 0)->href, $logContent);
    }
}

function parseAd(Palto $palto, $adUrl, $level2) {
    $adResponse = PylesosService::get($adUrl, $palto->getEnv());
    $adDocument = new HtmlDocument($adResponse->getResponse());
    $regionLink = $adDocument->find('.subarea a', 0);
    if ($regionLink) {
        $regionTitle = $regionLink->innertext;
        $regionId = $palto->getRegionId([
            'donor_url' => $regionLink->href,
            'url' => $palto->findRegionUrl($regionTitle),
            'title' => $palto->upperCaseEveryWord($regionTitle),
            'level' => 1,
            'tree_id' => $palto->getDb()->queryFirstField('SELECT MAX(tree_id) FROM regions') + 1,
            'create_time' => (new DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    $titleElement = $adDocument->find('#titletextonly', 0);
    if ($titleElement) {
        $ad = [
            'title' => $titleElement->innertext,
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
            'region_id' => $regionId ?? null,
            'price' => 0,
            'currency' => '',
            'seller_name' => '',
            'seller_postfix' => '',
            'seller_phone' => '',
            'create_time' => (new DateTime())->format('Y-m-d H:i:s')
        ];
        $images = getImages($adDocument);
        $details = getDetails($adDocument);
        $palto->addAd($ad, $images, $details);
        $palto->getLogger()->debug(
            'Added ad with ' . count($images) . ' images, ' . count($details) . ' details'
        );

        return true;
    } else {
        $palto->getLogger()->debug('Ignored ad ' . $adUrl . ': empty title');

        return false;
    }
}

function getDetails($adDocument) {
    $details = [];
    foreach ($adDocument->find('.vip-matrix-data table tr') as $property) {
        $details[html_entity_decode($property->find('td', 0)->plaintext)]
            = html_entity_decode($property->find('td', 1)->plaintext);
        $details[html_entity_decode($property->find('td', 3)->plaintext)]
            = html_entity_decode($property->find('td', 4)->plaintext);
    }

    return $details;
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
    $map = $adDocument->find('#map', 0);
    if ($map) {
        $latitude = $map->{'data-latitude'};
        $longitude = $map->{'data-longitude'};
        $accuracy = $map->{'data-accuracy'};

        return implode(',', [$latitude, $longitude, $accuracy]);
    }

    return '';
}

function isUrlsRegionsEquals($url1, $url2) {
    return explode('.', $url1)[0] == explode('.', $url2)[0];
}
<?php

use Palto\Palto;
use Palto\Parser;
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
        $leafCategories = $palto->getDb()->query(
            "SELECT * FROM categories WHERE id NOT IN (SELECT parent_id FROM categories WHERE parent_id IS NOT NULL)"
        );
        if ($leafCategories) {
            shuffle($leafCategories);
            $leafCategoriesCount = count($leafCategories);
            foreach ($leafCategories as $leafKey => $category) {
                $logContent = [
                    'iteration' => ($leafKey + 1) . '/' . $leafCategoriesCount,
                    'pid' => $pid
                ];
                $palto->getLogger()->info('Parsing category ' . $category['title'], $logContent);
                parseCategory($palto, $category, $category['donor_url'], $logContent);
            }
        } else {
            $palto->getLogger()->info('Categories not found');
        }
    },
    function(Exception $e) use ($palto) {
        $palto->getLogger()->error($e->getMessage());
    }
);

function parseCategory(Palto $palto, array $category, string $url, array $logContent = [])
{
    $fullLevel2Url = DONOR_URL . $url;
    $categoryResponse = PylesosService::download($fullLevel2Url, [], [], $palto->getEnv(), 20);
    $categoryDocument = new HtmlDocument($categoryResponse->getResponse());
    $ads = $categoryDocument->find('.result-row');
    $extendedLogContext = array_merge(
        [
            'category' => $category['title'],
            'url' => $fullLevel2Url
        ],
        $logContent
    );
    $palto->getLogger()->info('Found ' . count($ads) . ' ads', $extendedLogContext);
    $addedAdsCount = 0;
    foreach ($ads as $resultRow) {
        $adUrl = $resultRow->find('h3.result-heading a', 0)->href;
        if (isUrlsRegionsEquals($adUrl, $fullLevel2Url)) {
            if (!$palto->isAdUrlExists($adUrl)) {
                $isAdded = $palto->safeTransaction(function () use ($palto, $adUrl, $category) {
                    return parseAd($palto, $adUrl, $category);
                });
                if (!is_bool($isAdded)) {
                    $palto->getLogger()->debug('Skipped wrong ad with url ' . $adUrl);
                }

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
    if ($categoryDocument->find($nextPageSelector, 0)) {
        $palto->getLogger()->debug('Parsing next page ' . $categoryDocument->find($nextPageSelector, 0)->href);
        parseCategory($palto, $category, $categoryDocument->find($nextPageSelector, 0)->href, $logContent);
    }
}

function parseAd(Palto $palto, $adUrl, $level2)
{
    $adResponse = PylesosService::download($adUrl, [], [], $palto->getEnv(), 20);
    $adDocument = new HtmlDocument($adResponse->getResponse());
    $regionLink = $adDocument->find('.subarea a', 0);
    if ($regionLink) {
        $regionTitle = $regionLink->innertext;
        $regionId = $palto->getRegionId(
            [
                'donor_url' => $regionLink->href,
                'url' => $palto->findRegionUrl($regionTitle),
                'title' => $palto->upperCaseEveryWord($regionTitle),
                'level' => 1,
                'tree_id' => $palto->getDb()->queryFirstField('SELECT MAX(tree_id) FROM regions') + 1,
                'create_time' => (new DateTime())->format('Y-m-d H:i:s')
            ]
        );
    }

    $titleElement = $adDocument->find('#titletextonly', 0);
    if ($titleElement) {
        $priceWithCurrency = $adDocument->find('.postingtitletext .price', 0)->innertext ?? '';
        $currency = $priceWithCurrency ? mb_substr($priceWithCurrency, 0, 1) : '';
        $price = $priceWithCurrency ? Parser::filterPrice(mb_substr($priceWithCurrency, 1)) : 0;
        $ad = [
            'title' => $titleElement->innertext,
            'url' => $adUrl,
            'category_id' => $level2['id'],
            'text' => trim(
                explode(
                    '</div></div>',
                    $adDocument->find('#postingbody', 0)->innertext
                )[1]
            ),
            'address' => $adDocument->find('.postingtitletext small', 0)
                ? strtr(
                    trim($adDocument->find('.postingtitletext small', 0)->innertext),
                    [
                        '(' => '',
                        ')' => '',
                    ]
                ) : '',
            'coordinates' => getCoordinates($adDocument),
            'post_time' => (new DateTime($adDocument->find('.postinginfos .postinginfo time', 0)->datetime))
                ->format('Y-m-d H:i:s'),
            'region_id' => $regionId ?? null,
            'price' => $price,
            'currency' => $currency,
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

function getDetails($adDocument)
{
    $details = [];
    foreach ($adDocument->find('.attrgroup span') as $property) {
        if (mb_strpos($property->plaintext, ':') !== false) {
            list($name, $value) = explode(': ', $property->plaintext);
            $details[$name] = $value;
        }
    }

    return $details;
}

function getImages($adDocument)
{
    $images = [];
    foreach ($adDocument->find('#thumbs a') as $link) {
        $bigImage = $link->href;
        $smallImage = $link->find('img', 0)->src;
        $images[] = ['big' => $bigImage, 'small' => $smallImage];
    }

    return $images;
}

function getCoordinates($adDocument)
{
    $map = $adDocument->find('#map', 0);
    if ($map) {
        $latitude = $map->{'data-latitude'};
        $longitude = $map->{'data-longitude'};
        $accuracy = $map->{'data-accuracy'};

        return implode(',', [$latitude, $longitude, $accuracy]);
    }

    return '';
}

function isUrlsRegionsEquals($url1, $url2)
{
    return explode('.', $url1)[0] == explode('.', $url2)[0];
}
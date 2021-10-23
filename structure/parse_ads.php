<?php

use Palto\Palto;
use Palto\Parser;
use Palto\Status;
use Pylesos\PylesosService;
use Pylesos\Scheduler;
use Symfony\Component\DomCrawler\Crawler;
//use simplehtmldom\HtmlDocument;

const DONOR_URL = 'https://www.olx.ua';

require 'vendor/autoload.php';

$palto = new Palto();
$pid = $palto->getParserPid();
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
        $palto->getLogger()->warning($e->getMessage());
    }
);

function parseCategory(Palto $palto, array $category, string $url, array $logContent = [])
{
    $fullLevel2Url = strpos($url, 'http') !== 0 ? Parser::getDonorUrl() . $url: $url;
    $categoryResponse = PylesosService::get($fullLevel2Url, [], $palto->getEnv());
    $categoryDocument = new Crawler($categoryResponse->getResponse());
    $extendedLogContext = array_merge(
        [
            'category' => $category['title'],
            'url' => $fullLevel2Url
        ],
        $logContent
    );
    $ads = $categoryDocument->filter('#offers_table tr.wrap');
    if (!$ads->count()) {
        $ads = $categoryDocument->filter('.gallerywide li[data-id]');
    }

    $palto->getLogger()->info('Found ' . count($ads) . ' ads', $extendedLogContext);
    $addedAdsCount = 0;
    $ads->each(function (Crawler $resultRow, $i) use (&$level1Categories, $palto, &$addedAdsCount, $category) {
        $adUrl = getAdUrl($resultRow);
        if (!$adUrl) {
            $palto->getLogger()->error('Url not parsed: ' . $resultRow->outerHtml());
        } elseif (!$palto->isAdUrlExists($adUrl)) {
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
    });
    $palto->getLogger()->info('Added ' . $addedAdsCount . ' ads from page ' . $url, $extendedLogContext);
    if (Parser::hasNextPageLinkTag($categoryDocument) && Parser::getNextPageUrl($categoryDocument) <= 10) {
        $palto->getLogger()->debug('Parsing next page ' . Parser::getNextPageUrl($categoryDocument));
        parseCategory($palto, $category, Parser::getNextPageUrl($categoryDocument), $logContent);
    }
}

function parseAd(Palto $palto, $adUrl, $level3)
{
    $adResponse = PylesosService::get($adUrl, [], $palto->getEnv());
    $adDocument = new Crawler($adResponse->getResponse());
    $count = $adDocument->filter('[data-testid=breadcrumb-item]')->count();
    if ($adDocument->filter('[data-testid=breadcrumb-item]')->eq($count - 1)->count()) {
        $breadCrumbLevel2RegionTitle = $adDocument->filter('[data-testid=breadcrumb-item]')->eq($count - 1)->text();
        $breadCrumbLevel2RegionTitleParts = explode(' - ', $breadCrumbLevel2RegionTitle);
        $level2RegionTitle = $breadCrumbLevel2RegionTitleParts[count($breadCrumbLevel2RegionTitleParts) - 1];

        $breadCrumbLevel1RegionTitle = $adDocument->filter('[data-testid=breadcrumb-item]')->eq($count - 2)->text();
        $breadCrumbLevel1RegionTitleParts = explode(' - ', $breadCrumbLevel1RegionTitle);
        $level1RegionTitle = $breadCrumbLevel1RegionTitleParts[count($breadCrumbLevel1RegionTitleParts) - 1];

        $regionLevel1Id = $palto->getRegionId(
            [
                'donor_url' => '',
                'url' => $palto->findRegionUrl($level1RegionTitle),
                'title' => $palto->upperCaseEveryWord($level1RegionTitle),
                'level' => 1,
                'tree_id' => $palto->getDb()->queryFirstField('SELECT MAX(tree_id) FROM regions') + 1,
                'create_time' => (new DateTime())->format('Y-m-d H:i:s')
            ]
        );
        $regionLevel2Id = $palto->getRegionId(
            [
                'donor_url' => '',
                'url' => $palto->findRegionUrl($level2RegionTitle),
                'parent_id' => $regionLevel1Id,
                'title' => $palto->upperCaseEveryWord($level2RegionTitle),
                'level' => 2,
                'tree_id' => $palto->getDb()->queryFirstField('SELECT tree_id FROM regions WHERE id = %d', $regionLevel1Id) + 1,
                'create_time' => (new DateTime())->format('Y-m-d H:i:s')
            ]
        );
        $title = $adDocument->filter('h1')->count() ? $adDocument->filter('h1')->text() : '';
        $html = $adDocument->filter('[data-cy="ad_description"] div')->count()
            ? $adDocument->filter('[data-cy="ad_description"] div')->html()
            : $adDocument->filter('h2+div')->html();
        if ($title && $html) {
            $priceWithCurrency = $adDocument->filter('h3')->count() > 0
                ? $adDocument->filter('h3')->text()
                : '';
            list($price, $currency) = Parser::filterPriceCurrency($priceWithCurrency);
            $ad = [
                'title' => $title,
                'url' => $adUrl,
                'category_id' => $level3['id'],
                'text' => $html,
                'address' => '',
                'coordinates' => '',
                'post_time' => null,
                'region_id' => $regionLevel2Id,
                'price' => $price,
                'currency' => $currency,
                'seller_name' => $adDocument->filter('h2')->text(),
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
}

function getDetails($adDocument)
{
    $translates = Parser::getJsVariable($adDocument, 'window.__INIT_CONFIG__');
    $locale = $translates['locale'];
    $values = Parser::getJsVariable($adDocument, 'window.__PRERENDERED_STATE__');
    $details = [];
    if (isset($translates['language']['messages'][$locale]['posting.private_business.value.private'])) {
        $details['isBusiness'] = isset($values['ad']['ad']['isBusiness'])
            ? $translates['language']['messages'][$locale]['posting.private_business.value.private']
            : $translates['language']['messages'][$locale]['posting.private_business.value.business'];
    }

    if (isset($values['ad']['ad']['params'])) {
        foreach ($values['ad']['ad']['params'] as $param) {
            $details[$param['name']] = strval($param['value']);
        }
    }

    return $details;
}

function getImages($adDocument)
{
    $mainImage = $adDocument->filter('img[src]')->attr('src');
    $otherImages = $adDocument->filter('img[data-srcset]')->each(
        function (Crawler $resultRow, $i) {
            return $resultRow->attr('data-srcset');
        }
    );

    $images = [];
    foreach (array_merge([$mainImage], $otherImages) as $image) {
        $imageParts = explode(';', $image);
        if (mb_substr($imageParts[0], 0, 4) == 'http') {
            $images[] = [
                'small' => $imageParts[0],
                'big' => ''
            ];
        }
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

function getAdUrl(Crawler $resultRow) {
    if ($resultRow->filter('h3 a', 0)->count() > 0) {
        $adUrl = $resultRow->filter('h3 a', 0)->attr('href');
    } elseif ($resultRow->filter('h4 a', 0)->count() > 0) {
        $adUrl = $resultRow->filter('h4 a', 0)->attr('href');
    }

    return $adUrl ?? '';
}
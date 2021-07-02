<?php

use Palto\Palto;
use Palto\Parser;
use Pylesos\PylesosService;
use Pylesos\Scheduler;
use Symfony\Component\DomCrawler\Crawler;

const DONOR_URL = 'https://washingtondc.craigslist.org';

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
        $palto->getLogger()->error($e->getMessage());
    }
);

function parseCategory(Palto $palto, array $category, string $url, array $logContent = [])
{
    $fullLevel2Url = DONOR_URL . $url;
    $categoryResponse = PylesosService::download($fullLevel2Url, [], [], $palto->getEnv(), 20);
    $categoryDocument = new Crawler($categoryResponse->getResponse());
    $extendedLogContext = array_merge(
        [
            'category' => $category['title'],
            'url' => $fullLevel2Url
        ],
        $logContent
    );
    $ads = $categoryDocument->filter('.result-row');
    $palto->getLogger()->info('Found ' . count($ads) . ' ads', $extendedLogContext);
    $addedAdsCount = 0;
    $ads->each(function (Crawler $ad, $i) use ($palto, $fullLevel2Url, $category, &$addedAdsCount) {
        $adUrl = $ad->filter('h3.result-heading a')->first()->attr('href');
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
    });
    $palto->getLogger()->info('Added ' . $addedAdsCount . ' ads from page ' . $url, $extendedLogContext);
    $nextPageSelector = '.paginator .buttons a.next';
    if (count($categoryDocument->filter($nextPageSelector))) {
        $palto->getLogger()->debug(
            'Parsing next page ' . $categoryDocument->filter($nextPageSelector)->first()->attr('href')
        );
        parseCategory(
            $palto,
            $category,
            $categoryDocument->filter($nextPageSelector)->first()->attr('href'),
            $logContent
        );
    }
}

function parseAd(Palto $palto, $adUrl, $level2)
{
    $adResponse = PylesosService::download($adUrl, [], [], $palto->getEnv(), 20);
    $adDocument = new Crawler($adResponse->getResponse());
    $regionLink = $adDocument->filter('.subarea p a');
    if ($regionLink) {
        $regionTitle = $regionLink->text();
        $regionId = $palto->getRegionId(
            [
                'donor_url' => $regionLink->attr('href'),
                'url' => $palto->findRegionUrl($regionTitle),
                'title' => $palto->upperCaseEveryWord($regionTitle),
                'level' => 1,
                'tree_id' => $palto->getDb()->queryFirstField('SELECT MAX(tree_id) FROM regions') + 1,
                'create_time' => (new DateTime())->format('Y-m-d H:i:s')
            ]
        );
    }

    $titleElement = $adDocument->filter('#titletextonly');
    if ($titleElement) {
        $priceWithCurrency = $adDocument->filter('.postingtitletext .price')->first()->html() ?? '';
        $currency = $priceWithCurrency ? mb_substr($priceWithCurrency, 0, 1) : '';
        $price = $priceWithCurrency ? Parser::filterPrice(mb_substr($priceWithCurrency, 1)) : 0;
        var_dump(trim(
            explode(
                '</div></div>',
                $adDocument->filter('#postingbody')->first()->html()
            )[1] ?? $adDocument->filter('#postingbody')->first()->html()
        ));
        var_dump(Parser::removeDivWithClass(
            trim(
                explode(
                    '</div></div>',
                    $adDocument->filter('#postingbody')->first()->html()
                )[1] ?? $adDocument->filter('#postingbody')->first()->html()
            ))
        );
        exit;
        $ad = [
            'title' => $titleElement->html(),
            'url' => $adUrl,
            'category_id' => $level2['id'],
            'text' => getHtml($adDocument),
            'address' => $adDocument->filter('.postingtitletext small')
                ? strtr(
                    trim($adDocument->filter('.postingtitletext small')->first()->html()),
                    [
                        '(' => '',
                        ')' => '',
                    ]
                ) : '',
            'coordinates' => getCoordinates($adDocument),
            'post_time' => (new DateTime($adDocument->filter('.postinginfos .postinginfo time')->first()->attr('datetime')))
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
    $adDocument->filter('.attrgroup span')->each(function (Crawler $property, $i) {
        if (mb_strpos($property->text(), ':') !== false) {
            list($name, $value) = explode(': ', $property->text());
            $details[$name] = $value;
        }
    });

    return $details;
}

function getImages(Crawler $adDocument)
{
    $images = [];
    $adDocument->filter('#thumbs a')->each(function (Crawler $link, $i) use (&$images) {
        $bigImage = $link->attr('href');
        $smallImage = $link->filter('img')->first()->attr('src');
        $images[] = ['big' => $bigImage, 'small' => $smallImage];
    });

    return $images;
}

function getCoordinates(Crawler $adDocument)
{
    $map = $adDocument->filter('#map');
    if ($map) {
        $latitude = $map->first()->attr('data-latitude');
        $longitude = $map->first()->attr('data-longitude');
        $accuracy = $map->first()->attr('data-accuracy');

        return implode(',', [$latitude, $longitude, $accuracy]);
    }

    return '';
}

function isUrlsRegionsEquals($url1, $url2)
{
    return explode('.', $url1)[0] == explode('.', $url2)[0];
}

function getHtml(Crawler $adDocument)
{
    if (count($adDocument->filter('#postingbody')) > 0) {
        $html = $adDocument->filter('#postingbody')->first()->html();
        $parts = explode('</div>
        </div>', $html);

        return isset($parts[1]) ? trim($parts[1]) : $html;
    }

    return '';
}
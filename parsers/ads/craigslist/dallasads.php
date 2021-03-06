<?php

use Palto\Ads;
use Palto\AdsParser;
use Palto\Category;
use Palto\Parser;
use Palto\Regions;
use Palto\Url;
use Symfony\Component\DomCrawler\Crawler;

require realpath(dirname(__DIR__) . '/../../') . '/vendor/autoload.php';

(new class extends AdsParser {
    protected function parseAd(Crawler $adDocument, Category $category, Url $adUrl): int
    {
        $regionLink = $adDocument->filter('.subarea a');
        if ($regionLink->count()) {
            $regionTitle = $regionLink->text();
            $region = Regions::safeAdd([
                'donor_url' => $regionLink->attr('href'),
                'url' => Regions::generateUrl($regionTitle),
                'title' => Parser::upperCaseEveryWord($regionTitle)
            ]);
        }

        $title = Parser::getText($adDocument, ['#titletextonly']);
        if ($title) {
            $priceWithCurrency = Parser::getHtml($adDocument, ['.postingtitletext .price']);
            $currency = $priceWithCurrency ? mb_substr($priceWithCurrency, 0, 1) : '';
            $price = $priceWithCurrency ? Parser::filterPrice(mb_substr($priceWithCurrency, 1)) : 0;
            $postTimeElement = $adDocument->filter('.postinginfos .postinginfo time', 0);
            $ad = [
                'title' => $title,
                'url' => $adUrl,
                'category_id' => $category->getId(),
                'text' => trim(explode(
                        '</div>
        </div>',
                        $adDocument->filter('#postingbody')->html())[1] ?? ''
                ),
                'address' => strtr(trim(Parser::getHtml($adDocument, ['.postingtitletext small'])), [
                    '(' => '',
                    ')' => '',
                ]),
                'coordinates' => $this->getCoordinates($adDocument),
                'post_time' => $postTimeElement
                    ? (new DateTime($postTimeElement->attr('datetime')))->format('Y-m-d H:i:s')
                    : null,
                'region_id' => isset($region) ? $region->getId() : null,
                'price' => $price,
                'currency' => $currency,
            ];
            $images = $this->getImages($adDocument);
            $details = $this->getDetails($adDocument);

            return Ads::add($ad, $images, $details);
        } else {
            \Palto\Logger::debug('Empty ad title: ' . $adUrl->getFull());
        }

        return 0;
    }

    protected function findAds(Crawler $categoryDocument)
    {
        return $categoryDocument->filter('.result-row');
    }

    protected function findAdUrl(Crawler $resultRow, Category|\Palto\Region $category): ?Url
    {
        $url = $resultRow->filter('h3.result-heading a')->attr('href');

        return $url ? new Url($url) : null;
    }

    private function getDetails(Crawler $adDocument): array
    {
        $details = [];
        foreach ($adDocument->filter('.attrgroup span') as $property) {
            $propertyCrawler = new Crawler($property);
            if (mb_strpos($propertyCrawler->text(), ':') !== false) {
                list($name, $value) = explode(': ', $propertyCrawler->text());
                $details[$name] = $value;
            }
        }

        return $details;
    }

    private function getImages($adDocument): array
    {
        $images = [];
        foreach ($adDocument->filter('#thumbs a img') as $link) {
            $linkCrawler = new Crawler($link);
            $smallImage = $linkCrawler->attr('src');
            $bigImage = str_replace('50x50c.jpg', '600x450.jpg', $smallImage);
            $images[] = ['big' => $bigImage, 'small' => $smallImage];
        }

        if (!$images) {
            $bigs = $adDocument->filter('.gallery .swipe img')->each(
                function (Crawler $resultRow, $i) {
                    return $resultRow->attr('src');
                }
            );
            $smalls = $adDocument->filter('#thumbs a img')->each(
                function (Crawler $resultRow, $i) {
                    return $resultRow->attr('src');
                }
            );
            foreach ($bigs as $key => $big) {
                $images[] = ['big' => $big, 'small' => $smalls[$key] ?? str_replace('600x450.jpg', '50x50c.jpg', $big)];
            }
        }

        return $images;
    }

    private function getCoordinates(Crawler $adDocument): string
    {
        $map = $adDocument->filter('#map');
        if ($map->count()) {
            $latitude = Parser::getAttribute($map, 'data-latitude');
            $longitude = Parser::getAttribute($map, 'data-longitude');
            $accuracy = Parser::getAttribute($map, 'data-accuracy');

            return implode(',', [$latitude, $longitude, $accuracy]);
        }

        return '';
    }

})->run(__FILE__);
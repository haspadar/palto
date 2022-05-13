<?php

use Palto\Ads;
use Palto\AdsParser;
use Palto\Categories;
use Palto\Category;
use Palto\Logger;
use Palto\Parser;
use Palto\Regions;
use Palto\Url;
use Symfony\Component\DomCrawler\Crawler;

require realpath(dirname(__DIR__) . '/../../') . '/vendor/autoload.php';

(new class extends AdsParser {
//  параметр ?s=120 указывает количество пропускаемых, а не страницу
    protected const MAX_PAGE_NUMBER = 1200;

    protected function parseAd(Crawler $adDocument, \Palto\Region $region, Url $adUrl): int
    {
        $title = Parser::getText($adDocument, ['#titletextonly']);
        $text = trim(strtr(strip_tags(Parser::getHtml($adDocument, ['#postingbody'])), ['QR Code Link to This Post' => '']));
        if ($title && $text) {
            $category = \Palto\Synonyms::findCategory([$title, mb_substr($text, 0, 200)]);
            $priceWithCurrency = Parser::getHtml($adDocument, ['.postingtitletext .price']);
            $currency = $priceWithCurrency ? mb_substr($priceWithCurrency, 0, 1) : '';
            $price = $priceWithCurrency ? Parser::filterPrice(mb_substr($priceWithCurrency, 1)) : 0;
            $postTimeElement = $adDocument->filter('.postinginfos .postinginfo time', 0);
            $ad = [
                'title' => $title,
                'url' => $adUrl,
                'category_id' => $category->getId(),
                'text' => $text,
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

    public function getTreeLeafs(): array
    {
        return Regions::getLeafs();
    }

    protected function findAds(Crawler $leafDocument)
    {
        return $leafDocument->filter('.result-row');
    }

    protected function findAdUrl(Crawler $resultRow, Category|\Palto\Region $region): ?Url
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
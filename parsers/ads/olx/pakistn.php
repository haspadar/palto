<?php

use Palto\Ads;
use Palto\AdsParser;
use Palto\Category;
use Palto\Parser;
use Palto\Region;
use Palto\Regions;
use Palto\Url;
use Symfony\Component\DomCrawler\Crawler;

require realpath(dirname(__DIR__) . '/../../') . '/vendor/autoload.php';

(new class extends AdsParser {
    protected function parseAd(Crawler $adDocument, Category $category, Url $adUrl): int
    {
        $breadcrumbSelector = Parser::getSelector($adDocument, [
            'ul li'
        ]);
        $count = $adDocument->filter($breadcrumbSelector)->count();
        if ($adDocument->filter($breadcrumbSelector)->eq($count - 1)->count()) {
            list($level1RegionTitle, $level2RegionTitle, $level3RegionTitle) = $this->getRegions($adDocument->filter($breadcrumbSelector));
            if ($level1RegionTitle && $level2RegionTitle && $level3RegionTitle) {
                $regionLevel1 = Regions::safeAdd(
                    [
                        'title' => Parser::upperCaseEveryWord($level1RegionTitle),
                    ]
                );
                $regionLevel2 = Regions::safeAdd(
                    [
                        'parent_id' => $regionLevel1->getId(),
                        'title' => Parser::upperCaseEveryWord($level2RegionTitle),
                    ]
                );
                $regionLevel3 = Regions::safeAdd(
                    [
                        'parent_id' => $regionLevel2->getId(),
                        'title' => Parser::upperCaseEveryWord($level3RegionTitle),
                    ]
                );
                $images = $this->getImages($adDocument);
                $details = $this->getDetails($adDocument);
                $title = Parser::getHtml($adDocument, ['h1', '[data-aut-id="itemTitle"]']);
                $html = Parser::getHtml($adDocument, [
                    'div[aria-label="Details and description"] > div > div > span'
                ]);
                $price = $details['Price'] ?? 0;
                if ($price) {
                    $price = Parser::filterPrice($price);
                }

                $ad = [
                    'title' => $title,
                    'url' => $adUrl,
                    'category_id' => $category->getId(),
                    'text' => $html,
                    'region_id' => $regionLevel3->getId(),
                    'price' => $price,
                    'currency' => 'Rs',
                    'seller_name' => Parser::getHtml($adDocument, ['h2', '[data-aut-id="profileCard"] div a div']),
                ];

                return Ads::add($ad, $images, $details);
            } else {
                \Palto\Logger::warning('Regions not parsed');
            }
        } else {
            \Palto\Logger::warning('Regions not found: empty breadcrumb');
        }

        return 0;
    }

    protected function findAds(Crawler $categoryDocument)
    {
        return $categoryDocument->filter('article');
    }

    protected function findAdUrl(Crawler $resultRow, Category|\Palto\Region $category): ?Url
    {
        $adUrl = $resultRow->filter('a[href]')->attr('href');
        $domain = (new Url($category->getDonorUrl()))->getDomain();

        return isset($adUrl) ? new Url($domain . $adUrl) : null;
    }

    private function getRegions(Crawler $breadcrumbItems): array
    {
        $titles = [];
        for ($i = 0; $i < $breadcrumbItems->count(); $i++) {
            $fullTitle = $breadcrumbItems->eq($i)->text();
            $parts = explode('in ', $fullTitle);
            $titles[] = $parts[1] ?? '';
        }

        $uniqueRegions = array_values(array_filter($titles));

        return [$uniqueRegions[0] ?? '', $uniqueRegions[1] ?? '', $uniqueRegions[2] ?? ''];
    }

    private function getDetails($adDocument): array
    {
//        $description = $adDocument->filter('div[aria-label="Details and description"]');
        $keys = [];
        $values = [];
        $adDocument->filter('div[aria-label="Details and description"] > div > div > div span')->each(function (Crawler $resultRow, $i) use (&$keys, &$values) {
            if (!($i % 2)) {
                $keys[] = $resultRow->html();
            } else {
                $values[] = $resultRow->html();
            }
        });

        return array_combine($keys, $values) ?? [];
    }

    private function getImages($adDocument): array
    {
        return $adDocument->filter('.image-gallery  picture img')->each(
            function (Crawler $resultRow, $i) {
                $bigImageHref = $resultRow->attr('src');
                $smallImageHref = str_replace('-400x300', '-120x90', $bigImageHref);

                return [
                    'small' => $smallImageHref,
                    'big' => $bigImageHref
                ];
            }
        );
    }

    protected function getNextPageUrl(Crawler $leafDocument, Category|Region $leaf, Url $url, int $pageNumber): ?Url
    {
        return new Url($url->getDomain() . $url->getPath() . '?page=' . $this->getNextPageNumber($leafDocument, $leaf, $url, $pageNumber));
    }

    protected function getFirstPageNumber(): int
    {
        return 0;
    }
})->run(__FILE__);
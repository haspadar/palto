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
        $count = $adDocument->filter('[data-testid=breadcrumb-item]')->count();
        if ($adDocument->filter('[data-testid=breadcrumb-item]')->eq($count - 1)->count()) {
            $breadCrumbLevel2RegionTitle = $adDocument->filter('[data-testid=breadcrumb-item]')->eq($count - 1)->text();
            $breadCrumbLevel2RegionTitleParts = explode(' - ', $breadCrumbLevel2RegionTitle);
            $level2RegionTitle = $breadCrumbLevel2RegionTitleParts[count($breadCrumbLevel2RegionTitleParts) - 1];
            $breadCrumbLevel1RegionTitle = $adDocument->filter('[data-testid=breadcrumb-item]')->eq($count - 2)->text();
            $breadCrumbLevel1RegionTitleParts = explode(' - ', $breadCrumbLevel1RegionTitle);
            $level1RegionTitle = $breadCrumbLevel1RegionTitleParts[count($breadCrumbLevel1RegionTitleParts) - 1];
            $level1Region = Regions::safeAdd([
                'url' => Regions::generateUrl($level1RegionTitle),
                'title' => Parser::upperCaseEveryWord($level1RegionTitle),
            ]);
            $level2Region = Regions::safeAdd([
                'url' => Regions::generateUrl($level2RegionTitle),
                'parent_id' => $level1Region->getId(),
                'title' => Parser::upperCaseEveryWord($level2RegionTitle),
            ]);
            $title = $adDocument->filter('h1')->count() ? $adDocument->filter('h1')->text() : '';
            $html = $adDocument->filter('[data-cy="ad_description"] div')->count()
                ? $adDocument->filter('[data-cy="ad_description"] div')->html()
                : $adDocument->filter('h2+div')->html();
            $priceWithCurrency = $adDocument->filter('h3')->count() > 0
                ? $adDocument->filter('h3')->text()
                : '';
            list($price, $currency) = Parser::filterPriceCurrency($priceWithCurrency);
            $ad = [
                'title' => $title,
                'url' => $adUrl,
                'category_id' => $category->getId(),
                'text' => $html,
                'post_time' => null,
                'region_id' => $level2Region->getId(),
                'price' => $price,
                'currency' => $currency,
                'seller_name' => $adDocument->filter('h2')->text()
            ];

            return Ads::add($ad, $this->getImages($adDocument), $this->getDetails($adDocument));
        }

        return 0;
    }

    protected function findAds(Crawler $categoryDocument)
    {
        $ads = $categoryDocument->filter('#offers_table tr.wrap');
        if (!$ads->count()) {
            $ads = $categoryDocument->filter('.gallerywide li[data-id]');
        }

        return $ads;
    }

    protected function findAdUrl(Crawler $resultRow, Category|\Palto\Region $category): ?Url
    {
        if ($resultRow->filter('h3 a')->count() > 0) {
            $adUrl = $resultRow->filter('h3 a')->attr('href');
        } elseif ($resultRow->filter('h4 a')->count() > 0) {
            $adUrl = $resultRow->filter('h4 a')->attr('href');
        }

        return isset($adUrl) ? new Url($adUrl) : null;
    }

    private function getDetails($adDocument): array
    {
        $translates = Parser::getJsVariable($adDocument, 'window.__INIT_CONFIG__');
        $locale = $translates['locale'] ?? '';
        $values = Parser::getJsVariable($adDocument, 'window.__PRERENDERED_STATE__');
        $details = [];
        if ($locale && isset($translates['language']['messages'][$locale]['posting.private_business.value.private'])) {
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

    private function getImages($adDocument): array
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
})->run(__FILE__);
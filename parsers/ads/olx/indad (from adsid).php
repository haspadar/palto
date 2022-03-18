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
        $breadcrumbSelector = Parser::getSelector($adDocument, [
            '[data-testid=breadcrumb-item]',
            '[data-aut-id=breadcrumb] ol li'
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
                $title = Parser::getHtml($adDocument, ['h1', '[data-aut-id="itemTitle"]']);
                $html = Parser::getHtml($adDocument, [
                    '[data-cy="ad_description"] div',
                    'h2+div',
                    '[data-aut-id=itemDescriptionContent]'
                ]);
                if (!$html && $adDocument->filter('h4')->count()) {
                    $html = $adDocument->filter('h4')->parents()->html();
                }

                $priceWithCurrency = Parser::getHtml($adDocument, ['[data-aut-id="itemPrice"]', 'h3']);
                if ($priceWithCurrency) {
                    list($currency, $price) = explode(' ', Parser::getHtml($adDocument, ['[data-aut-id="itemPrice"]', 'h3']));
                    $price = Parser::filterPrice($price);
                }

                $ad = [
                    'title' => $title,
                    'url' => $adUrl,
                    'category_id' => $category->getId(),
                    'text' => $html,
                    'region_id' => $regionLevel3->getId(),
                    'price' => $price ?? 0,
                    'currency' => $currency ?? '',
                    'seller_name' => Parser::getHtml($adDocument, ['h2', '[data-aut-id="profileCard"] div a div']),
                ];
                $images = $this->getImages($adDocument);
                $details = $this->getDetails($adDocument);

                return Ads::add($ad, $images, $details);
            }
        }

        return 0;
    }

    protected function findAds(Crawler $categoryDocument)
    {
        $ads = $categoryDocument->filter('#offers_table tr.wrap');
        if (!$ads->count()) {
            $ads = $categoryDocument->filter('.gallerywide li[data-id]');
            if (!$ads->count()) {
                $ads = $categoryDocument->filter('ul[data-aut-id="itemsList"] li');
            }
        }

        return $ads;
    }

    protected function findAdUrl(Crawler $resultRow, Category $category): ?Url
    {
        if ($resultRow->filter('h3 a', 0)->count() > 0) {
            $adUrl = $resultRow->filter('h3 a', 0)->attr('href');
        } elseif ($resultRow->filter('h4 a', 0)->count() > 0) {
            $adUrl = $resultRow->filter('h4 a', 0)->attr('href');
        } elseif ($resultRow->filter('a[href]', 0)->count() > 0) {
            $adUrl = $resultRow->filter('a[href]', 0)->attr('href');
        }

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
        $translates = Parser::getJsVariable($adDocument, 'window.__INIT_CONFIG__');
        if (isset($translates['locale'])) {
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
        } else {
            $names = [];
            $values = [];
            $adDocument->filter('[data-aut-id="itemParams"] [data-aut-id]')->each(
                function (Crawler $resultRow, $i) use (&$names, &$values) {
                    if (!($i % 2)) {
                        $names[] = $resultRow->html();
                    } else {
                        $values[] = $resultRow->html();
                    }

                    return $resultRow->html();
                }
            );
            $details = array_combine($names, $values);
        }

        return $details ?? [];
    }

    private function getImages($adDocument): array
    {
        $images = $adDocument->filter('.slick-slide figure img')->each(
            function (Crawler $resultRow, $i) {
                $srcSet = $resultRow->attr('srcset');
                if (!$srcSet) {
                    $srcSet = $resultRow->attr('srcSet');
                }

                $parts = explode(',', $srcSet);

                return [
                    'small' => $this->filterImage($parts[0]),
                    'big' => $this->filterImage($parts[count($parts) - 1])
                ];
            }
        );
        if (!$images) {
            $mainImage = $adDocument->filter('img[src]')->attr('src');
            $otherImages = $adDocument->filter('img[data-srcset]')->each(
                function (Crawler $resultRow, $i) {
                    return $resultRow->attr('data-srcset');
                }
            );
            foreach (array_merge([$mainImage], $otherImages) as $image) {
                $imageParts = explode(';', $image);
                if (mb_substr($imageParts[0], 0, 4) == 'http') {
                    $images[] = [
                        'small' => $imageParts[0],
                        'big' => ''
                    ];
                }
            }
        }

        return $images;
    }

    private function filterImage(string $path): string
    {
        $path = trim($path);
        $parts = explode(' ', $path);

        return $parts[0];
    }

//    protected function getNextPageUrl(Crawler $categoryDocument, Category $category, Url $url, int $pageNumber): ?Url
//    {
//        if (Parser::hasNextPageLinkTag($categoryDocument)) {
//            return Parser::getNextPageUrl($categoryDocument);
//        }
//
//        return null;
//    }
})->run(__FILE__);
<?php

namespace Test;

use Crunz\Infrastructure\Psr\Logger\EnabledLoggerDecorator;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Model\Categories;
use Symfony\Component\DomCrawler\Crawler;

class NextPageTest extends Web
{
    public function testNextPage()
    {
        $mainRegionResponse = $this->download('/' . Config::get('DEFAULT_REGION_URL'));
        $crawler = new Crawler($mainRegionResponse->getHtml());
        $this->checkPhpErrors($mainRegionResponse);
        $this->checkLinks($mainRegionResponse);
        $nextPage = $crawler->filter('link[rel=next]');
        $firstPageAd = $crawler->filter('.serp a');
        $this->assertTrue(
            $firstPageAd->count() > 0,
            'Main Region Page hasn\'t ads: ' . $mainRegionResponse->getUrl()
        );
        $firstPageAdUrl = $firstPageAd->attr('href');
        if ($nextPage) {
            $nextUrl = $nextPage->attr('href');
            Debug::dump($nextUrl);
            $nextPageResponse = $this->download($nextUrl);
            $crawler = new Crawler($nextPageResponse->getHtml());
            $secondPageAdUrl = $crawler->filter('.serp a')->attr('href');
            $this->assertTrue(
                $secondPageAdUrl != $firstPageAdUrl,
                'Second page url ' . $secondPageAdUrl . ' is equal to ' . $firstPageAdUrl . ' on page ' . '/' . Config::get('DEFAULT_REGION_URL')
            );
        }
    }
}
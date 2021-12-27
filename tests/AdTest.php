<?php

namespace Test;

use Crunz\Infrastructure\Psr\Logger\EnabledLoggerDecorator;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Model\Categories;
use Symfony\Component\DomCrawler\Crawler;

class AdTest extends Web
{
    protected string $url = '/categories';

    public function testAd()
    {
        $categoriesResponse = $this->download($this->url);
        $crawler = new Crawler($categoriesResponse->getHtml());
        $firstCategoryUrl = $crawler->filter('.table_main a')->attr('href');
        $categoryResponse = $this->download($firstCategoryUrl);
        $crawler = new Crawler($categoryResponse->getHtml());
        $firstAd = $crawler->filter('.serp a');
        $this->assertTrue(
            $firstAd->count() > 0,
            'Categories page hasn\'t ads: ' . $categoryResponse->getUrl()
        );
        $firstAdUrl = $firstAd->attr('href');
        $response = $this->download($firstAdUrl);
        $this->checkPhpErrors($response);
    }
}
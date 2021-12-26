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
        $categoriesResponse = $this->download('/' . Config::get('DEFAULT_REGION_URL'));
        $crawler = new Crawler($categoriesResponse->getHtml());
        $this->checkPhpErrors($categoriesResponse);
        $this->checkLinks($categoriesResponse);
        $nextPage = $crawler->filter('link[rel=next]');
        $firstPageAdUrl = $crawler->filter('.serp a')->attr('href');
        if ($nextPage) {
            $nextUrl = $crawler->filter('link[rel=next]')->attr('href');
            $nextPageResponse = $this->download($nextUrl);
            $crawler = new Crawler($nextPageResponse->getHtml());
            $secondPageAdUrl = $crawler->filter('.serp a')->attr('href');
            $this->assertTrue($secondPageAdUrl != $firstPageAdUrl);
        }
    }
}
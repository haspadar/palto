<?php

namespace Test;

use Crunz\Infrastructure\Psr\Logger\EnabledLoggerDecorator;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Model\Categories;
use Symfony\Component\DomCrawler\Crawler;

class RegionTest extends Web
{
    protected string $url = '/regions';

    public function testRegion()
    {
        $regionsResponse = $this->download($this->url);
        $crawler = new Crawler($regionsResponse->getHtml());
        $firstRegionUrl = $crawler->filter('.table_main a')->attr('href');
        $response = $this->download($firstRegionUrl);
        $this->checkPhpErrors($response);
        $this->checkLinks($response);
    }
}
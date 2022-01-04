<?php

namespace Test;

use Crunz\Infrastructure\Psr\Logger\EnabledLoggerDecorator;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Model\Categories;
use Palto\Model\Regions;
use Symfony\Component\DomCrawler\Crawler;

class RegionTest extends Web
{
    protected string $url = '/regions?limit=10';

    public function testRegion()
    {
        if (Regions::getDb()->query('SELECT * FROM regions LIMIT 1')) {
            $regionsResponse = $this->download($this->url);
            $crawler = new Crawler($regionsResponse->getHtml());
            $firstRegionPage = $crawler->filter('.table_main a');
            $this->assertTrue(
                $firstRegionPage->count() > 0,
                'Region page hasn\'t ads: ' . $regionsResponse->getUrl()
            );
            $firstRegionUrl = $firstRegionPage->attr('href');
            $response = $this->download($firstRegionUrl);
            $this->checkPhpErrors($response);
            $this->checkLinks($response);
        } else {
            $this->markTestIncomplete('Project hasn\'t regions');
        }
    }
}
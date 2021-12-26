<?php

namespace Test;

use Crunz\Infrastructure\Psr\Logger\EnabledLoggerDecorator;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Model\Categories;
use Symfony\Component\DomCrawler\Crawler;

class RegionsAds extends Web
{
    protected string $url = '/regions';

    public function testRegions()
    {
        $response = $this->download($this->url);
        $this->checkPhpErrors($response);
        $this->checkLinks($response);
    }
}
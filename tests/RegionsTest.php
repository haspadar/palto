<?php

namespace Test;

use Crunz\Infrastructure\Psr\Logger\EnabledLoggerDecorator;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Model\Categories;
use Palto\Model\Regions;
use Symfony\Component\DomCrawler\Crawler;

class RegionsTest extends Web
{
    protected string $url = '/regions?limit=10';

    public function testRegions()
    {
        if (Regions::getDb()->query('SELECT * FROM regions LIMIT 1')) {
            $response = $this->download($this->url);
            $this->checkPhpErrors($response);
            $this->checkLinks($response);
        } else {
            $this->markTestIncomplete('Project hasn\'t regions');
        }
    }
}
<?php

namespace Test;

use Crunz\Infrastructure\Psr\Logger\EnabledLoggerDecorator;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Model\Categories;
use Symfony\Component\DomCrawler\Crawler;

class CategoryTest extends Web
{
    protected string $url = '/categories';

    public function testCategory()
    {
        $categoriesResponse = $this->download($this->url);
        $crawler = new Crawler($categoriesResponse->getHtml());
        $firstCategoryUrl = $crawler->filter('.table_main a')->attr('href');
        $response = $this->download($firstCategoryUrl);
        $this->checkPhpErrors($response);
        $this->checkLinks($response);
    }
}
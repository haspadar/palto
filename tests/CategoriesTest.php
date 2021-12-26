<?php

namespace Test;

use Crunz\Infrastructure\Psr\Logger\EnabledLoggerDecorator;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Model\Categories;
use Symfony\Component\DomCrawler\Crawler;

class CategoriesTest extends Web
{
    protected string $url = '/categories';

    public function testCategories()
    {
        $response = $this->download($this->url);
        $this->checkPhpErrors($response);
        $this->checkLinks($response);
    }
}
<?php

namespace Test;

use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Model\Categories;
use Symfony\Component\DomCrawler\Crawler;

class IndexTest extends Web
{
    public function testDomainUrl()
    {
        $this->assertNotEmpty($this->getDomainUrl());
    }

    public function testPhpErrors()
    {
        $content = $this->download('/');
        $this->checkPhpErrors($content);
        $this->expectNotToPerformAssertions();
    }

    public function testListWithAds()
    {
        $content = $this->download('/');
        $categoryDocument = new Crawler($content);
        $links = $categoryDocument->filter('.table_main p a');
        $this->assertTrue($links->count() > 0);
    }
}
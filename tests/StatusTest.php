<?php

namespace Test;


class StatusTest extends Web
{
    protected string $url = '/status.php';

    public function testJson()
    {
        $jsonResponse = $this->download($this->url);
        $this->assertTrue($jsonResponse->isJson());
        $this->assertTrue(strlen($jsonResponse->getJson()->disk_mysql_used) > 0);
    }
}
<?php

namespace Test;

class IndexTest extends Web
{
    protected string $url = '/';

    public function testIndex()
    {
        $response = $this->download($this->url);
        $this->checkPhpErrors($response);
        $this->checkLinks($response);
    }
}
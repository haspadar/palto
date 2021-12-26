<?php

use PHPUnit\Framework\TestCase;

class WebTest extends TestCase
{
    public function __construct(string $name)
    {
        parent::__construct($name);
        ini_set('display_errors', true);
        ini_set('display_startup_errors', true);
    }

    protected function download(string $url)
    {
        return file_get_contents($url);
    }
}
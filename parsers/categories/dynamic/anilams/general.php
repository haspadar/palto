<?php

use Symfony\Component\DomCrawler\Crawler;
use Palto\Logger;

require realpath(dirname(__DIR__) . '/../../../') . '/vendor/autoload.php';

\Palto\Categories::safeAdd(['title' => 'Birds']);
#!/usr/bin/php
<?php

use Palto\Config;
use Palto\Sitemap;

require_once __DIR__ . '/autoload_require_composer.php';

$sitemap = new Sitemap(Config::getDomainUrl(), '/sitemaps/' . \Palto\Directory::getProjectName());
$sitemap->generate();